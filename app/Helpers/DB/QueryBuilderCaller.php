<?php

namespace App\Helpers\DB;

use App\Helpers\DB\DbTestingTrait;
use Illuminate\Support\Str;

/**
 * Class QueryBuilderCaller
 * Author: Sameer Panjwani
 * Created: 5th May, 2015
 *
 * Usage: For any Query Builder object you have defined as a function (it is compulsory that the functions are defined in camelCase),
 * you can initiate any of the following magic methods:
 * -- get{FunctionName} (Note: the case will change for your function name, so if you have a "users()" function, it will be "getUsers()")
 * -- countOf{FunctionName}
 * -- first{FunctionName}
 * -- ifExists{FunctionName} returns boolean
 *
 * @package App\Helpers
 */
class QueryBuilderCaller
{
    use DbTestingTrait;

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {

        return $this->makeAndCallMethod($method, $arguments);


    }

    /**
     * This is the main function that does the work of finding the method and calling it
     * @param $method
     * @param $arguments
     * @return mixed
     */
    private function makeAndCallMethod($method, $arguments)
    {
        $method_parts = explode('_', Str::snake($method));

        $valid_operators = ['get', 'count', 'first', 'if'];

        if (!$this->isValidCall($method_parts, $valid_operators)) {

            throw new \Exception("Invalid method $method called. ");

        }

        list($query_operator, $method) = $this->extractQueryOperatorAndMethod($method_parts);

        return $this->callMethod($method, $arguments, $query_operator);


    }

    /**
     * @param $query_builder_object
     * @return mixed
     */
    public function getResult($query_builder_object)
    {
        return $query_builder_object->get();
    }

    /**
     * @param $query_builder_object
     * @return mixed
     */
    public function firstResult($query_builder_object)
    {
        return $query_builder_object->first();
    }

    /**
     * @param $query_builder_object
     * @return mixed
     */
    public function countOfResult($query_builder_object)
    {
        //echo "inside countOfResult<br>";
        return $query_builder_object->count();
    }

    /**
     * @param $query_builder_object
     * @return bool
     */
    public function ifExistsResult($query_builder_object)
    {
        //echo "inside countOfResult<br>";
        if (count($query_builder_object->first()) == 1) return true; else return false;
    }


    /**
     * @param $chunks
     * @return array
     */
    private function extractQueryOperatorAndMethod($chunks)
    {
        $query_operator = $chunks[0];

        //It's optional to pass countOf instead of just count. Here we remove the "count" from the chunks array
        if ($query_operator == "count") {
            list($chunks, $query_operator) = $this->handleCountOfOperator($chunks);
        }

        if ($query_operator == "if") {
            list($chunks, $query_operator) = $this->handleIfOperator($chunks);
        }

        array_shift($chunks); //remove the get/first/l
        $method = Str::camel(implode('_', $chunks));
        return array($query_operator, $method);
    }

    /**
     * @param $method
     * @param $arguments
     * @param $query_operator
     * @return mixed
     */
    private function callMethod($method, $arguments, $query_operator)
    {
        if (method_exists($this, $method)) {
            $object_result = call_user_func_array(array($this, $method), $arguments);
            //echo "Going to call {$chunk_operator}Results function<br>";
            return call_user_func(array($this, $query_operator . "Result"), $object_result);
            //return $this->getResults($object_result);
        }

        throw new \Exception("The method '$method' does not exist!");
    }

    /**
     * @param $chunks
     * @return array
     */
    private function handleCountOfOperator($chunks)
    {
        if ($chunks[1] == "of") {
            array_shift($chunks);//we remove the "count" because we know that the next function will remove the "of"
        }
        $query_operator = "countOf";
        return array($chunks, $query_operator);
    }

    /**
     * @param $chunks
     * @return array
     */
    private function handleIfOperator($chunks)
    {
        if ($chunks[1] == "exists") {
            array_shift($chunks);//we remove the "if" because we know that the next function will remove the "of"
        }
        $query_operator = "ifExists";
        return array($chunks, $query_operator);
    }

    /**
     * @param $method_parts
     * @param $valid_operators
     * @return bool
     */
    private function isValidCall(array $method_parts, $valid_operators)
    {
        return in_array($method_parts[0], $valid_operators) && count($method_parts) >= 2;
    }


}
