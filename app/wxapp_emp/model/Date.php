<?php

namespace app\wxapp_emp\model;
use think\model\concern\SoftDelete;

class Date extends BaseModel
{
    use SoftDelete;

    protected $name = 'member_date';

    public function maleMember()
    {
        return $this->hasOne(Member::class, 'id', 'male_member_id');
    }

    public function femaleMember()
    {
        return $this->hasOne(Member::class, 'id', 'female_member_id');
    }

    public function getManToWomanLevelTextAttr()
    {
        return $this->getAttr('man_to_woman_level') ? config('dict.date_result')[$this->getAttr('man_to_woman_level')] : '';
    }

    public function getWomanToManLevelTextAttr()
    {
        return $this->getAttr('woman_to_man_level') ? config('dict.date_result')[$this->getAttr('woman_to_man_level')] : '';
    }

    public function getCreateTimeAttr($value)
    {
        return date('m-d H:i', $value);
    }
}