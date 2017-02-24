<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class Action extends BaseActiveRecord
{
    const TABLE_NAME = "action";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['time', 'con', 'avg_resp', 'pid', 'fetch', 'qps', 'dps'], 'required'],
            [['con', 'time'], 'integer', "min" => 0],
            [['ctime', 'utime'], 'safe'],
            [['ctime'], 'date', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'        => 'ID',
            'time'      => '压测持续时间',
            'con'       => '并发数',
            'avg_resp'  => '平均响应时间',
            'max_resp'  => '最大响应时间',
            'most_resp' => '99%响应时间',
            'pid'       => '项目id',
            'error'     => '错误结果',
            'fetch'     => '总请求次数',
            'qps'       => 'qps',
            'dps'       => 'dps',
            'ctime'     => '创建时间',
            'utime'     => '更新时间',
        ];
    }

    public function getQuery()
    {
        $action_t = self::tableName();
        $query = self::find();
        $query->andFilterWhere(["$action_t.pid" => $this->pid ]);
        return $query;
    }

    public function getChartData()
    {
        $result = $this->getQuery()->asArray()->all();
        $max = 0;
        foreach ($result as $index => $one) {
            if ($max < $one['qps']) {
                $max = $one['qps'];
            }
            
            $ret['con'][] = $one['con'];
            $ret['qps'][] = $one['qps'];
            $error_arr = explode(',', $one['error']);
            $ret['error'][] = $error_arr[3];
        }
        $ret['max'] = (round($max / 200,0) + 1) * 200;
        $ret['max_value'] = $ret['max'] / 5;
        return $ret;
    }
}
