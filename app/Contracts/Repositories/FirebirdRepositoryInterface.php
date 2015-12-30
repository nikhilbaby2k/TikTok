<?php
/**
 * Created by PhpStorm.
 * User: Nikhil
 * Date: 27-12-2015
 * Time: 11:36 PM
 */

namespace App\Contracts\Repositories;


interface FirebirdRepositoryInterface
{

    public function getFirebirdHandle();

    public function executeGetQuery($query);

    public function executeInsertQuery($query);

    public function executeUpdateQuery($query);


}