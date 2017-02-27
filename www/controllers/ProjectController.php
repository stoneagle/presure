<?php

namespace app\controllers;

use Yii;
use app\models\Project;
use app\models\Action;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class ProjectController extends BaseController
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

    // 压测项目展示界面
    public function actionIndex()
    {
        $model = new Project();
        $model->load(Yii::$app->request->queryParams);

        $data_provider = new ActiveDataProvider([
            'query' => $model->getQuery(),
        ]);
        return $this->render('index', [
            'searchModel'      => $model,
            'dataProvider'     => $data_provider,
            'statusArr'     => Project::$status_arr,
        ]);
    }

    public function actionChart()
    {
        $params = Yii::$app->request->queryParams;
        $plan_id = $params['Project']['plan_id'];
        $result = Project::find()->andWhere(['plan_id' => $plan_id])->select("id, name")->asArray()->all();
        $project_id_arr = ArrayHelper::map($result, "id", "name");
        $model = new Action;
        $final_data = [];
        foreach ($project_id_arr as $project_id => $project_name) {
            $model->pid = $project_id;
            $chart_data = $model->getChartData();
            $final_data['x'] = $chart_data['con'];
            $final_data['legend'][] = $project_name;
            $final_data['list'][] = [
                'name' => $project_name,
                'type' => "line",
                'stack' => "总量",
                'data' => $chart_data['qps'],
            ];
        }

        return $this->render('chart', [
            'chart_data'   => json_encode($final_data),
        ]);
    }

    // 校验提交的表单参数
    public function actionValid()
    {
        try {
            $model = new Project();
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
        if (($model = Project::findOne($id)) !== null) {
            return $model;
        } else {
            throw new \Exception("无法找到测试项目对象", Error::ERR_PROJECT_MODEL);
        }
    }

    // 新增压测
    public function actionCreate()
    {
        $model = new Project();
        try {
            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->ctime = date("Y-m-d H:i:s", time());
                $model->modelValidSave();
                $code = Error::ERR_OK;
                return $this->echoJson([
                    'id' => $model->attributes['id'],
                    'plan_id' => $model->attributes['plan_id'],
                ], $code, Error::msg($code));
            } else {
                $model->load(Yii::$app->request->queryParams);
                return $this->render('save', [
                    'model' => $model,
                ]);
            }
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    // 更新压测
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
                return $this->echoJson([
                    'id' => $model->attributes['id'],
                    'plan_id' => $model->attributes['plan_id'],
                ], $code, Error::msg($code));
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

    // 压测删除 
    public function actionDelete()
    {
        try {
            $ids = Yii::$app->request->post('ids', null);
            if (empty($ids)) {
                throw new \Exception (Error::msg(Error::ERR_PARAMS), Error::ERR_PARAMS);
            }
            $ids_str = explode(',',$ids);
            $query = Project::find()->andWhere(['and',['in', 'id', $ids]]);
            foreach ($query->all() as $model) {
                if ($model->status != Project::STATUS_WAIT) {
                    throw new \Exception ("已启动的测试无法删除", Error::ERR_PROJECT_DEL);
                } else {
                    $result = $model->delete();
                    if (!$result) {
                        throw new \Exception (Error::msg(Error::ERR_DEL), Error::ERR_DEL);
                    }
                }
            }
            $code = Error::ERR_OK;
            return $this->echoJson($ids, $code, Error::msg($code));
        } catch (\Exception $e) {
            return $this->returnexception($e);
        }
    }

    // 开始执行压测
    public function actionExec()
    {
        try {
            $id = Yii::$app->request->get('id', null);
            if (empty($id)) {
                throw new \Exception (Error::msg(Error::ERR_PARAMS), Error::ERR_PARAMS);
            }
            $model = $this->findModel($id);
            if ($model->status != Project::STATUS_WAIT) {
                throw new \Exception ("已启动的测试无法删除", Error::ERR_PROJECT_DEL);
            } else {
                $pid   = pcntl_fork();
                if (-1 == $pid) {
                    throw new \Exception ("子进程启动失败", Error::ERR_PROJECT_EXEC);
                } else if (0 == $pid) {
                    $php_bash = "php ".dirname($_SERVER['DOCUMENT_ROOT'])."/daemon/presure.php {$id}";
                    exec($php_bash);
                } else {
                    $code = Error::ERR_OK;
                    //return $this->echoJson([], $code, Error::msg($code));
                    return $this->redirect("index?Project[plan_id]=".$model->plan_id);
                }

            }
        } catch (\Exception $e) {
            return $this->returnexception($e);
        }
    }

    // 撤销并恢复配置 
    public function actionRedo()
    {
        try {
            $id = Yii::$app->request->get('id', null);
            if (empty($id)) {
                throw new \Exception (Error::msg(Error::ERR_PARAMS), Error::ERR_PARAMS);
            }
            $model = $this->findModel($id);
            if ($model->status != Project::STATUS_FIN) {
                throw new \Exception ("未结束的测试无法重启", Error::ERR_PROJECT_DEL);
            } else {
                $model->status = Project::STATUS_WAIT;
                $model->modelValidSave();
                $query = Action::find()->andWhere(["pid" => $id]);
                foreach ($query->all() as $action_model) {
                    $result = $action_model->delete();
                    if (!$result) {
                        throw new exception (Error::msg(Error::ERR_DEL), Error::ERR_DEL);
                    }
                }
                return $this->redirect("index?Project[plan_id]=".$model->plan_id);
            }
        } catch (\Exception $e) {
            return $this->returnexception($e);
        }
        
    }

    // 停止压测执行
    public function actionStop()
    {
        try {
            $id = Yii::$app->request->get('id', null);
            if (empty($id)) {
                throw new \Exception (Error::msg(Error::ERR_PARAMS), Error::ERR_PARAMS);
            }
            $model = $this->findModel($id);
            if ($model->status != Project::STATUS_EXEC) {
                throw new \Exception ("已启动的测试无法删除", Error::ERR_PROJECT_DEL);
            } else {
                $model->status = Project::STATUS_FIN;
                $model->modelValidSave();
            }

            $code = Error::ERR_OK;
            return $this->redirect("index?Project[plan_id]=".$model->plan_id);
        } catch (\Exception $e) {
            return $this->returnexception($e);
        }
    }
}
