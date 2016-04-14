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

    const TIME_ARRAY_FORMAT = [
            'Before 8:30' => [ 'from' => '08:00', 'to' => '08:30' ],
            '8:30 - 9:00' => [ 'from' => '08:30', 'to' => '09:00' ],
            '9:00 - 9:30' => [ 'from' => '09:00', 'to' => '09:30' ],
            '9:30 - 10:00' => [ 'from' => '09:30', 'to' => '10:00' ],
            '10:00 - 10:30' => [ 'from' => '10:00', 'to' => '10:30' ],
            '10:30 - 11:00' => [ 'from' => '10:30', 'to' => '11:00' ],
            'After 11:00' => [ 'from' => '11:00', 'to' => '' ],
            ];


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

        $this->view['in_time_statistics_data'] = $this->inTimeStatisticsData();
        return view('admin', $this->view);
    }

    public function manage()
    {
        set_time_limit(600);
        $processed_attendance_status = $this->processAttendance();
        $this->updateFirstInAndLastOut();
        /*//prepare Last Out and First In
        $this->updateFirstIn();
        $this->updateLastOut();

        $updated_work_time_details = $this->processStage_3();
        print_r("\n</br>Updated Wokr time Details: \n</br>");
        print_r($updated_work_time_details);*/
        return 1;
    }

    public function processAttendance()
    {
        set_time_limit(600);
        $this->processStage_1();
        return 'Processed';
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

    public function updateFirstInAndLastOut()
    {
        set_time_limit(600);
        $distinct_dates = \DB::table('tik_tok_attendance')
                            ->where('work_time_processed_status', 0)
                            ->select(\DB::raw('DISTINCT(punch_trg_date) '))->limit(5)->get();

        $emp_details = $this->dev_repository->getAllActiveEmployees()->get();
        $trg_ids_first_in = [];
        $trg_ids_last_out = [];
        $distinct_dates_temp = [];

        foreach($distinct_dates as $distinct_date)
        {
            $date = $distinct_date->punch_trg_date;
            $distinct_dates_temp[] = $date;

            foreach($emp_details as $emp_detail_item)
            {

                $temp_first_in_temp = \DB::table('tik_tok_attendance')
                    ->where('emp_mx_id', $emp_detail_item->emp_mx_id)
                    ->where('punch_trg_date', $date)
                    ->where('punch_type', 'In')
                    ->min('punch_trg_id');

                $temp_first_in = \DB::table('tik_tok_attendance')
                    ->where('emp_mx_id', $emp_detail_item->emp_mx_id)
                    ->where('punch_trg_date', $date)
                    ->where('punch_type', 'In')
                    ->min('punch_trg_id');

                if ( ! is_null($temp_first_in) )
                $trg_ids_first_in[] = $temp_first_in;

                $temp_last_out = \DB::table('tik_tok_attendance')
                    ->where('emp_mx_id', $emp_detail_item->emp_mx_id)
                    ->where('punch_trg_date', $date)
                    ->where('punch_type', 'Out')
                    ->max('punch_trg_id');

                if ( ! is_null($temp_last_out) )
                    $trg_ids_last_out[] = $temp_last_out;

                //$count_of_ins = $query->where('punch_type', 'In')->count();
                //$count_of_outs = $query->where('punch_type', 'Out')->count();

                /*\DB::table('tik_tok_work_time')->where('work_date', $date)->where('emp_mx_id', $emp_detail_item->emp_mx_id)
                    ->update([ 'number_ins' => $count_of_ins, 'number_outs' => $count_of_outs ]);*/

            }

        }

        \DB::table('tik_tok_attendance')
            ->whereIn('punch_trg_id', $trg_ids_first_in)
            ->update([
                'first_in' => 1
            ]);

        \DB::table('tik_tok_attendance')
            ->whereIn('punch_trg_id', $trg_ids_last_out)
            ->update([
                'last_out' => 1
            ]);

        \DB::table('tik_tok_attendance')
            ->whereIn('punch_trg_date', $distinct_dates_temp)
            ->update([
                'work_time_processed_status' => -1
            ]);



        return 1;
    }

    public function updateLastOut()
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


    public function liveAttendanceData()
    {
        $current_present_employees = count($this->dev_repository->getPresentEmployees()->get());
        return $current_present_employees;
    }

    public function inTimeStatisticsData()
    {

        foreach(self::TIME_ARRAY_FORMAT as $index_name => $index_from_to_values)
        {
            $data_for_pi_chart[$index_name] = $this->dev_repository->getAttandanceDataForTimeBetween($index_from_to_values['from'], $index_from_to_values['to'] )->count();
        }

        $sum_of_values = array_sum(array_values($data_for_pi_chart));


        foreach($data_for_pi_chart as $index => $integer_value)
        {
            $data_for_pi_chart[$index] =  ($sum_of_values != 0) ? number_format(($integer_value*100/$sum_of_values), 1 ) : 0;
        }

        return  $data_for_pi_chart ;
    }

}