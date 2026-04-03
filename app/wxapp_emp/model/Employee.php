<?php

namespace app\wxapp_emp\model;
use think\model\concern\SoftDelete;

class Employee extends BaseModel
{
    use SoftDelete;

    protected $type = [
        'avatar_key' => 'array',
    ];

    public function getAvatarAttr()
    {
        $keys = $this->getAttr('avatar_key');
        if (empty($keys)) {
            return [];
        }
        return get_files($keys);
    }
}