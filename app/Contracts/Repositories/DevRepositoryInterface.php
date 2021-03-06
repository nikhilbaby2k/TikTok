<?php
/**
 * Created by PhpStorm.
 * User: Nikhil
 * Date: 27-12-2015
 * Time: 11:37 PM
 */

namespace App\Contracts\Repositories;


interface DevRepositoryInterface
{

    public function getUnprocessedAttendanceRecords();

    public function insertAttendance($emp_mx_id, $emp_name, $punch_trg_id, $punch_datetime, $punch_type);

    public function updateAttendanceProcessedStatus($date, $status = 1);

    public function getAttendance($emp_mx_id);

    public function getAttendanceForParticularDate($emp_mx_id, $date = '');

    public function getEmployeeByMxId($emp_mx_id = '');

    public function getAllActiveEmployees();

    public function getEmployeeById($emp_id = '');

    public function insertEmployee($emp_id, $emp_mx_id, $emp_fullname, $emp_active, $emp_date_of_join);

    public function updateEmployeeActiveStatus($emp_mx_id, $emp_active = 'Ex');

    public function insertWorkTime($emp_mx_id, $work_date);

    public function getWorkTime($emp_mx_id, $work_date = '');

    public function checkWorkTimeForDate($work_date = 'NOW()');

    public function updateWorkTime($date, $emp_mx_id, $total_work_time_in_minutes, $total_out_time_in_minutes);

    public function getAbsentEmployees($work_date = '');

    public function getPresentEmployees($work_date = '');


}