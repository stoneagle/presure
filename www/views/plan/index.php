<?php

use app\models\Project;
use kartik\grid\GridView;
use yii\helpers\Html;
use app\assets\AppAsset;
use yii\helpers\ArrayHelper;
use \kartik\date\DatePicker;

$this->title = '计划管理';
$this->params['breadcrumbs'][] = $this->title;
AppAsset::register($this);
?>

<div>
    <h1><?= Html::encode($this->title) ?></h1>
    <div data-pjax-timeout="1000" data-pjax-push-state="" data-pjax-container="" id="w0">
        <?php
         $gridColumns = [
                "box" => [
                    'class' => 'yii\grid\CheckboxColumn',
                    'name' => 'box',
                    'contentOptions' => [
                        'class' => 'data-id'
                    ],
                ],
                "id",
                "name",
                [
                    'attribute' => 'ctime',
                    'contentOptions' => ['width' => '10%'],
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
                [
                    'attribute' => 'utime',
                    'contentOptions' => ['width' => '10%'],
                    'filter'    => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'utime',
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
                "button" => [
                    'header' => '操作',
                    'contentOptions' => ['style' => 'white-space: normal;', 'width' => '12%'],
                    'class' => 'yii\grid\ActionColumn',
                    'template' => "{check} {edit} {delete}",
                    'buttons' => [
                        'check' => function ($url, $model) {
                            return Html::a(
                            "测试详情",
                            "/project/index?Project[plan_id]={$model->id}",
                            [
                                'data-pjax' => '0',
                                'class' => 'label label-warning handle',
                            ]);
                        },
                        'edit' => function ($url, $model) {
                            return Html::a( 
                                "修改",
                                "/plan/update?id=".$model->id,
                                [
                                    'data-pjax' => '0',
                                    'class'     => 'label label-primary handle',
                                ]
                            );
                        },
                        'delete' => function ($url, $model) {
                            return Html::a( 
                                "删除",
                                "/plan/delete",
                                [
                                    'data-pjax' => '0',
                                    'name'      => "plan_delete",
                                    'model_id'  => $model->id,
                                    'class'     => 'label label-primary handle',
                                ]
                            );
                        },
                    ]
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
                    Html::a('新建计划',['create?'.$_SERVER['QUERY_STRING']],['data-pjax'      => 0, 'class' => 'btn btn-success',]),
                ],
                'options' => ['class' => 'grid-view','style'=>'overflow:auto', 'id' => 'grid'],
                'columns' => $gridColumns
                ])
            ?>
        </div>
    </div>
</div>

<?php $this->beginBlock('js') ?>  
$(function(){
    $("[name='plan_delete']").on('click', function (e) {
        e.preventDefault();
        href = $(this).attr("href");
        var post_data = {
            'ids' : $(this).attr("model_id")
        };
        $.ajax({
            url: href,
            data: post_data,
            dataType: 'text',
            type: 'POST',
            success: function(result) {
                var data = eval('(' + result + ')');  
                if (data.error === 0) {
                    swal({
                        title: "操作成功!",   
                        text: data.message,  
                        type: "success",    
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "确定",
                    },function(){
                        location.reload();
                    });
                } else {
                    swal("操作失败!", data.message, "error");
                }
            },
            error: function(data) {
                swal("操作失败!", data.message, "error");
            }
        })
    });
});
<?php $this->endBlock() ?>  
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>  
