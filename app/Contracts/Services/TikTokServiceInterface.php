<?php
/**
 * Created by PhpStorm.
 * User: Nikhil
 * Date: 30-12-2015
 * Time: 10:34 AM
 */

namespace App\Contracts\Services;


interface TikTokServiceInterface
{
    /**
     * @return array|string
     */
    public function processAttendance();

    public function processAttendanceRecordsForWorkTime();

}