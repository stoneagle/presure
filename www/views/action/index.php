<?php

use app\models\Project;
use kartik\grid\GridView;
use yii\helpers\Html;
use app\assets\AppAsset;
use yii\helpers\ArrayHelper;
use \kartik\date\DatePicker;

$this->title = '压测详情';
$this->params['breadcrumbs'][] = ['label' => '测试管理', 'url' => ['/project/index']];
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
$this->registerJsFile('@web/js/lib/echarts.js',['position'=>$this::POS_HEAD]);
?>

<div>
    <h1><?= Html::encode($this->title) ?></h1>
    <div data-pjax-timeout="1000" data-pjax-push-state="" data-pjax-container="" id="w0">
        <?php
         $gridColumns = [
                "box" => [
                    'class' => 'yii\grid\CheckboxColumn',
                    'name' => 'action_box',
                    'contentOptions' => [
                        'class' => 'data-id'
                    ],
                ],
                "id",
                "time",
                "con",
                "avg_resp",
                "fetch",
                "qps",
                "dps",
                "error",
                [
                    'attribute' => 'ctime',
                    'contentOptions' => ['width' => '15%'],
                    'filter'    => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'ctime',
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'autoclose'=> true,
                            'format' => 'yyyy-M-dd'
                        ],
                    ]),
                    'value' => function ($model) {
                        return $model->ctime;
                    },
                ],
            ];

        ?>
        <div class="grid-view" id="w1">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'panel' => [
                    'type' => GridView::TYPE_PRIMARY,
                    'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-book"></i></h3>',
                ],
                'toolbar' => [
                    //$fullExportMenu,
                ],
                'options' => ['class' => 'grid-view','style'=>'overflow:auto', 'id' => 'grid'],
                'columns' => $gridColumns
                ])
            ?>
        </div>
    </div>
    <div id="chart" style="width: 800px;height:600px;"></div>
</div>

<script>
var myChart = echarts.init(document.getElementById('chart'));
var chart_data = <?php echo $chart_data;?>;
console.log(chart_data.max);
//app.title = '折柱混合';
option = {
    tooltip: {
        trigger: 'axis'
    },
    toolbox: {
        feature: {
            dataView: {show: true, readOnly: false},
            magicType: {show: true, type: ['line', 'bar']},
            restore: {show: true},
            saveAsImage: {show: true}
        }
    },
    legend: {
        data:['错误数','qps']
    },
    xAxis: [
        {
            type: 'category',
            data: chart_data.con 
        }
    ],
    yAxis: [
        {
            type: 'value',
            name: 'qps',
            min: 0,
            max: chart_data.max,
            interval: chart_data.max_value,
            axisLabel: {
                formatter: '{value} 次'
            }
        },
        {
            type: 'value',
            name: '错误次数',
            min: 0,
            max: 1000,
            interval: 200,
            axisLabel: {
                formatter: '{value} 次'
            }
        }
    ],
    series: [
        {
            name:'qps',
            type:'line',
            yAxisIndex: 0,
            data:chart_data.qps
        },
        {
            name:'错误数',
            type:'bar',
            yAxisIndex: 1,
            data:chart_data.error
        }
    ]
};
myChart.setOption(option);
</script>
