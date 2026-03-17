<?php

namespace app\wxapp_cust\model;

use think\model\concern\SoftDelete;

class Member extends BaseModel
{
    use SoftDelete;

    protected $appendFields = [
        'albums',
        'age',
        'cover',
        'coverPath',
        'albumPaths',
        'gender_text',
        'familys_text',
        'industry_text',
        'education_text',
        'marital_status_text',
    ];

    protected $type = [
        'albumKeys' => 'array',
        'currentAddress' => 'array',
        'permanentAddress' => 'array',
        'familys' => 'array',
    ];

    public function findDetailByUid($uid)
    {
        return $this
            ->where('uid', $uid)
            ->append($this->appendFields)
            ->find();
    }

    public function findRandomDetail($exceptUids = [])
    {
        return $this
            // ->whereNotIn('uid', $exceptUids)
            ->orderRaw('RAND()')
            ->append($this->appendFields)
            ->find();
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

    public function getCoverPathAttr()
    {
        $albumPaths = array_column($this->albums, 'path');
        return $albumPaths[0] ?? null;
    }

    public function getAlbumPathsAttr()
    {
        $albumPaths = array_column($this->albums, 'path');
        return array_slice($albumPaths, 1);
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

    public function getFamilysTextAttr()
    {
        $familys = $this->getAttr('familys');
        if (empty($familys)) {
            return [];
        }
        $familysText = [];
        foreach ($familys as $v) {
            $familysText[] = config('dict.family')[$v];
        }
        return implode('，', $familysText);
    }
}