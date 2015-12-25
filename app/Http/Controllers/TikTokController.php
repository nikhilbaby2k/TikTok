<?php
/**
 * Created by PhpStorm.
 * User: Nikhil
 * Date: 25-12-2015
 * Time: 12:50 PM
 */

namespace App\Http\Controllers;
use App\Repositories\DevRepository;
use App\Repositories\FirebirdRepository;

class TikTokController extends Controller
{

    protected $fb_repository;

    protected $dev_repository;

    public function __construct()
    {
        $this->fb_repository = \App::make(FirebirdRepository::class);
        $this->dev_repository = \App::make(DevRepository::class);

    }

    public function manage()
    {

        $result = $this->dev_repository->getData();
        dd($result);


        $query = "select * from employee";
        $result = $this->fb_repository->executeGetQuery($query);
//dd($result);
        $a = [];
        foreach($result as $value)
        {
            $a[] = $value['EMP_FULNAME'];
        }

        dd($a);
    }

}