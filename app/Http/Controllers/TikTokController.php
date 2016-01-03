<?php
/**
 * Created by PhpStorm.
 * User: Nikhil
 * Date: 25-12-2015
 * Time: 12:50 PM
 */

namespace App\Http\Controllers;
use App\Contracts\Repositories\DevRepositoryInterface;
use App\Contracts\Repositories\FirebirdRepositoryInterface;
use App\Contracts\Services\TikTokAdminDisplayServiceInterface;
use App\Contracts\Services\TikTokServiceInterface;

class TikTokController extends Controller
{

    /**
     * @var FirebirdRepositoryInterface
    */
    protected $fb_repository;

    /**
     * @var DevRepositoryInterface
     */
    protected $dev_repository;

    /**
     * @var TikTokServiceInterface
     */
    protected $tik_tok_service;

    /**
     * @var TikTokAdminDisplayServiceInterface
     */
    protected $admin_display_service;


    public function __construct(FirebirdRepositoryInterface $fb_repository, DevRepositoryInterface $dev_repository, TikTokServiceInterface $tik_tok_service, TikTokAdminDisplayServiceInterface $admin_display_service)
    {
        $this->fb_repository = $fb_repository;
        $this->dev_repository = $dev_repository;
        $this->tik_tok_service = $tik_tok_service;
        $this->admin_display_service = $admin_display_service;
    }

    public function admin()
    {
        $this->view = $this->admin_display_service->getViewData();
        return view('admin', $this->view);
    }

    public function manage()
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

        dd($total_work_time);

        return $this->processAttendance();

    }

    public function processWorkTime($emp_mx_id, $punch_date)
    {
        $total_work_time = 0;
        $in_times = [];
        $out_times = [];
        $difference = 0;


        $total_work_time = \DB::select(" SELECT
                                         TIMESTAMPDIFF(MINUTE, MIN(punch_trg_datetime), MAX(punch_trg_datetime)) total_time
                                      FROM tik_tok_attendance
                                      WHERE emp_mx_id = '$emp_mx_id' AND
                                            punch_trg_date = '$punch_date' AND DATE_FORMAT(punch_trg_datetime , '%H') >= 8 ;
                                     ")[0]->total_time;

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

        $num_hours = intval($total_work_time/60) < 10 ? ('0' . intval($total_work_time/60) )  : intval($total_work_time/60) ;
        $num_minutes = ($total_work_time%60) < 10 ? ('0' . $total_work_time%60) : $total_work_time%60;

        return "$num_hours:$num_minutes:00"  ;
    }



    public function processAttendance()
    {
        $inserted_punch_detail = $this->processStage_1();
        if(is_array($inserted_punch_detail))
        {
            $processed_status = $this->processStage_2($inserted_punch_detail);
            if(!is_array($processed_status))
                return 'All '. count($inserted_punch_detail) .' Processed & Good';
            else
                return "Only ". count($processed_status) . '/' . count($inserted_punch_detail) . " records are processed. Remaining " . count($inserted_punch_detail) - count($processed_status) . " records failed.";
        }
        return 'Nothing new process';
    }


    /**
     * Fetch un-processed Attendance Records From Local-Db and insert into Dev-Db; If available;
     * @return processed/inserted records or
     *         -1;
     */
    public function processStage_1()
    {
        $inserted_punch_detail = $this->tik_tok_service->processAttendance();
        return $inserted_punch_detail;
    }

    /**
     * For each of inserted punch data into Dev-Db, update processed status[1] in Local-Db
     *
     * @param $inserted_punch_detail
     * @return array|int
     */
    public function processStage_2($inserted_punch_detail)
    {
        $processed_count = [];

        foreach($inserted_punch_detail as $inserted_punch_detail_item)
        {
            $processed_count = $this->tik_tok_service->updateProcessedStatusInTrgIdBaseForRecords($inserted_punch_detail_item->punch_trg_id);
        }

        if(count($inserted_punch_detail) == count($processed_count))
            return 1;

        return $processed_count;
    }

    public function processStage_3()
    {
        return $this->prepareWorkTime();
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