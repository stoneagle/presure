<?php

use app\models\Project;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$this->title = '压测图表';
$this->params['breadcrumbs'][] = ['label' => '测试管理', 'url' => ['/project/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('@web/js/lib/echarts.js',['position'=>$this::POS_HEAD]);
?>

<div>
    <h1><?= Html::encode($this->title) ?></h1>
    <div id="chart" style="width: 800px;height:600px; float:left;"></div>
    <div id="chart_avg" style="width: 800px;height:600px; float:right;"></div>
</div>

<script>
var myChart = echarts.init(document.getElementById('chart'));
var myChart2 = echarts.init(document.getElementById('chart_avg'));
var chart_data = <?php echo $chart_data;?>;
option = {
    title: {
        text: '压测结果'
    },
    tooltip: {
        trigger: 'axis'
    },
    legend: {
        data:chart_data.legend
    },
    toolbox: {
        feature: {
            saveAsImage: {}
        }
    },
    xAxis: {
        name: "并发线程",
        type: 'category',
        boundaryGap: false,
        data: chart_data['x'] 
    },
    yAxis: {
        name: "qps",
        type: 'value'
    },
    series: chart_data.list    
};

myChart.setOption(option);

option = {
    title: {
        text: '压测结果'
    },
    tooltip: {
        trigger: 'axis'
    },
    legend: {
        data:chart_data.legend
    },
    toolbox: {
        feature: {
            saveAsImage: {}
        }
    },
    xAxis: {
        name: "并发线程",
        type: 'category',
        boundaryGap: false,
        data: chart_data['x'] 
    },
    yAxis: {
        name: "平均响应时间(ms)",
        type: 'value'
    },
    series: chart_data.avg    
};
myChart2.setOption(option);
</script>
