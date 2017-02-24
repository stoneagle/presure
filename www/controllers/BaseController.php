<?php

namespace app\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

class BaseController extends Controller
{
    public function beforeAction($action)
    {
        //登录模块
        return true;
    }

    public function echoJson($data, $error = 0, $msg = '', $content = '')
    {
        header("HTTP/1.1 200 OK");
        header('Content-type: text/json; charset=utf-8');
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        if($content) {
            return \Yii::createObject([
                'class' => 'yii\web\Response',
                'format' => Response::FORMAT_RAW,
                'data' => $content
            ]);
        }
        return \Yii::createObject([
            'class' => 'yii\web\Response',
            'format' => Response::FORMAT_JSON,
            'data' => [
                'error' => $error,
                'message' => $msg,
                'data' => $data,
            ],
        ]);
    }

    // 传入异常对象，返回错误提醒
    public function returnException($e)
    {
        $params = [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ];
        //Yii::info($this->logFormat("exception", $params), \LOG_CATEGORY::BACKEND_EXCEPTION);
        return $this->echoJson($params, $e->getCode(), $e->getMessage());
    }

    public function getParamsByConf($conf_arr, $method_flag = "get")
    {
        $ret = [];
        foreach ($conf_arr as $name => $one) {
            switch ($method_flag) {
                case "get" :
                    $param = Yii::$app->request->get($name, $one[0]);
                    break;
                case "post" :
                    $param = Yii::$app->request->post($name, $one[0]);
                    break;
            }
            if ($one[1] && is_null($param)) {
                throw new  \Exception("{$name}不符合格式", Error::ERR_PARAMS);
            }
            $ret[$name] = $param;
        }
        return $ret;
    }
}
