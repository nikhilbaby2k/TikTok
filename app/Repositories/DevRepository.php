<?php
/**
 * Created by PhpStorm.
 * User: Nikhil
 * Date: 25-12-2015
 * Time: 08:37 PM
 */

namespace App\Repositories;

use App\Contracts\Repositories\DevRepositoryInterface;
use Carbon\Carbon;
use DB;

class DevRepository extends AbstractDbRepository implements DevRepositoryInterface
{

    /**
     * @var Carbon
    */
    protected $now;

    public function __construct(Carbon $carbon)
    {
        $this->now = $carbon->now();
    }


    public function getData()
    {
        return DB::connection('mysql')->table('tik_tok_attendance')->select();

    }

    public function getUnprocessedAttendanceRecords()
    {
        return DB::table('tik_tok_attendance')
                        ->where('work_time_processed_status', 0)
                        ->orderBy('punch_trg_date', 'ASC')
                        ->select([
                            DB::raw('distinct(punch_trg_date)')
                        ]);
    }

    public function getUnprocessedAttendanceRecordsDistinctDates()
    {
        return DB::table('tik_tok_attendance')
            ->where('work_time_processed_status', 0)
            ->orderBy('punch_trg_date', 'ASC')
            ->select([
                DB::raw('distinct(punch_trg_date)')
            ]);
    }

    public function getUnprocessedAttendanceRecordsDistinctMxIdForParticularDate($punch_trg_date)
    {
        return DB::table('tik_tok_attendance')
            ->where('work_time_processed_status', 0)
            ->where('punch_trg_date', $punch_trg_date)
            ->select([
                DB::raw('distinct(emp_mx_id)')
            ]);
    }

    public function getUnprocessedAttendanceRecordsForDateAndMxId($particular_date, $emp_mx_id)
    {
        return DB::table('tik_tok_attendance')
            ->where('work_time_processed_status', 0)
            ->where('punch_trg_date', $particular_date)
            ->where('emp_mx_id', $emp_mx_id)
            ->orderBy('punch_trg_id', 'ASC')
            ->select();
    }

    public function insertAttendance($emp_mx_id, $emp_name, $punch_trg_id, $punch_datetime, $punch_type)
    {
        return DB::connection('mysql')->table('tik_tok_attendance')
                    ->insertGetId([
                        'emp_mx_id' => $emp_mx_id,
                        'emp_name' => $emp_name,
                        'punch_trg_id' => $punch_trg_id,
                        'punch_trg_datetime' => $punch_datetime,
                        'punch_trg_date' => $punch_datetime,
                        'punch_type' => $punch_type
                    ]);
    }

    public function getAttendance($emp_mx_id)
    {
        return DB::connection('mysql')
            ->table('tik_tok_attendance')
            ->where('emp_mx_id', $emp_mx_id)
            ->select();
    }

    public function getAttendanceForParticularDate($emp_mx_id, $date = '')
    {
        $query = DB::connection('mysql')
                        ->table('tik_tok_attendance')
                        ->where('emp_mx_id', $emp_mx_id)
                        ->orderBy('punch_trg_id', 'ASC');
        if(!empty($date))
        {
            $date_new = date_create($date);
            $date = date_format($date_new, 'Y-m-d');
            $query = $query->where('punch_trg_date', $date)
                ->select();
        }
        else
            $query = $query->where('punch_trg_date', DB::raw('CURDATE()'))
                           ->select();

        return $query;
    }

    public function getEmployeeByMxId($emp_mx_id = '')
    {
        $query = DB::connection('mysql')
                    ->table('tik_tok_employee')
                    ->select();
        if(!empty($emp_mx_id))
            $query = $query->where('emp_mx_id', $emp_mx_id);

        return $query;
    }

    public function getEmployeeById($emp_id = '')
    {
        $query = DB::connection('mysql')
            ->table('tik_tok_employee')
            ->select();
        if(!empty($emp_id))
            $query = $query->where('emp_id', $emp_id);

        return $query;
    }

    public function getAllActiveEmployees()
    {
        $query = DB::connection('mysql')
            ->table('tik_tok_employee')
            ->where('emp_active', 'Active')
            ->select();

        return $query;
    }

    public function insertEmployee($emp_id, $emp_mx_id, $emp_fullname, $emp_active, $emp_date_of_join)
    {
        return DB::connection('mysql')
                    ->table('tik_tok_employee')
                    ->insertGetId([
                        'emp_id' => $emp_id,
                        'emp_mx_id' => $emp_mx_id,
                        'emp_fullname' => $emp_fullname,
                        'emp_date_of_join' => $emp_date_of_join
                    ]);
    }

    public function updateEmployeeActiveStatus($emp_mx_id, $emp_active = 'Ex')
    {
        return DB::connection('mysql')
                    ->table('tik_tok_employee')
                    ->where('emp_mx_id', $emp_mx_id)
                    ->update([ 'emp_active' => $emp_active ]);
    }

    public function insertWorkTime($emp_mx_id, $work_date)
    {
        if($work_date ==- 'NOW()')
            return DB::connection('mysql')
                        ->table('tik_tok_work_time')
                        ->insertGetId([
                            'emp_mx_id' => $emp_mx_id,
                            'work_date' => DB::raw('NOW()')
                    ]);

        else
            return DB::connection('mysql')
                ->table('tik_tok_work_time')
                ->insertGetId([
                    'emp_mx_id' => $emp_mx_id,
                    'work_date' => $work_date
                ]);
    }

    public function getWorkTime($emp_mx_id, $work_date = '')
    {
        $query = DB::connection('mysql')
                        ->table('tik_tok_work_time')
                        ->where('emp_mx_id', $emp_mx_id)
                        ->select();

        if(!empty($work_date))
            $query = $query->where('work_date', $work_date);

        return $query;
    }

    public function checkWorkTimeForDate($work_date = 'NOW()')
    {
        $query = DB::connection('mysql')
            ->table('tik_tok_work_time')
            ->select();

        if($work_date === 'NOW()')
            $query = $query->where('work_date', DB::raw('CURDATE()'));
        else
            $query = $query->where('work_date', $work_date);

        return $query;
    }

    public function getAbsentEmployees($work_date = '')
    {
        $query =  DB::connection('mysql')
                    ->table('tik_tok_work_time')
                    ->where('number_ins', 0)
                    ->select();

        if(!empty($work_date))
            $query = $query->where('work_date', $work_date);
        else
            $query = $query->where('work_date', DB::raw('NOW()'));

        return $query;
    }

    public function getPresentEmployees($work_date = '')
    {
        $query =  DB::connection('mysql')
            ->table('tik_tok_work_time')
            ->where('number_ins', '>', 0)
            ->select();

        if(!empty($work_date))
            $query = $query->where('work_date', $work_date);
        else
            $query = $query->where('work_date', DB::raw('NOW()'));

        return $query;
    }

    public function getActiveDevices()
    {
        return DB::table('tik_tok_punch_devices')
                    ->select();
    }




}