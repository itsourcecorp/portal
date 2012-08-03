<?php           
$this->breadcrumbs=array(
    'Users'=>array('index'),
    $model->id,
);

$this->menu=array(
    array('label'=>'List User', 'url'=>array('index')),
    array('label'=>'Create User', 'url'=>array('create')),
);
?>
<h1>Assign User To Tenant</h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'user-form',
    'enableAjaxValidation'=>false,
)); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    
    <div class="row">
        <?php echo $form->labelEx($model,'email'); ?>
        <?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>255)); ?>
        <?php echo $form->error($model,'email'); ?>
    </div>
    <div class="row">
        <?php echo CHtml::label('Tenant','tenant-dropdown'); ?>
        <?php echo CHtml::dropDownList('Tenant',$tenant,$tenants,array('id'=>'tenant-dropdown')); ?>
    </div>


    <div class="row buttons">
        <?php echo CHtml::submitButton('Add User'); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->