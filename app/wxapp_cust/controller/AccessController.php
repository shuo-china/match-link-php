<?php

namespace app\wxapp_cust\controller;

use app\common\Auth;
use app\wxapp_cust\model\User;
use app\wxapp_cust\model\UserWechatMini;

class AccessController extends BaseController
{
    protected $middleware = [];

    public function createAccessToken()
    {
        $oauth = \thirdconnect\Oauth::wechat_mini([
            'APPID' => config('sys.wechat_mini_cust.appid'),
            'APPSECRET' => config('sys.wechat_mini_cust.appsecret')
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

        if ($userWxapp->user_id) {
            $user = User::where('id', $userWxapp->user_id)->find();
            if (!$user->status) {
                $this->error(403, '该账号已被禁用', 'NOT_AUTH');
            }
            $accessToken = Auth::setAccessToken($user->id, [
                'level' => 'bound',
                'user_info' => $user->toArray(),
                'user_wxapp_info' => $userWxapp->toArray(),
            ]);
            $this->app->event->trigger('UserLoginAfter', $userWxapp);
            $this->app->event->trigger('UserLoginAfter', $user);
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
