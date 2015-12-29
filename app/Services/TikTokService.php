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

class TikTokService
{

    protected $fb_repository;

    protected $dev_repository;

    public function __construct(FirebirdRepositoryInterface $fb_repository, DevRepositoryInterface $dev_repository)
    {
        $this->fb_repository = $fb_repository;
        $this->dev_repository = $dev_repository;

    }


    public function processAttendance()
    {
        $new_attendance_records = [];
        $inserted_punch_detail = [];
        $new_attendance_records = $this->checkForNewAttendanceRecord();

        if(!empty($new_attendance_records))
        {
            foreach($new_attendance_records as $new_attendance_records_item)
            {
                $specific_punch_details = $this->fetchSpecificPunchDetails($new_attendance_records_item->trg_id);
                $inserted_punch_detail[] = $this->insertNewPunchDetailsIntoDevDb($specific_punch_details);

                if($specific_punch_details->punch_type == 'Out')
                    $this->updateWorkTimeInDevDb($specific_punch_details);
            }

            $this->updateProcessedStatusInTrgIdBaseForRecords($new_attendance_records);

            return $inserted_punch_detail;

        }
        else
            return 'No New Attendance Records';

    }


    public function checkForNewAttendanceRecord()
    {
        $query = "SELECT * FROM TRG_ID_BASE WHERE PROCESSED_STATUS = 0;";
        return $this->fb_repository->executeGetQuery($query);
    }

    public function fetchSpecificPunchDetails($trg_id)
    {
        $query = "SELECT TRG_ID, TRG_EMP_ID, E.EMP_FULNAME FULNAME, TRG_DTTM, LUK_VALUE PUNCH_TYPE FROM PUNCHES_CUSTOM AS P JOIN EMPLOYEE AS E ON P.TRG_EMP_ID = E.EMP_ID WHERE TRG_ID = $trg_id ;";
        return $this->fb_repository->executeGetQuery($query);
    }

    public function insertNewPunchDetailsIntoDevDb($specific_punch_details)
    {

    }

}