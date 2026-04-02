<?php

namespace app\wxapp_emp\controller;

use app\wxapp_emp\model\Employee;
use app\wxapp_emp\model\UserWechatMini;

class EmployeeController extends BaseController
{
    protected $middleware = [
        'wxapp_employee_api_auth:guest' => [
            'only' => [
                'bindMobile'
            ],
        ],
        'wxapp_employee_api_auth:bound' => [
            'except' => [
                'bindMobile'
            ],
        ],
    ];

    public function bindMobile()
    {
        $code = $this->request->param('code');
        $mobileInfo = $this->getWechatMiniMobile($code);
        $purePhoneNumber = $mobileInfo['phone_info']['purePhoneNumber'];

        $userWxapp = UserWechatMini::where('id', '<>', $this->request->clientId)->where('bind_employee_mobile', '=', $purePhoneNumber)->find();
        if ($userWxapp) {
            $this->error(401, '该手机号已被其他用户绑定，请先解绑', 'BIND_MOBILE_EXISTS');
        }

        $employee = Employee::where('mobile', $purePhoneNumber)->find();
        if (!$employee) {
            $this->error(401, '该手机号未绑定员工账号，请先绑定', 'BIND_MOBILE_NOT_EXISTS');
        }

        UserWechatMini::where('id', $this->request->clientId)->update([
            'employee_id' => $employee->id,
            'bind_employee_mobile' => $purePhoneNumber,
        ]);

        $this->success(201);
    }

    public function currentEmployee()
    {
        $employee = Employee::where('id', $this->request->clientId)->find();

        $this->success(200, $employee);
    }

    public function unBindMobile()
    {
        UserWechatMini::where('id', $this->request->userWxappId)->update([
            'employee_id' => null,
            'bind_employee_mobile' => null,
        ]);

        $this->success(201);
    }

    public function pagination()
    {
        $mbrs = Employee::order('id', 'desc')->paginate();

        $this->success(200, $mbrs);
    }

    public function create()
    {
        $post = $this->request->post();

        if (!$this->request->employeeInfo['is_super']) {
            $this->error(400, '非超级管理员不能创建员工账号');
        }

        $has = Employee::where('mobile', $post['mobile'])->find();
        if ($has) {
            $this->error(400, '该手机号已注册过');
        }

        $model = new Employee();
        $model->save($post);

        $this->success(201);
    }

    public function update()
    {
        $post = $this->request->post();

        Employee::update($post);
        $this->success(201);
    }

    public function detail($id)
    {
        $mbr = Employee::where('id', $id)->find();
        $this->success(200, $mbr);
    }

    public function delete()
    {
        $id = $this->request->param('id');
        $mbr = Employee::find($id);
        $mbr->delete();
        $this->success(204);
    }

    /**
     * 获取手机号码
     */
    protected function getWechatMiniMobile($code)
    {
        $params = [
            'appid' => config('sys.wechat_mini_emp.appid'),
            'secret' => config('sys.wechat_mini_emp.appsecret'),
            'grant_type' => 'client_credential',
            'force_refresh' => false,
        ];

        $requestTokenUrl = 'https://api.weixin.qq.com/cgi-bin/stable_token';

        $access_token = $this->posturl($requestTokenUrl, $params);

        $access_token = $access_token['access_token'];

        $requestMobile = $this->posturl('https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token=' . $access_token, ['code' => $code]);

        if ($requestMobile['errcode'] != 0) {
            $this->error(401, $requestMobile['errmsg'], 'WECHAT_ERROR');
        }

        return $requestMobile;
    }

    protected function posturl($url, $data)
    {
        $data = json_encode($data);
        $headerArray = array("Content-type:application/json;charset='utf-8'", "Accept:application/json");
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return json_decode($output, true);
    }
}