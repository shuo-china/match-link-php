<?php
namespace app\wxapp_emp\controller;

use app\wxapp_emp\model\Member;

class MemberController extends BaseController
{
    protected $middleware = [
        'wxapp_employee_api_auth:bound' => [
            'except' => [
                'roughDetail'
            ],
        ]
    ];

    public function roughDetail($id)
    {
        $mbr = Member::where('id', $id)
            ->append(['age', 'albums', 'job_text', 'education_text', 'marital_status_text', 'familys_text', 'childrens_text'])
            ->field('name,gender,birthYear,albumKeys,industry,occupation,education,annualIncome,hasHouse,hasVehicle,permanentAddress,maritalStatus,height,childrens,familys')->find();
        $this->success(200, $mbr);
    }

    public function options()
    {
        $param = $this->request->param();
        $map = [];

        if (!empty($param['gender'])) {
            $map[] = ['gender', '=', $param['gender']];
        }

        $mbrs = Member::field('id,name,mobile,albumKeys')->append(['cover'])->where($map)->select();
        $result = [];
        foreach ($mbrs as $mbr) {
            $result[] = [
                'id' => $mbr['id'],
                'name' => $mbr['name'],
                'mobile' => $mbr['mobile'],
                'cover' => empty($mbr['cover']) ? null : $mbr['cover']['path'],
            ];
        }
        $this->success(200, $result);
    }

    public function pagination()
    {
        $map = [];
        $param = $this->request->param();

        if (!empty($param['gender'])) {
            $map[] = ['gender', 'in', $param['gender']];
        }

        if (isset($param['hasHouse']) && $param['hasHouse'] !== '') {
            $map[] = ['hasHouse', 'in', $param['hasHouse']];
        }

        if (isset($param['hasVehicle']) && $param['hasVehicle'] !== '') {
            $map[] = ['hasVehicle', 'in', $param['hasVehicle']];
        }

        if (isset($param['hasChildren']) && $param['hasChildren'] !== '') {
            $map[] = ['hasChildren', 'in', $param['hasChildren']];
        }

        if (!empty($param['industry'])) {
            $map[] = ['industry', 'in', $param['industry']];
        }

        if (!empty($param['vipLevel'])) {
            $map[] = ['vipLevel', 'in', $param['vipLevel']];
        }

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

        $mbrs = Member::where($map)->append(['age', 'cover', 'industry_text', 'education_text', 'marital_status_text'])
            ->order('id', 'desc')->paginate();

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
        $mbr = Member::with(['employee'])->where('id', $id)
            ->append(['age', 'albums', 'gender_text', 'industry_text', 'vip_level_text', 'education_text', 'marital_status_text'])->find();
        $this->success(200, $mbr);
    }

    public function delete()
    {
        $id = $this->request->param('id');
        Member::destroy($id);
        $this->success(204);
    }
}