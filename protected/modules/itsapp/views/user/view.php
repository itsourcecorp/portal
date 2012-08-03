<?php 
$this->breadcrumbs=array(
	'Users'=>array('index'),
	$model->id,
);
if(isset(Yii::app()->params['tenant'])){
$this->menu=array(
    array('label'=>'List User', 'url'=>array('index','tenant'=>Yii::app()->tenant->id)),
    array('label'=>'Create User', 'url'=>array('create')),
    array('label'=>'Update User Profile', 'url'=>array('update', 'id'=>$model->id)),
    array('label'=>'Assign User Roles', 'url'=>array('assignRoles', 'id'=>$model->id)),
    //array('label'=>'Edit Roles', 'url'=>array('editRoles', 'id'=>$model->id)),
    array('label'=>'Delete User', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
);
}else{
$this->menu=array(
	array('label'=>'List User', 'url'=>array('index')),
	array('label'=>'Create User', 'url'=>array('create')),
    array('label'=>'Update User Profile', 'url'=>array('update', 'id'=>$model->id),'htmlOptions'=>array('visible'=>true)),
    array('label'=>'Assign User Roles', 'url'=>array('assignRoles', 'id'=>$model->id)),
    array('label'=>'Edit Roles', 'url'=>array('editRoles', 'id'=>$model->id)),
	array('label'=>'Delete User', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
);
}
?>
<h1>View User <?php echo $model->email; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		//'group',
		//'managed_by',
		'firstname',
		'lastname',
		'email',
		//'password',
		'phone',
		//'mobile',
		'created',
		//'cookie',
		//'session',
		//'ip',
		//'hardware_id',
		array('name'=>'security_question','type'=>'Html'),
		'security_answer',
		'status',
		'ext',
		'picture_id',
		'notification',
	),
)); 
?>
</br>
<h2>Roles</h2>

<?
$this->widget('zii.widgets.grid.CGridView', array(
            'dataProvider'=>$dataprovider,
            'id'=>'roles',
            'template'=>'{items}',
            'emptyText'=>Rights::t('core', 'This user has not been assigned any items.'),
            'htmlOptions'=>array('class'=>'grid-view user-assignment-table mini'),
            'columns'=>array(
                'tenant',
                'role'
                
            )
        ));

?>
