<?php
/**
 * Created by PhpStorm.
 * User: Nikhil
 * Date: 25-12-2015
 * Time: 08:37 PM
 */

namespace App\Repositories;

use DB;
use PDO;

class FirebirdRepository extends AbstractDbRepository
{

    protected $connection_string = "firebird:dbname=localhost:C:\\Users\\Nikhil\\Desktop\\Proj\\ITAS.FDB;host=localhost";

    protected $firebird_handle;


    public function __construct()
    {
        $this->firebird_handle = $this->getFirebirdHandle();
    }

    public function getFirebirdHandle()
    {
        $firebird_handle = new PDO($this->connection_string, "SYSDBA", "masterkey");
        return $firebird_handle;
    }

    public function executeGetQuery($query)
    {
        $result = $this->firebird_handle->query($query);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

}