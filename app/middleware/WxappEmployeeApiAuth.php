<?php

declare(strict_types=1);

namespace app\middleware;

class WxappEmployeeApiAuth extends ApiAuth
{
    protected $levels = [
        'guest' => 1,
        'bound' => 2,
    ];

    /**
     * 处理请求
     * @param \think\Request $request
     * @param \Closure $next
     */
    public function handle($request, \Closure $next, $level = 'guest')
    {
        $this->authenticateRequest($request);

        if ($this->levels[$level] > $this->levels[$request->clientInfo['level']]) {
            $this->error(403, '没有权限', 'NO_AUTH');
        }

        if ($this->levels[$request->clientInfo['level']] >= $this->levels['guest']) {
            $request->userWxappId = $request->clientInfo['user_wxapp_info']['id'];
            $request->userWxappInfo = $request->clientInfo['user_wxapp_info'];
        }

        if ($this->levels[$request->clientInfo['level']] >= $this->levels['bound']) {
            $request->employeeId = $request->clientInfo['employee_info']['id'];
            $request->employeeInfo = $request->clientInfo['employee_info'];
        }

        return $next($request);
    }
}