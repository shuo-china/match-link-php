<?php

namespace app\wxapp_cust\model;

use think\model\concern\SoftDelete;

class Member extends BaseModel
{
    use SoftDelete;

    public $appendFields = [
        'albums',
        'age',
        'cover',
        'coverPath',
        'albumPaths',
        'gender_text',
        'familys_text',
        'job_text',
        'education_text',
        'childrens_text',
        'marital_status_text',
    ];

    protected $type = [
        'albumKeys' => 'array',
        'currentAddress' => 'array',
        'permanentAddress' => 'array',
        'familys' => 'array',
        'childrens' => 'json'
    ];

    protected $hidden = [
        'mobile'
    ];

    public function findDetailById($id)
    {
        return $this
            ->where('id', $id)
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
        if (count($albumPaths) <= 1) {
            return $albumPaths;
        }
        $first = array_shift($albumPaths);
        $albumPaths[] = $first;
        return $albumPaths;
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

    public function getJobTextAttr()
    {
        $industryText = $this->getAttr('industry') ? config('dict.industry')[$this->getAttr('industry')] : '';
        $occupationText = $this->getAttr('occupation');
        $res = [];
        if (!empty($industryText)) {
            $res[] = $industryText;
        }
        if (!empty($occupationText)) {
            $res[] = $occupationText;
        }
        return implode('-', $res);
    }

    public function getChildrensTextAttr()
    {
        $childrens = $this->getAttr('childrens');
        if (empty($childrens)) {
            return '';
        }
        $stats = [
            '1_1' => 0,
            '1_2' => 0,
            '2_1' => 0,
            '2_2' => 0,
        ];
        $childrensText = [];
        foreach ($childrens as $v) {
            $gender = (int) ($v['gender'] ?? 0);
            $custody = (int) ($v['custody'] ?? 0);
            $key = $gender . '_' . $custody;
            if (isset($stats[$key])) {
                $stats[$key]++;
            }
        }
        $selfBoys = $stats['1_1'];
        $selfGirls = $stats['2_1'];
        $otherBoys = $stats['1_2'];
        $otherGirls = $stats['2_2'];

        if ($selfBoys > 0 || $selfGirls > 0) {
            $selfParts = [];
            if ($selfBoys > 0) {
                $selfParts[] = $selfBoys . '个男孩';
            }
            if ($selfGirls > 0) {
                $selfParts[] = $selfGirls . '个女孩';
            }
            $childrensText[] = implode(',', $selfParts) . '跟自己';
        }
        if ($otherBoys > 0 || $otherGirls > 0) {
            $otherParts = [];
            if ($otherBoys > 0) {
                $otherParts[] = $otherBoys . '个男孩';
            }
            if ($otherGirls > 0) {
                $otherParts[] = $otherGirls . '个女孩';
            }
            $childrensText[] = implode('', $otherParts) . '跟对方';
        }
        return implode('，', $childrensText);
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