<?php

namespace app\wxapp_cust\model;

class Favorite extends BaseModel
{
    protected $name = 'user_favorite';

    public function member()
    {
        return $this->hasOne(Member::class, 'id', 'member_id');
    }
}