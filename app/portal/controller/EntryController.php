<?php

namespace app\portal\controller;

use app\wxapp_emp\model\Member;

class EntryController extends BaseController
{
    public function index()
    {
        return;

        $members = \think\facade\Db::table('kr_member_copy')->select()->toArray();

        for ($i = 0; $i < count($members); $i++) {
            $item = $members[$i];
            $item['photoIds'] = explode(',', $item['photoIds']);
            $albumKeys = [];
            foreach ($item['photoIds'] as $id) {
                $key = \think\facade\Db::table('kr_file')->where('id', (int) ($id) + 11000)->value('key');
                if (!empty($key)) {
                    $albumKeys[] = $key;
                }
            }

            $data = [
                'uid' => $item['uid'],
                'name' => $item['name'],
                'mobile' => $item['mobile'],
                'gender' => $item['gender'],
                'birthYear' => $item['birthYear'],
                'height' => $item['height'],
                'industry' => '',
                'education' => $item['education'],
                'occupation' => $item['occupation'],
                'albumKeys' => $albumKeys,
                'annualIncome' => $item['annualIncome'],
                'hasHouse' => $item['hasHouse'],
                'houseCount' => $item['houseCount'],
                'hasVehicle' => $item['hasVehicle'],
                'currentAddress' => explode(',', $item['currentAddress']),
                'permanentAddress' => explode(',', $item['permanentAddress']),
                'familys' => explode(',', $item['familys']),
                'maritalStatus' => $item['maritalStatus'],
                'hasChildren' => $item['hasChildren'],
                'childrens' => $item['childrens'],
                'remark' => $item['remark'],
                'employee_id' => 100001
            ];

            $mbr = new Member();
            $mbr->save($data);
        }
    }

    public function index2()
    {
        return;

        $files = \think\facade\Db::table('kr_file_copy')->select()->toArray();

        foreach ($files as $item) {
            $item['id'] = $item['id'] + 11000;
            $item['key'] = uniqid();
            unset($item['delete_time']);
            \think\facade\Db::table('kr_file')->insert($item);
        }
    }

    protected function getText($input)
    {
        $content = "请根据以下JSON映射,将用户输入的职业名称转换为对应的键名。如果找不到完全匹配的职业,则返回,other.
        {
            student: 学生,
            it: IT/互联网,
            education: 教育/科研,
            government: 政府机构,
            enterprise: 国企/事业单位,
            manufacturing: 生产/制造,
            realEstate: 建筑/房地产,
            health: 医疗/健康,
            communication: 通信/电子,
            finance: 金融,
            humanResources: 人事/行政,
            mediaArts: 传媒/艺术,
            sales: 销售,
            services: 服务业,
            transportation: 交通运输,
            commercePurchase: 商贸/采购,
            biotechnologyPharmaceuticals: 生物/制药,
            law: 法律,
            advertisingMarketing: 广告/市场,
            consultingAdvisory: 咨询/顾问,
            highManagement: 高级管理,
            logisticsWarehousing: 物流/仓储,
            agricultureForestryFishing: 农林牧渔,
            accountingAuditing: 财会/审计,
            chemicalEnergy: 化工/能源,
            hotelsTravel: 酒店/旅游,
            freelance: 自由职业,
            other: 其他职业,
            unemployed: 待业
        }";

        $apiKey = "sk-dcf43ee81f144249959ac3f948e827d8";

        $payload = [
            'model' => 'deepseek-chat',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $content . "\n\n只输出键名（必须是映射中的一个key），不要输出任何解释、标点或换行。",
                ],
                [
                    'role' => 'user',
                    'content' => $input,
                ],
            ],
            'stream' => false,
            'temperature' => 0,
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.deepseek.com/chat/completions');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $apiKey,
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($curl);
        $curlErrno = curl_errno($curl);
        $curlError = curl_error($curl);
        $httpCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($curlErrno) {
            $this->error(502, '调用DeepSeek失败', 'DEEPSEEK_REQUEST_FAIL', [
                'curl_errno' => $curlErrno,
                'curl_error' => $curlError,
            ]);
        }

        $resp = json_decode((string) $output, true);
        $rawText = (string) ($resp['choices'][0]['message']['content'] ?? '');
        $rawText = trim($rawText);

        return $rawText;
    }

    public function index3()
    {
        return;
        $dict = [
            1 => '公务员',
            2 => '事业单位',
            3 => '个体老板',
            4 => '公司高管',
            5 => '上班族',
            6 => '自由职业',
            7 => '在校学生',
            8 => '军人',
            9 => '求职中',
            10 => '退休',
        ];

        $members = \think\facade\Db::table('kr_member_copy')->select()->toArray();

        // 永远不超时
        set_time_limit(0);

        // 忽略异常
        try {
            foreach ($members as $v) {
                if (\think\facade\Db::table('kr_member')->where('uid', $v['uid'])->value('industry')) {
                    continue;
                }
                $result = $dict[$v['careerType']];
                if ($v['occupation']) {
                    $result .= '-' . $v['occupation'];
                    \think\facade\Db::table('kr_member')->where('uid', $v['uid'])->update([
                        'industry' => $this->getText($result)
                    ]);
                }
            }
        } catch (\Exception $e) {
            echo 'error';
        }
    }
}