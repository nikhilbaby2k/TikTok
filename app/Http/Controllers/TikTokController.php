<?php
/**
 * Created by PhpStorm.
 * User: Nikhil
 * Date: 25-12-2015
 * Time: 12:50 PM
 */

namespace App\Http\Controllers;
use App\Repositories\MondovoRepository;
use PDO;
use App\Repositories\FirebirdRepository;

class TikTokController extends Controller
{

    protected $fb_repository;

    protected $mondovo_repository;

    public function __construct()
    {
        $this->fb_repository = \App::make(FirebirdRepository::class);
        $this->mondovo_repository = \App::make(MondovoRepository::class);

    }

    public function manage()
    {

        $result = $this->mondovo_repository->getData();
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