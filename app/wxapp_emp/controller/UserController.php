<?php
namespace app\wxapp_emp\controller;

use app\wxapp_emp\model\User;

class UserController extends BaseController
{
    public function pagination()
    {
        $users = User::where('mobile', '<>', null)->with([
            'favorites' => function ($query) {
                $query->with([
                    'member' => function ($query) {
                        $query->append(['cover']);
                    }
                ]);
            }
        ])->order('id', 'desc')->paginate();
        $usersData = $users->toArray();

        foreach ($usersData['data'] as &$user) {
            $user['members'] = [];

            if (empty($user['favorites'])) {
                continue;
            }

            foreach ($user['favorites'] as &$favorite) {
                if (!empty($favorite['member'])) {
                    $user['members'][] = $favorite['member'];
                    unset($favorite['member']);
                }
            }
            unset($favorite);
        }
        unset($user);

        $this->success(200, $usersData);
    }
}