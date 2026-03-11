<?php
// 中间件配置
return [
    // 别名或分组
    'alias' => [
        'api_auth' => app\middleware\ApiAuth::class,
        'wxapp_employee_api_auth' => app\middleware\WxappEmployeeApiAuth::class,
        'wxapp_user_api_auth' => app\middleware\WxappUserApiAuth::class,
    ],
    // 优先级设置，此数组中的中间件会按照数组中的顺序优先执行
    'priority' => [],
];
