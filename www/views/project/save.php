<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\AppAsset;

AppAsset::register($this);
$this->params['breadcrumbs'][] = ['label' => '测试管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
if ($model->isNewRecord) {
    $href = "/project/create";
    $this->title = '创建测试';
} else {
    $href = "/project/update?id=".$id;
    $this->params['breadcrumbs'][] = ['label' => $model->name];
    $this->title = '更新测试';
}

$form = ActiveForm::begin([
    'id' => 'form',
    'options' => ['class' => 'project'],
    'enableAjaxValidation' => true,
    'validationUrl' => '/project/valid',
])
?>
<div class="channel">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php if(!$model->isNewRecord) { 
        echo $form->field($model, 'id')->textInput()->hiddenInput()->label(false);
    } 
    ?>
    <?= $form->field($model, 'name')->textInput(['placeholder' => '不能为空'])?>
    <?= $form->field($model, 'url')->textInput(['readonly'=>($model->isNewRecord == true)? false : true])->hint('不能为空')?>
    <?= $form->field($model, 'init')->textInput(['readonly'=>($model->isNewRecord == true)? false : true])->hint('不能为空')?>
    <?= $form->field($model, 'incr')->textInput(['readonly'=>($model->isNewRecord == true)? false : true])->hint('不能为空')?>
    <?= $form->field($model, 'num')->textInput(['readonly'=>($model->isNewRecord == true)? false : true])->hint('不能为空')?>
    <?= $form->field($model, 'desc')->textArea(['rows' => '6'])->label('描述'); ?>
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
                        project_id = data.data['id'];
                        window.location.href = "/project/index?Project[id]=" + project_id;
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
