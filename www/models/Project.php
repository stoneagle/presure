<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class Project extends BaseActiveRecord
{
    const TABLE_NAME = "project";

    const STATUS_WAIT = 0;
    const STATUS_EXEC = 1;
    const STATUS_FIN = 2;

    public static $status_arr = [
        self::STATUS_WAIT => "等待中",
        self::STATUS_EXEC => "执行中",
        self::STATUS_FIN  => "已结束",
    ];

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
            [['name', 'url', 'init', 'incr'], 'required'],
            [['id', 'init', 'plan_id'], 'integer', "min" => 0],
            [['incr'], 'integer', "min" => 1, "max" => 100],
            [['num'], 'integer', "min" => 1, "max" => 10],
            [['ctime', 'utime'], 'safe'],
            [['ctime', 'utime'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['name', 'desc'], 'string', 'max' => 128],
            [['url'], 'string', 'max' => 512]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'      => 'ID',
            'plan_id' => '所属计划id',
            'name'    => '测试名称',
            'desc'    => '测试描述',
            'url'     => '压测地址',
            'init'    => '初始并发',
            'incr'    => '并发递增数量',
            'num'     => '递增次数',
            'ctime'   => '创建时间',
            'utime'   => '更新时间',
        ];
    }

    public function getQuery()
    {
        $project_t = self::tableName();
        $query = self::find();
        $query->andFilterWhere(["like", "$project_t.name", $this->name]);
        $query->andFilterWhere(["$project_t.plan_id" => $this->plan_id]);
        return $query;
    }
}
