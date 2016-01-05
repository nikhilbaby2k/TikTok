<?php
/**
 * Created by PhpStorm.
 * User: Nikhil
 * Date: 29-12-2015
 * Time: 08:21 PM
 */

namespace App\Services;

use App\Contracts\Repositories\DevRepositoryInterface;
use App\Contracts\Repositories\FirebirdRepositoryInterface;
use App\Contracts\Services\TikTokServiceInterface;
use Illuminate\Database\QueryException;

class TikTokService implements TikTokServiceInterface
{

    protected $fb_repository;

    protected $dev_repository;

    public function __construct(FirebirdRepositoryInterface $fb_repository, DevRepositoryInterface $dev_repository)
    {
        $this->fb_repository = $fb_repository;
        $this->dev_repository = $dev_repository;

    }


    /**
     * @return array|string
     */
    public function processAttendance()
    {
        $new_attendance_records = [];
        $inserted_punch_detail = [];
        $new_attendance_records = $this->checkForNewAttendanceRecord();

        if(!empty($new_attendance_records))
        {
            foreach($new_attendance_records as $new_attendance_records_item)
            {
                try{
                    $specific_punch_details = $this->fetchSpecificPunchDetails($new_attendance_records_item['TRG_ID']);
                    $inserted_punch_detail[] = $this->insertNewPunchDetailsIntoDevDb($specific_punch_details[0]);
                }
                catch (QueryException $e)
                {
                    continue;
                }
            }
            return $inserted_punch_detail;
        }
        else
            return -1;

    }


    public function checkForNewAttendanceRecord()
    {
        $query = "SELECT * FROM TRG_ID_BASE WHERE PROCESSED_STATUS = 0 OR PROCESSED_STATUS IS NULL ORDER BY TRG_ID DESC ;";
        return $this->fb_repository->executeGetQuery($query);
    }

    public function fetchSpecificPunchDetails($trg_id)
    {
        $query = "SELECT TRG_ID, TRG_EMP_ID, E.EMP_FULNAME FULNAME, TRG_DTTM, LUK_VALUE PUNCH_TYPE FROM PUNCHES_CUSTOM AS P JOIN EMPLOYEE AS E ON P.TRG_EMP_ID = E.EMP_ID WHERE TRG_ID = $trg_id ;";
        return $this->fb_repository->executeGetQuery($query);
    }

    public function insertNewPunchDetailsIntoDevDb($specific_punch_details)
    {
        $employee_details = $this->dev_repository->getEmployeeById($specific_punch_details['TRG_EMP_ID'])->first();
        //dd($employee_details);
        $insert_result = $this->dev_repository->insertAttendance($employee_details->emp_mx_id, $employee_details->emp_fullname, $specific_punch_details['TRG_ID'], $specific_punch_details['TRG_DTTM'], $specific_punch_details['PUNCH_TYPE'] );
        return $insert_result;
    }

    public function updateProcessedStatusInTrgIdBaseForRecords($trg_id)
    {
        $query = " UPDATE TRG_ID_BASE SET PROCESSED_STATUS = 1 WHERE TRG_ID = $trg_id ; ";
        return $this->fb_repository->executeGetQuery($query);
    }

   /* public function updateWorkTimeInDevDb($specific_punch_details)
    {
        $employee_details = $this->dev_repository->getEmployeeById($specific_punch_details['TRG_EMP_ID'])->first();
        $specific_attendance_detail = $this->dev_repository->getAttendanceForParticularDate($employee_details->emp_mx_id, $specific_punch_details['TRG_DTTM'])->get();
        dd($specific_attendance_detail);

    }*/

    public function prepareWorkTime()
    {
        $unprocessed_attendance_records = $this->dev_repository->getUnprocessedAttendanceRecords()->get();
        if(empty($unprocessed_attendance_records))
            return 1;



    }


    public function processAttendanceRecordsForWorkTime()
    {

        $distinct_dates = $this->dev_repository->getUnprocessedAttendanceRecordsDistinctDates()->get();
        $distinct_mx_ids = [];
        foreach( $distinct_dates as $distinct_date_item)
        {
            $distinct_mx_ids[$distinct_date_item->punch_trg_date] = $this->dev_repository->getUnprocessedAttendanceRecordsDistinctMxIdForParticularDate($distinct_date_item->punch_trg_date)->get();
        }
        $total_work_time = [];
        foreach($distinct_mx_ids as $punch_date => $emp_mx_id_detail)
        {
            foreach($emp_mx_id_detail as $key => $emp_mx_id_detail_item)
            {
                $emp_punch_detail = $this->dev_repository->getUnprocessedAttendanceRecordsForDateAndMxId($punch_date, $emp_mx_id_detail_item->emp_mx_id)->get();

                $total_work_time[$punch_date][$emp_mx_id_detail_item->emp_mx_id] = $this->processWorkTime($emp_mx_id_detail_item->emp_mx_id, $punch_date );
            }

        }

        foreach($total_work_time as $punch_date => $total_work_time_detail_per_day)
        {
            $this->makeWorkTimeEntriesForAllActiveEmployees($punch_date);

            foreach($total_work_time_detail_per_day as $emp_mx_id => $total_work_time_in_minutes )
            {
                $time_array = explode(',', $total_work_time_in_minutes);
                $total_work_time_in_minutes = $time_array[0];
                $total_out_time_in_minutes = $time_array[1];

                $this->dev_repository->updateWorkTime($punch_date, $emp_mx_id, $total_work_time_in_minutes, $total_out_time_in_minutes);
            }
            $this->dev_repository->updateAttendanceProcessedStatus($punch_date);

        }

        return $total_work_time;

    }

    public function processWorkTime($emp_mx_id, $punch_date)
    {
        $total_work_time = 0;
        $in_times = [];
        $out_times = [];
        $difference = 0;
        $total_out_time = 0;

        $total_work_time = \DB::select(" SELECT
                                         TIMESTAMPDIFF(MINUTE, MIN(punch_trg_datetime), MAX(punch_trg_datetime)) total_time
                                      FROM tik_tok_attendance
                                      WHERE emp_mx_id = '$emp_mx_id' AND
                                            punch_trg_date = '$punch_date' AND DATE_FORMAT(punch_trg_datetime , '%H') >= 8 ;
                                     ")[0]->total_time;

        //Latha and Alex
        if($emp_mx_id != 'MX057' || $emp_mx_id != 'MX076')
        {

            $all_in_time_array = \DB::select("SELECT punch_trg_datetime FROM tik_tok_attendance WHERE punch_trg_date = '$punch_date' AND emp_mx_id = '$emp_mx_id' AND punch_type = 'In' AND DATE_FORMAT(punch_trg_datetime , '%H') >= 8 ORDER BY punch_trg_datetime,punch_trg_id ASC ;");
            $all_out_time_array = \DB::select("SELECT punch_trg_datetime FROM tik_tok_attendance WHERE punch_trg_date = '$punch_date' AND emp_mx_id = '$emp_mx_id' AND punch_type = 'Out' AND DATE_FORMAT(punch_trg_datetime , '%H') >= 8 ORDER BY punch_trg_datetime,punch_trg_id ASC ;");

            foreach($all_in_time_array as $all_in_time_array_item)
            {
                $in_times[] = $all_in_time_array_item->punch_trg_datetime;
            }

            foreach($all_out_time_array as $all_out_time_array_item)
            {
                $out_times[] = $all_out_time_array_item->punch_trg_datetime;
            }
            $in_time_count = count($in_times);
            $out_time_count = count($out_times);

            if($in_time_count == $out_time_count)
            {
                foreach($out_times as $key => $out_time_value)
                {
                    if(!empty($in_times[$key+1]))
                    {
                        $num_temp = $key+1;
                        $difference = \DB::connection('mysql_dummy')->select(" SELECT TIMESTAMPDIFF(MINUTE, '$in_times[$num_temp]', '$out_time_value' ) difference ; ")[0];
                        $total_work_time += $difference->difference ;
                        $total_out_time += abs($difference->difference);
                    }
                }
            }
            else
            {

                if($in_time_count > $out_time_count)
                {
                    $evaluate_date = new \DateTime($in_times[$in_time_count-1]);
                    $num_temp = $in_time_count-1;
                    if (intval($evaluate_date->format('H'))  < 19 );
                    $difference = \DB::connection('mysql_dummy')->select(" SELECT TIMESTAMPDIFF(MINUTE, '$in_times[$num_temp]', '$punch_date 19:30:00' ) difference ; ")[0];
                    $total_work_time += $difference->difference;
                }

            }

        }

        return "$total_work_time,$total_out_time"  ;
    }

    public function makeWorkTimeEntriesForAllActiveEmployees($date = 'NOW()')
    {
        $work_time_entry_check = $this->dev_repository->checkWorkTimeForDate($date)->get();
        if(is_array($work_time_entry_check) && !empty($work_time_entry_check))
            return 1;

        $all_employees = $this->dev_repository->getAllActiveEmployees()->get();
        foreach ($all_employees as $employee_detail)
        {
            $this->dev_repository->insertWorkTime($employee_detail->emp_mx_id, $date);
        }

        return 2;

    }


}