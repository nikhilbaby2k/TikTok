<?php
/**
 * Created by PhpStorm.
 * User: Nikhil
 * Date: 03-01-2016
 * Time: 11:11 PM
 */

namespace App\Services;

use App\Contracts\Repositories\DevRepositoryInterface;
use App\Contracts\Services\TikTokAdminDisplayServiceInterface;

class TikTokAdminDisplayService implements TikTokAdminDisplayServiceInterface
{

    protected $dev_repository;

    public function __construct(DevRepositoryInterface $dev_repository)
    {
        $this->dev_repository = $dev_repository;
    }

    public function getViewData()
    {
        $this->view['registered_employee_count'] = $this->getRegisteredEmployeeCount();
        $this->view['employee_present_today'] = $this->getEmployeePresentTodayCount();
        $this->view['active_devices'] = $this->getActiveDevicesCount();
        return $this->view;

    }

    public function getRegisteredEmployeeCount()
    {
        $employee_count = count($this->dev_repository->getAllActiveEmployees()->get());
        return $employee_count;
    }

    public function getEmployeePresentTodayCount()
    {
        $employee_present_today_count = count($this->dev_repository->getPresentEmployees()->get());
        return $employee_present_today_count;
    }

    public function getActiveDevicesCount()
    {
        $active_devices_count = count($this->dev_repository->getActiveDevices()->get());
        return $active_devices_count;
    }



}