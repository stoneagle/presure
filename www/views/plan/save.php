<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\AppAsset;

AppAsset::register($this);
$this->params['breadcrumbs'][] = ['label' => '计划管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
if ($model->isNewRecord) {
    $href = "/plan/create";
    $this->title = '创建计划';
} else {
    $href = "/plan/update?id=".$id;
    $this->params['breadcrumbs'][] = ['label' => $model->name];
    $this->title = '更新计划';
}

$form = ActiveForm::begin([
    'id' => 'form',
    'options' => ['class' => 'plan'],
    'enableAjaxValidation' => true,
    'validationUrl' => '/plan/valid',
])
?>
<div class="channel">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php if(!$model->isNewRecord) { 
        echo $form->field($model, 'id')->textInput()->hiddenInput()->label(false);
    } 
    ?>
    <?= $form->field($model, 'name')->textInput(['placeholder' => '不能为空'])?>
    <div class="form-group">
        <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end() ?>
</div>

<?php $this->beginBlock('js') ?>  
$(function(){
    // 提交按钮
    $('#form').on('beforeSubmit',function(e){
        var post_data = $(this).serializeArray();
        var href = "<?php echo $href;?>";
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
                        var id = data.data['id'];
                        window.location.href = "/plan/index?Plan[id]=" + id;
                    });
                } else {
                    swal("操作失败!", data.message, "error");
                }
            },
            error: function(data) {
                swal("操作失败!", data.message, "error");
            }
        })
    }).on('submit', function(e){
        e.preventDefault();
    });
});
<?php $this->endBlock() ?>  

<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>  
