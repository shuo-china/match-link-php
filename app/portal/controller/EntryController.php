<?php

namespace app\portal\controller;

use app\wxapp_emp\model\Member;

class EntryController extends BaseController
{
    public function index()
    {
        $this->success(200, 'hello world');
    }
}