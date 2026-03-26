<?php
namespace app\wxapp_cust\controller;

use app\wxapp_cust\model\Favorite;

class FavoriteController extends BaseController
{
    public function pagination()
    {
        $map = [
            ['user_id', '=', $this->request->userId],
        ];
        $favs = Favorite::where($map)
            ->with([
                'member' => function ($query) {
                    $query->append(['cover', 'age', 'job_text', 'education_text']);
                },
            ])
            ->paginate();
        $this->success(200, $favs);
    }

    public function like()
    {
        $post = $this->request->post();
        $post['user_id'] = $this->request->userId;
        $favorite = new Favorite();
        $favorite->save($post);
        $this->success(201);
    }

    public function dislike()
    {
        $post = $this->request->post();
        $favorite = new Favorite();
        $has = $favorite->where('user_id', '=', $this->request->userId)
            ->where('member_id', '=', $post['member_id'])
            ->find();
        if ($has) {
            $has->delete();
        }
        $this->success(201);
    }
}