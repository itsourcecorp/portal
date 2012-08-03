<?php
$this->breadcrumbs=array(
    'Users'=>array('index'),
    $model->id,
);

$this->menu=array(
    array('label'=>'List User', 'url'=>array('index')),
    array('label'=>'Create User', 'url'=>array('create')),
    array('label'=>'Back to User Profile', 'url'=>array('view', 'id'=>$model->id)),
    array('label'=>'Update User Profile', 'url'=>array('update', 'id'=>$model->id)),
    array('label'=>'Edit Roles', 'url'=>array('editRoles', 'id'=>$model->id)),
);
?>
 <div class="flashes">


        <div class="flash success">

            <?php echo Yii::app()->user->getFlash('Error'); ?>
            <?php echo Yii::app()->user->getFlash('Success'); ?>

        </div>



 </div>
<h1>Assign Roles to User <?php echo $model->email; ?></h1>

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
                    'urlExpression'=>'array("revokeRole","id"=>'.$model->id.',"role"=>$data["id"])',
                    'labelExpression'=>'"revoke"'
                )
            )
        ));
?>
<h2 style="float:left;">Available Roles</h2>
<?
$this->widget('zii.widgets.grid.CGridView', array(
            'dataProvider'=>$availableroles,
            'id'=>'roles',
            'template'=>'{items}',
            'emptyText'=>Rights::t('core', 'This user has not been assigned any items.'),
            'htmlOptions'=>array('class'=>'grid-view user-assignment-table mini'),
            'columns'=>array(
                'tenant',
                'role',
                array(
                    'class'=>'CLinkColumn',
                    'urlExpression'=>'array("assignRole","id"=>'.$model->id.',"role"=>$data["id"])',
                    'labelExpression'=>'"assign"'
                )
            ),
            'emptyText'=>'No roles Available for Assignment'
        ));
?>

