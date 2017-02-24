<?php

namespace app\controllers;

use Yii;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class ActionController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionNormal()
    {
        
    }
}

