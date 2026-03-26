<?php
namespace app\wxapp_emp\controller;

use app\wxapp_emp\model\Track;
use app\wxapp_emp\model\Member;

class TrackController extends BaseController
{
    public function pagination()
    {
        $map = [];
        $param = $this->request->param();

        if (!empty($param['intention'])) {
            $map[] = ['intention', 'in', $param['intention']];
        }

        if (!empty($param['name'])) {
            $memberIds = Member::whereLike('name', '%' . $param['name'] . '%')->column('id');
            $map[] = ['member_id', 'in', empty($memberIds) ? [0] : $memberIds];
        }

        if (!empty($param['mobile'])) {
            $memberIds = Member::whereLike('mobile', '%' . $param['mobile'] . '%')->column('id');
            $map[] = ['member_id', 'in', empty($memberIds) ? [0] : $memberIds];
        }

        $tracks = Track::with([
            'member' => function ($query) {
                $query->append(['age', 'cover', 'job_text']);
            },
            'employee'
        ])->where($map)->append(['intention_text'])->order('id', 'desc')->paginate();
        $this->success(200, $tracks);
    }

    public function create()
    {
        $post = $this->request->post();
        $post['employee_id'] = $this->request->employeeId;

        $track = new Track();
        $track->save($post);
        $this->success(201);
    }

    public function update()
    {
        $post = $this->request->post();

        Track::update($post);
        $this->success(201);
    }

    public function detail($id)
    {
        $track = Track::with(['member', 'employee'])->where('id', $id)->find();
        $this->success(200, $track);
    }

    public function delete()
    {
        $id = $this->request->param('id');
        $track = Track::find($id);
        $track->delete();
        $this->success(204);
    }
}