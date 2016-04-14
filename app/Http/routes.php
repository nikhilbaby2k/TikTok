<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('manage', 'TikTokController@manage');

Route::get('admin', ['as'=>'admin_get', 'uses'=>'TikTokController@admin'] );

Route::post('live-attendance-data', ['as'=>'live_attendance_data_ajax', 'uses'=>'TikTokController@liveAttendanceData'] );
Route::post('in-time-statistics', ['as'=>'in_time_statistics_ajax', 'uses'=>'TikTokController@inTimeStatisticsData'] );

Route::get('update-in-out-time-status', ['as'=>'update_in_out_time_status', 'uses'=>'TikTokController@updateFirstInAndLastOut'] );
Route::get('update-attendance', ['as'=>'update_attendance', 'uses'=>'TikTokController@processAttendance'] );
