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
    array('label'=>'List User Role', 'url'=>array('editRoles')),
);
?>
<h1>Create User Role</h1>

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
    <div class="row">
        <?php echo CHtml::label('Tenant','tenant-dropdown'); ?>
        <?php echo CHtml::dropDownList('Tenant',$tenant,$tenants,array('id'=>'tenant-dropdown')); ?>
    </div>


    <div class="row buttons">
        <?php echo CHtml::submitButton('Create Role'); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->