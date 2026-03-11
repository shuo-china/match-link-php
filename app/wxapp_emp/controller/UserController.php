<?php
namespace app\wxapp_emp\controller;

use app\wxapp_emp\model\User;

class UserController extends BaseController
{
    public function pagination()
    {
        $users = User::where('mobile', '<>', null)->paginate();

        $this->success(200, $users);
    }
}