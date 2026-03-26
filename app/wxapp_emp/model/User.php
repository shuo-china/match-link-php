<?php

namespace app\wxapp_emp\model;

use app\wxapp_emp\model\Favorite;
use think\model\concern\SoftDelete;

class User extends BaseModel
{
    use SoftDelete;

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function getCreateTimeAttr($value)
    {
        return date('Y-m-d H:i', $value);
    }

    public function getLastLoginTimeAttr($value)
    {
        return date('Y-m-d H:i', $value);
    }
}