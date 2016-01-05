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

        $processed_attendance_status = $this->processAttendance();
        print_r("\n</br>Processed Attendance Status: \n</br>". $processed_attendance_status);
        //prepare Last Out and First In
        $this->updateFirstIn();
        $this->updateLastOut();

        $updated_work_time_details = $this->processStage_3();
        print_r("\n</br>Updated Wokr time Details: \n</br>");
        print_r($updated_work_time_details);
        return 1;
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
        return 'Nothing new to process';
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
        return $this->tik_tok_service->processAttendanceRecordsForWorkTime();
    }

    public function updateFirstIn()
    {
        $distinct_dates = \DB::table('tik_tok_attendance')
                            ->where('work_time_processed_status', 0)
                            ->select(\DB::raw('DISTINCT(punch_trg_date) '))->get();

        $emp_details = $this->dev_repository->getAllActiveEmployees()->get();

        foreach($distinct_dates as $distinct_date)
        {
            $date = $distinct_date->punch_trg_date;

            foreach($emp_details as $emp_detail_item)
            {
                $query = \DB::table('tik_tok_attendance')
                    ->where('emp_mx_id', $emp_detail_item->emp_mx_id)
                    ->where('punch_trg_date', $date);

                $punch_trg_id = $query->orderBy('punch_trg_datetime', 'ASC')->where('punch_type', 'In')->first();

                if(empty($punch_trg_id))
                    continue;

                $query->where('punch_trg_id', $punch_trg_id->punch_trg_id)
                    ->where('punch_type', 'In')
                    ->update(['first_in' => 1 ]);
            }

        }
        return 1;
    }

    public function updateLastOut()
    {
        $distinct_dates = \DB::table('tik_tok_attendance')
            ->where('work_time_processed_status', 1)
            ->select(\DB::raw('DISTINCT(punch_trg_date) '))->get();

        $emp_details = $this->dev_repository->getAllActiveEmployees()->get();

        foreach($distinct_dates as $distinct_date)
        {
            $date = $distinct_date->punch_trg_date;

            foreach($emp_details as $emp_detail_item)
            {
                $query = \DB::table('tik_tok_attendance')
                    ->where('emp_mx_id', $emp_detail_item->emp_mx_id)
                    ->where('punch_trg_date', $date);

                $punch_trg_id = $query->orderBy('punch_trg_datetime', 'DESC')->where('punch_type', 'Out')->first();

                if(empty($punch_trg_id))
                    continue;

                $query->update([ 'last_out' => 0 ]);

                $query->where('punch_trg_id', $punch_trg_id->punch_trg_id)
                    ->where('punch_type', 'Out')
                    ->update(['last_out' => 1 ]);
            }

        }
        return 1;
    }

}