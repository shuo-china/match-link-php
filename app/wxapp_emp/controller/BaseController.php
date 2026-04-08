<?php

namespace app\wxapp_emp\controller;

use app\wxapp_emp\WxappSend;
use app\common\BaseController as CommonBaseController;

class BaseController extends CommonBaseController
{
    use WxappSend;

    protected $middleware = ['wxapp_employee_api_auth:bound'];

    protected function initialize()
    {
        parent::initialize();
    }
}