<?php

namespace app\wxapp_cust\controller;

use app\wxapp_cust\model\Favorite;
use app\wxapp_cust\model\Member;

class MemberController extends BaseController
{
    public function detail($id)
    {
        $mbrModel = new Member();
        $mbr = $mbrModel->findDetailById($id);
        $isFavorite = Favorite::where('user_id', '=', $this->request->userId)
            ->where('member_id', '=', $id)
            ->find();
        $mbr['is_favorite'] = $isFavorite ? true : false;
        $this->success(200, $mbr);
    }

    public function pagination()
    {
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
        $randSeed = (string) $this->request->param('randSeed', date('Ymd'));
        $randSeed = addslashes($randSeed);
        $randOrderExpr = "CRC32(CONCAT(id, '-', '{$randSeed}'))";

        $query->orderRaw("{$randOrderExpr} ASC, id ASC");

        $mbrs = $query->paginate();
        $this->success(200, $mbrs);
    }
}