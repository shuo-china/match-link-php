<?php

namespace app\wxapp_emp\model;
use think\model\concern\SoftDelete;

class Track extends BaseModel
{
    use SoftDelete;

    protected $name = 'member_track';

    public function member()
    {
        return $this->hasOne(Member::class, 'id', 'member_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }

    public function getIntentionTextAttr()
    {
        return config('dict.purchase_intention')[$this->getAttr('intention')];
    }

    public function getCreateTimeAttr($value)
    {
        return date('m-d H:i', $value);
    }
}