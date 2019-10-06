<?php
/**
 * Created by IntelliJ IDEA.
 * User: Andrey
 * Date: 04.10.2019
 * Time: 11:48
 */

namespace app\controllers;


use yii\rest\Controller;

class ApiController extends Controller
{
    protected $response = ['code' =>  200, 'status' => false];

    protected function sendResponse()
    {
        return $this->response;
    }

}