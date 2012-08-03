<?php           
$this->breadcrumbs=array(
    'Users'=>array('index'),
    'Edit Roles' =>array('editRoles'),
    'Create Role'
);

$this->menu=array(
    array('label'=>'List User', 'url'=>array('index')),
    array('label'=>'Create User', 'url'=>array('create')),
    array('label'=>'Create User Role', 'url'=>array('createRole')),
    array('label'=>'Update Role', 'url'=>array('updateRole','role'=>$role->name)),
    array('label'=>'Delete Role', 'url'=>array('deleteRole','role'=>$role->name),  'linkOptions'=>array('confirm'=>'Are you sure you want to delete this item?')),

);
?>
<h1>Update <? echo $roletenant->name.' '.$name ?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'role-form',
    'enableAjaxValidation'=>false,
)); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    
    <div class="row">
        <?php echo $form->labelEx($model,'name'); ?>
        <?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255)); ?>
        <?php echo $form->error($model,'name'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'description'); ?>
        <?php echo $form->textArea($model,'description',array('size'=>60,'maxlength'=>255)); ?>
        <?php echo $form->error($model,'description'); ?>
    </div>


    <div class="row buttons">
        <?php echo CHtml::submitButton('Update Role'); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->