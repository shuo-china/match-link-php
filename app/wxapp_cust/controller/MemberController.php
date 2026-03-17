<?php

namespace app\wxapp_cust\controller;

use app\wxapp_cust\model\Member;

class MemberController extends BaseController
{
    public function randomDetail($exceptUids = [])
    {
        $mbrModel = new Member();
        $mbr = $mbrModel->findRandomDetail($exceptUids);
        $this->success(200, $mbr);
    }

    public function detail($uid)
    {
        $mbrModel = new Member();
        $mbr = $mbrModel->findDetailByUid($uid);
        $this->success(200, $mbr);
    }
}