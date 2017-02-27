<?php

namespace app\controllers;

use Yii;
use app\models\Plan;
use app\models\Project;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class PlanController extends BaseController
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

    public function actionIndex()
    {
        $model = new Plan();
        $model->load(Yii::$app->request->queryParams);

        $data_provider = new ActiveDataProvider([
            'query' => $model->getQuery(),
        ]);
        return $this->render('index', [
            'searchModel'      => $model,
            'dataProvider'     => $data_provider,
        ]);
    }

    // 校验提交的表单参数
    public function actionValid()
    {
        try {
            $model = new Plan();
            if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {        
                if (!is_null($model->id)) {
                    $model = $this->findModel($model->id);
                    $model->load(Yii::$app->request->post());
                }
                return $this->returnValidResponse($model);
            }
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    // model查找
    protected function findModel($id)
    {
        if (($model = Plan::findOne($id)) !== null) {
            return $model;
        } else {
            throw new \Exception("无法找到对象", Error::ERR_MODEL);
        }
    }

    public function actionCreate()
    {
        $model = new Plan();
        try {
            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->modelValidSave();
                $code = Error::ERR_OK;
                return $this->echoJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                return $this->render('save', [
                    'model' => $model,
                ]);
            }
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionUpdate()
    {
        try {
            $params_conf = [
                "id" => [null,true],
            ];
            $params = $this->getParamsByConf($params_conf, 'get');
            $model  = $this->findModel($params['id']);

            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->modelValidSave();
                $code = Error::ERR_OK;
                return $this->echoJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                return $this->render('save', [
                    'model' => $model,
                    'id'    => $params['id'],
                ]);
            }
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    // 计划删除 
    public function actionDelete()
    {
        try {
            $ids = Yii::$app->request->post('ids', null);
            if (empty($ids)) {
                throw new \Exception (Error::msg(Error::ERR_PARAMS), Error::ERR_PARAMS);
            }
            $ids_str = explode(',',$ids);
            $query = Plan::find()->andWhere(['and',['in', 'id', $ids]]);
            foreach ($query->all() as $model) {
                $project_num = Project::find()->andWhere(['plan_id' => $model->id])->count();
                if ($project_num > 0) {
                    throw new \Exception ("已有测试的计划不允许删除", Error::ERR_DEL);
                }
                $result = $model->delete();
                if (!$result) {
                    throw new \Exception (Error::msg(Error::ERR_DEL), Error::ERR_DEL);
                }
            }
            $code = Error::ERR_OK;
            return $this->echoJson($ids, $code, Error::msg($code));
        } catch (\Exception $e) {
            return $this->returnexception($e);
        }
    }
}
