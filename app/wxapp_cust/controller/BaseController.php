<?php

namespace app\wxapp_cust\controller;

use app\wxapp_cust\WxappSend;
use app\common\BaseController as CommonBaseController;

class BaseController extends CommonBaseController
{
    use WxappSend;

    protected $middleware = ['wxapp_user_api_auth:bound'];

    protected function initialize()
    {
        parent::initialize();
    }
}