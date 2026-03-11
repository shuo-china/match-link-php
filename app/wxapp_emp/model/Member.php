<?php

namespace app\wxapp_emp\model;
use think\model\concern\SoftDelete;

class Member extends BaseModel
{
    use SoftDelete;

    protected $type = [
        'albumKeys' => 'array',
        'currentAddress' => 'array',
        'permanentAddress' => 'array',
        'familys' => 'array',
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }

    public function getAlbumsAttr()
    {
        $keys = $this->getAttr('albumKeys');
        if (empty($keys)) {
            return [];
        }
        return get_files($keys);
    }

    public function getCoverAttr()
    {
        $keys = $this->getAttr('albumKeys');
        if (empty($keys)) {
            return null;
        }
        return get_file($keys[0]);
    }

    public function getAgeAttr()
    {
        $birthYear = $this->getAttr('birthYear');
        $currentYear = date('Y');
        return $currentYear - $birthYear;
    }

    public function getGenderTextAttr()
    {
        return config('dict.gender')[$this->getAttr('gender')];
    }

    public function getIndustryTextAttr()
    {
        return config('dict.industry')[$this->getAttr('industry')];
    }

    public function getEducationTextAttr()
    {
        return config('dict.education')[$this->getAttr('education')];
    }

    public function getMaritalStatusTextAttr()
    {
        return config('dict.marital_status')[$this->getAttr('maritalStatus')];
    }
}