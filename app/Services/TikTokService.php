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

                    if($specific_punch_details[0]['PUNCH_TYPE'] === 'Out')
                    {
                        //$this->updateWorkTimeInDevDb($specific_punch_details[0]);
                    }

                    //$this->updateProcessedStatusInTrgIdBaseForRecords($new_attendance_records_item['TRG_ID']);
                }
                catch (QueryException $e)
                {
                    continue;
                }

            }

            //$this->updateProcessedStatusInTrgIdBaseForRecords($new_attendance_records);

            return $inserted_punch_detail;

        }
        else
            return 'No New Attendance Records';

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

    public function updateWorkTimeInDevDb($specific_punch_details)
    {
        $employee_details = $this->dev_repository->getEmployeeById($specific_punch_details['TRG_EMP_ID'])->first();
        $specific_attendance_detail = $this->dev_repository->getAttendanceForParticularDate($employee_details->emp_mx_id, $specific_punch_details['TRG_DTTM'])->get();
        dd($specific_attendance_detail);

    }

}