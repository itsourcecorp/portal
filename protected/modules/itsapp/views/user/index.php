<?php

$this->breadcrumbs=array(
    'Users'=>array('index'),
    'Manage',
);

$this->menu=array(
    array('label'=>'List User', 'url'=>array('index')),
    array('label'=>'Create User', 'url'=>array('create')),
    array('label'=>'Add Existing User', 'url'=>array('addExistingUser')),
    array('label'=>'Edit Roles', 'url'=>array('editRoles')),
);
$tenant = '';
if(isset(Yii::app()->params['tenant']->id)){
    $tenant = ',"tenant"=>"'.Yii::app()->tenant->id.'"';
}
/*
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});
$('.search-form form').submit(function(){
    $.fn.yiiGridView.update('user-grid', {
        data: $(this).serialize()
    });
    return false;
});
");
*/
?>

<h1>Manage Users</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'user-grid',
    'dataProvider'=>$model->search(),
    //'filter'=>$model,
    'columns'=>array(
        array(
           'class'=>'CLinkColumn',
           'header'=>'User Name',
           'urlExpression'=>'array("view","id"=>$data->user_id'.$tenant.')',
           'labelExpression'=>'$data->user->email'
        ),
        'user.firstname',
        'user.lastname',
        /*
        'password',
        'phone',
        'mobile',
        'created',
        'cookie',
        'session',
        'ip',
        'hardware_id',
        'security_question',
        'security_answer',
        'status',
        'ext',
        'picture_id',
        'notification',
          */
        
       
    ),
)); ?>
