
  <div id="myslidemenu" class="jqueryslidemenu"> 
    <?php 
    $menuarray =  array(
            array('label'=>'Home', 'url'=>array('/site/index')),
            array(
            'label'=>Rights::t('core', 'Assignments'),
            'url'=>array('assignment/view'),
            'itemOptions'=>array('class'=>'item-assignments'),
        ),
        array(
            'label'=>Rights::t('core', 'Roles'),
            'url'=>array('authItem/roles'),
            'itemOptions'=>array('class'=>'item-roles'),
        ),
        array(
            'label'=>Rights::t('core', 'Tasks'),
            'url'=>array('authItem/tasks'),
            'itemOptions'=>array('class'=>'item-tasks'),
        ),
        array(
            'label'=>Rights::t('core', 'Operations'),
            'url'=>array('authItem/operations'),
            'itemOptions'=>array('class'=>'item-operations'),
        ),
                    array('label'=>'User Management', 'url'=>array('/user/admin'), 'visible'=>Yii::app()->user->checkAccess('userManagement')),

            array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
            array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
            
        );
    

    $this->widget('zii.widgets.CMenu',array(
        'items'=>$menuarray,
    )); ?>
<div style="clear: both;"></div>
</div>

