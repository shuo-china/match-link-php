<?php

namespace app\wxapp_emp\controller;

use app\common\Auth;
use app\wxapp_emp\model\Employee;
use app\wxapp_emp\model\UserWechatMini;

class AccessController extends BaseController
{
    protected $middleware = [];

    public function createAccessToken()
    {
        $oauth = \thirdconnect\Oauth::wechat_mini([
            'APPID' => config('sys.wechat_mini_emp.appid'),
            'APPSECRET' => config('sys.wechat_mini_emp.appsecret')
        ]);
        $result = $oauth->getToken();
        $userWxapp = UserWechatMini::where('wechat_mini_openid', $result['openid'])->find();

        if (empty($userWxapp)) {
            UserWechatMini::create([
                'wechat_mini_openid' => $result['openid'],
                'wechat_unionid' => isset($result['unionid']) ? $result['unionid'] : '',
            ]);
            $userWxapp = UserWechatMini::where('wechat_mini_openid', $result['openid'])->find();
        }

        if ($userWxapp->employee_id) {
            $employee = Employee::where('id', $userWxapp->employee_id)->find();
            if (!$employee->status) {
                $this->error(403, '该账号已被禁用', 'NOT_AUTH');
            }
            $accessToken = Auth::setAccessToken($employee->id, [
                'level' => 'bound',
                'employee_info' => $employee->toArray(),
                'user_wxapp_info' => $userWxapp->toArray(),
            ]);
            $this->app->event->trigger('UserLoginAfter', $userWxapp);
            $this->app->event->trigger('UserLoginAfter', $employee);
            $this->success(201, [
                'level' => 'bound',
                'token_info' => $accessToken,
            ]);
        } else {
            $accessToken = Auth::setAccessToken($userWxapp->id, [
                'level' => 'guest',
                'user_wxapp_info' => $userWxapp->toArray(),
            ]);
            $this->app->event->trigger('UserLoginAfter', $userWxapp);
            $this->success(201, [
                'level' => 'guest',
                'token_info' => $accessToken,
            ]);
        }

    }

}
