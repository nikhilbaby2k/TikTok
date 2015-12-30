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

    public function __construct(FirebirdRepositoryInterface $fb_repository, DevRepositoryInterface $dev_repository, TikTokServiceInterface $tik_tok_service)
    {
        $this->fb_repository = $fb_repository;
        $this->dev_repository = $dev_repository;
        $this->tik_tok_service = $tik_tok_service;

    }

    public function manage()
    {
        /*$count = [];
        $dev_details = $this->dev_repository->getData()->get();
        foreach($dev_details as $dev_detail_item)
        {
            $trg_id = $dev_detail_item->punch_trg_id;
            $count[] = $this->fb_repository->executeUpdateQuery("UPDATE TRG_ID_BASE SET PROCESSED_STATUS = 1 WHERE TRG_ID = $trg_id ; ");
        }

        dd($count);*/

        /*$query = "SELECT * FROM TRG_ID_BASE WHERE PROCESSED_STATUS = 0 OR PROCESSED_STATUS IS NULL ORDER BY TRG_ID DESC ;";
        $all_att_details =  $this->fb_repository->executeGetQuery($query);
        dd($all_att_details);*/

        $result = $this->tik_tok_service->processAttendance();
        dd($result);

        $result = $this->dev_repository->getData();
        dd($result);


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