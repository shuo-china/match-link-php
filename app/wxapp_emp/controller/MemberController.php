<?php
namespace app\wxapp_emp\controller;

use app\wxapp_emp\model\Member;

class MemberController extends BaseController
{
    public function pagination()
    {
        $map = [];
        $param = $this->request->param();

        if (!empty($param['name'])) {
            $map[] = ['name', 'like', "%{$param['name']}%"];
        }

        if (!empty($param['mobile'])) {
            $map[] = ['mobile', '=', $param['mobile']];
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

        $mbrs = Member::where($map)->append(['age', 'cover', 'industry_text', 'education_text', 'marital_status_text'])->paginate();

        $this->success(200, $mbrs);
    }

    public function create()
    {
        $post = $this->request->post();

        $has = Member::where('mobile', $post['mobile'])->find();
        if ($has) {
            $this->error(400, '该手机号已注册过');
        }

        $post['uid'] = uniqid();
        $post['employee_id'] = $this->request->employeeId;

        $model = new Member();
        $model->save($post);

        $this->success(201);
    }

    public function update()
    {
        $post = $this->request->post();

        Member::update($post);
        $this->success(201);
    }

    public function detail($id)
    {
        $mbr = Member::with(['employee'])->where('id', $id)->append(['age', 'albums'])->find();
        $this->success(200, $mbr);
    }

    public function delete()
    {
        $id = $this->request->param('id');
        $mbr = Member::find($id);
        $mbr->delete();
        $this->success(204);
    }
}