<?php

use app\models\Project;
use kartik\grid\GridView;
use yii\helpers\Html;
use app\assets\AppAsset;
use yii\helpers\ArrayHelper;
use \kartik\date\DatePicker;

$this->title = '测试管理';
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
                    'name' => 'project_box',
                    'contentOptions' => [
                        'class' => 'data-id'
                    ],
                ],
                "id",
                "name",
                "url",
                "init",
                "incr",
                "num",
                [
                    "attribute" => "status",
                    "label" => "状态",
                    'contentOptions' => ['width' => '10%'],
                    //'filter' => Html::activeDropDownList($searchModel, '', $appList, ['class' => 'form-control']),
                    'value' => function ($model) use($statusArr) {
                        return ArrayHelper::getValue($statusArr, $model->status);
                    },
                ],
                "desc",
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
                    'template' => "{exec} {edit} {delete} {chart}",
                    'buttons' => [
                        'exec' => function ($url, $model) {
                            if ($model->status == Project::STATUS_WAIT) {
                                $ret = Html::a( 
                                    "执行",
                                    "/project/exec?id={$model->id}",
                                    [
                                        'data-pjax' => '0',
                                        'class' => 'label label-primary handle',
                                    ]
                                );
                            } else if ($model->status == Project::STATUS_EXEC) {
                                $ret = Html::a( 
                                    "暂停",
                                    "/project/stop?id={$model->id}",
                                    [
                                        'data-pjax' => '0',
                                        'class' => 'label label-primary handle',
                                    ]
                                );
                            }
                            return $ret; 
                        },
                        'edit' => function ($url, $model) {
                            return Html::a( 
                                "修改",
                                "/project/update?id=".$model->id,
                                [
                                    'data-pjax' => '0',
                                    'class'     => 'label label-primary handle',
                                ]
                            );
                        },
                        'delete' => function ($url, $model) {
                            return Html::a( 
                                "删除",
                                "/project/delete",
                                [
                                    'data-pjax' => '0',
                                    'name'      => "project_delete",
                                    'model_id'  => $model->id,
                                    'class'     => 'label label-primary handle',
                                ]
                            );
                        },
                        'chart' => function ($url, $model) {
                            return Html::a(
                            "压测详情",
                            "/action/index?Action[pid]={$model->id}",
                            [
                                'data-pjax' => '0',
                                'class' => 'label label-warning handle',
                            ]);
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
                    Html::a('新建测试',['create?'.$_SERVER['QUERY_STRING']],['data-pjax'      => 0, 'class' => 'btn btn-success',]),
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
    $("[name='project_delete']").on('click', function (e) {
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
