<?php
/**
 * Created by PhpStorm.
 * User: Nikhil
 * Date: 25-12-2015
 * Time: 08:37 PM
 */

namespace App\Repositories;

use DB;

class DevRepository extends AbstractDbRepository
{

    public function __construct()
    {

    }


    public function getData()
    {
        return DB::connection('mysql')->table('tik_tok_employee')->select()->get();

    }

    public function insertAttendance($emp_mx_id, $emp_name, $punch_trg_id, $punch_datetime, $punch_type)
    {
        return DB::connection('mysql')->table('attendance')
                    ->insertGetId([
                        'emp_mx_id' => $emp_mx_id,
                        'emp_name' => $emp_name,
                        'punch_trg_id' => $punch_trg_id,
                        'punch_datetime' => $punch_datetime,
                        'punch_date' => $punch_datetime,
                        'punch_type' => $punch_type
                    ]);
    }

    public function getAttendance($emp_mx_id)
    {
        return DB::connection('mysql')
            ->table('attendance')
            ->where('emp_mx_id', $emp_mx_id)
            ->select();
    }

    public function getEmployee($emp_mx_id = '')
    {
        $query = DB::connection('mysql')
                    ->table('employee')
                    ->select();
        if(!empty($emp_mx_id))
            $query = $query->where('emp_mx_id', $emp_mx_id);

        return $query;
    }

    public function insertEmployee($emp_id, $emp_mx_id, $emp_fullname, $emp_active, $emp_date_of_join)
    {
        return DB::connection('mysql')
                    ->table('employee')
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
                    ->table('employee')
                    ->where('emp_mx_id', $emp_mx_id)
                    ->update([ 'emp_active' => $emp_active ]);
    }

    public function insertWorkTime($emp_mx_id, $work_date)
    {
        return DB::connection('mysql')
                    ->table('work_time')
                    ->insertGetId([
                        'emp_mx_id' => $emp_mx_id,
                        'work_date' => DB::raw('NOW()')
                    ]);
    }

    public function getWorkTime($emp_mx_id, $work_date = '')
    {
        $query = DB::connection('mysql')
                        ->table('work_time')
                        ->where('emp_mx_id', $emp_mx_id)
                        ->select();

        if(!empty($work_date))
            $query = $query->where('work_date', $work_date);

        return $query;
    }

    public function getAbsentEmployees($work_date = '')
    {
        $query =  DB::connection('mysql')
                    ->table('work_time')
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
            ->table('work_time')
            ->where('number_ins', '>', 0)
            ->select();

        if(!empty($work_date))
            $query = $query->where('work_date', $work_date);
        else
            $query = $query->where('work_date', DB::raw('NOW()'));

        return $query;
    }




}