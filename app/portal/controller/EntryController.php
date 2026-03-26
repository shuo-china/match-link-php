<?php

namespace app\portal\controller;

use app\wxapp_emp\model\Member;

class EntryController extends BaseController
{
    public function index()
    {
        $members = \think\facade\Db::table('kr_member_before')->select()->toArray();

        for ($i = 0; $i < count($members); $i++) {
            $item = $members[$i];

            Member::where('uid', $item['uid'])->update([
                'vipLevel' => $item['vipLevel'],
            ]);
        }
    }
}