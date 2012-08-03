<?php
$this->breadcrumbs=array(
    'Users'=>array('index'),
    'Edit Roles',
);

$this->menu=array(
    array('label'=>'List User', 'url'=>array('index')),
    array('label'=>'Create User', 'url'=>array('create')),
    array('label'=>'Create User Role', 'url'=>array('createRole')),
);
?>
 <div class="flashes">


        <div class="flash success">

            <?php echo Yii::app()->user->getFlash('Error'); ?>
            <?php echo Yii::app()->user->getFlash('Success'); ?>

        </div>



 </div>
<h1>List of Roles</h1>

</br>
<h2 style="float:left;">Assigned Roles</h2>
<?
$this->widget('zii.widgets.grid.CGridView', array(
            'dataProvider'=>$dataprovider,
            'id'=>'roles',
            'template'=>'{items}',
            'emptyText'=>Rights::t('core', 'This user has not been assigned any items.'),
            'htmlOptions'=>array('class'=>'grid-view user-assignment-table mini'),
            'columns'=>array(
                'tenant',
                'role',
                array(
                    'class'=>'CLinkColumn',
                    'urlExpression'=>'array("editRole","role"=>$data["id"])',
                    'labelExpression'=>'"Edit Role"'
                )
            )
        ));
?>

