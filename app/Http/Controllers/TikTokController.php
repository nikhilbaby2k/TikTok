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

class TikTokController extends Controller
{

    protected $fb_repository;

    protected $dev_repository;

    public function __construct(FirebirdRepositoryInterface $fb_repository, DevRepositoryInterface $dev_repository)
    {
        $this->fb_repository = $fb_repository;
        $this->dev_repository = $dev_repository;

    }

    public function manage()
    {

        //$result = $this->dev_repository->getData();
        //dd($result);


        $query = "select * from employee";
        $result = $this->fb_repository->executeGetQuery($query);

        $a = [];
        foreach($result as $value)
        {
            $a[] = $value['EMP_FULNAME'];
        }

        dd($a);
    }

}