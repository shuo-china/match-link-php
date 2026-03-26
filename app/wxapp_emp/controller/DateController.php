<?php
namespace app\wxapp_emp\controller;

use app\wxapp_emp\model\Date;

class DateController extends BaseController
{
    public function pagination()
    {
        $map = [];
        $param = $this->request->param();

        $dates = Date::with([
            'male_member' => function ($query) {
                $query->append(['mobile', 'cover']);
            },
            'female_member' => function ($query) {
                $query->append(['mobile', 'cover']);
            }
        ])->where($map)->append(['man_to_woman_level_text', 'woman_to_man_level_text'])->order('id', 'desc')->paginate();
        $this->success(200, $dates);
    }

    public function list()
    {
        $map = [];
        $param = $this->request->param();

        if (!empty($param['date'])) {
            $map[] = ['date_time', '>=', $param['date'] . ' 00:00:00'];
            $map[] = ['date_time', '<=', $param['date'] . ' 23:59:59'];
        }

        $dates = Date::with([
            'male_member' => function ($query) {
                $query->append(['mobile', 'cover']);
            },
            'female_member' => function ($query) {
                $query->append(['mobile', 'cover']);
            }
        ])->where($map)->append(['man_to_woman_level_text', 'woman_to_man_level_text'])->select();
        $this->success(200, $dates);
    }

    public function create()
    {
        $post = $this->request->post();
        $post['employee_id'] = $this->request->employeeId;

        $track = new Date();
        $track->save($post);
        $this->success(201);
    }

    public function update()
    {
        $post = $this->request->post();

        Date::update($post);
        $this->success(201);
    }

    public function detail($id)
    {
        $track = Date::with([
            'male_member' => function ($query) {
                $query->append(['age', 'cover', 'industry_text', 'education_text', 'marital_status_text']);
            },
            'female_member' => function ($query) {
                $query->append(['age', 'cover', 'industry_text', 'education_text', 'marital_status_text']);
            }
        ])->where('id', $id)->append(['man_to_woman_level_text', 'woman_to_man_level_text'])->find();
        $this->success(200, $track);
    }

    public function delete()
    {
        $id = $this->request->param('id');
        $track = Date::find($id);
        $track->delete();
        $this->success(204);
    }
}