<?php
/**
 * Created by PhpStorm.
 * User: Nikhil
 * Date: 25-12-2015
 * Time: 08:37 PM
 */

namespace App\Repositories;

use App\Contracts\Repositories\FirebirdRepositoryInterface;
use PDO;

class FirebirdRepository extends AbstractDbRepository implements FirebirdRepositoryInterface
{

    protected $connection_string = 'firebird:dbname=localhost:C:\\Program Files\\Attend HRM\\Data\\ITAS.FDB;host=localhost';

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

    public function executeInsertQuery($query)
    {
        $count = $this->firebird_handle->exec($query);
        $insert_id = $this->firebird_handle->lastInsertId();
        return $insert_id;
    }

    public function executeUpdateQuery($query)
    {
        $count = $this->firebird_handle->exec($query);
        return $count;
    }



}