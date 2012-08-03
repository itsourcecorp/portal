<?php
Yii::app()->clientScript->registerScript('refresh','
                           refresh = function(data){
                                    $.fn.yiiGridView.update(data).fadeIn("fast");
                                }
                    
                    ',CClientScript::POS_END); 
                    
$this->breadcrumbs=array(
    'Users'=>array('index'),
    'Edit Roles'=>array('editRoles'),
    'Edit Role '.$model,
);

$this->menu=array(
    array('label'=>'List User', 'url'=>array('index')),
    array('label'=>'Create User', 'url'=>array('create')),
    array('label'=>'Create User Role', 'url'=>array('createRole')),
    array('label'=>'Update Role', 'url'=>array('updateRole','role'=>$role)),
    array('label'=>'Delete Role', 'url'=>array('deleteRole','role'=>$role),  'linkOptions'=>array('confirm'=>'Are you sure you want to delete this item?')),
);
?>
 <div class="flashes">


        <div class="flash success" id="flashes">

            <?php echo Yii::app()->user->getFlash('Error'); ?>
            <?php echo Yii::app()->user->getFlash('Success'); ?>

        </div>



 </div>
<h1 ><? echo $roletenant->name.' '.$model; ?></h1>
<h2 >Child Items</h2>
<?
$this->widget('zii.widgets.jui.CJuiTabs', array(
    'tabs'=>array(
        'Child Roles'=>$this->renderPartial('_childroles',array('dataprovider'=>$dataprovider,'model'=>$role,'filtersForm2'=>$filtersForm2),true),
        'Child Actions'=>$this->renderPartial('_childactions',array('actions'=>$actions,'model'=>$role,'filtersForm3'=>$filtersForm3),true),
    ),
    'themeUrl' => Yii::app()->baseUrl.'/css/jui',
    'theme'=>'redmond', //try 'bee' also to see the changes
    'cssFile'=>array('redmond.css'),
    // additional javascript options for the tabs plugin
    'options'=>array(
    ),
));  
?>
<div class="flashes">


        <div class="flash success" id="aflashes">

            <?php echo Yii::app()->user->getFlash('Error'); ?>
            <?php echo Yii::app()->user->getFlash('Success'); ?>

        </div>



 </div>
<h2>Available Items</h2>
<?
$this->widget('zii.widgets.jui.CJuiTabs', array(
    'tabs'=>array(
        'Available Actions'=>$this->renderPartial('_availableactions',array('availableactions'=>$availableactions,'filtersForm'=>$filtersForm,'model'=>$role),true,true),
        'Available Roles'=>$this->renderPartial('_availableroles',array('actions'=>$availableroles,'model'=>$role,'filtersForm4'=>$filtersForm4),true),
    ),
    'themeUrl' => Yii::app()->baseUrl.'/css/jui',
    'theme'=>'redmond', //try 'bee' also to see the changes
    'cssFile'=>array('redmond.css'),
    // additional javascript options for the tabs plugin
    'options'=>array(
    ),
));  
?>



