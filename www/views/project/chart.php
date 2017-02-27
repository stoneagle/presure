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
    <div id="chart" style="width: 800px;height:600px;"></div>
</div>

<script>
var myChart = echarts.init(document.getElementById('chart'));
var chart_data = <?php echo $chart_data;?>;
console.log(chart_data);
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
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    toolbox: {
        feature: {
            saveAsImage: {}
        }
    },
    xAxis: {
        type: 'category',
        boundaryGap: false,
        data: chart_data['x'] 
    },
    yAxis: {
        type: 'value'
    },
    series: chart_data.list    
};

myChart.setOption(option);
</script>
