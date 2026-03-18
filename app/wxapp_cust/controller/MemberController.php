<?php

namespace app\wxapp_cust\controller;

use app\wxapp_cust\model\Member;

class MemberController extends BaseController
{
    public function detail($uid)
    {
        $mbrModel = new Member();
        $mbr = $mbrModel->findDetailByUid($uid);
        $this->success(200, $mbr);
    }

    public function pagination()
    {
        $exceptUids = $this->request->post('exceptUids', []);

        $map = [
            ['albumKeys', '<>', '[]'],
        ];

        $param = $this->request->param();

        if (!empty($param['gender'])) {
            $map[] = ['gender', '=', $param['gender']];
        }

        if (!empty($param['age'])) {
            if ($param['age'][0] !== 'null') {
                $map[] = ['birthYear', '<=', date('Y') - intval($param['age'][0])];
            }

            if ($param['age'][1] !== 'null') {
                $map[] = ['birthYear', '>=', date('Y') - intval($param['age'][1])];
            }
        }

        if (!empty($param['height'])) {
            if ($param['height'][0] !== 'null') {
                $map[] = ['height', '>=', intval($param['height'][0])];
            }

            if ($param['height'][1] !== 'null') {
                $map[] = ['height', '<=', intval($param['height'][1])];
            }
        }

        if (!empty($param['maritalStatus'])) {
            $map[] = ['maritalStatus', 'in', $param['maritalStatus']];
        }

        if (!empty($param['education'])) {
            $map[] = ['education', 'in', $param['education']];
        }

        $mbr = new Member;
        $query = $mbr->where($map)->append($mbr->appendFields);

        if (!empty($exceptUids)) {
            $exceptUidSql = implode(',', array_map(function ($uid) {
                return "'" . addslashes((string) $uid) . "'";
            }, $exceptUids));
            $query->orderRaw("CASE WHEN uid IN ({$exceptUidSql}) THEN 1 ELSE 0 END ASC, RAND()");
        } else {
            $query->orderRaw('RAND()');
        }

        $mbrs = $query->paginate();
        $this->success(200, $mbrs);
    }
}