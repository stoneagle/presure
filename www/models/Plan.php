<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class Plan extends BaseActiveRecord
{
    const TABLE_NAME = "plan";

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
            [['name'], 'required'],
            [['ctime', 'utime'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'    => 'ID',
            'name'  => '计划名称',
            'ctime' => '创建时间',
            'utime' => '更新时间',
        ];
    }

    public function getQuery()
    {
        $plan_t = self::tableName();
        $query = self::find();
        $query->andFilterWhere(["like", "$plan_t.name", $this->name]);
        return $query;
    }
}
