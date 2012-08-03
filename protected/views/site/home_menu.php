<div id="myslidemenu" class="jqueryslidemenu"> 
    <?php 
    
        
        $menuarray =  array(
                array('label'=>'Home', 'url'=>array('/itsapp'),'htmlOptions'=>array('visible'=>true)),
                
            );
            
    $companyarray = array(array('label'=>'Companies','visible'=>true, 'url'=>array('/itsapp/'),
                            'items'=>(!Yii::app()->user->isGuest && !Yii::app()->user->isSuperuser &&isset($this->module)) ? $this->module->tenantMenu : array(),            
                ));
    if(isset(Yii::app()->params['tenant']))
    {
        $menuarray = array_merge($menuarray,$companyarray);
        $menuarray = array_merge($menuarray,$this->module->menu ); 
    }else{
        $menuarray = array_merge($menuarray,(!Yii::app()->user->isGuest && !Yii::app()->user->isSuperuser &&isset($this->module)) ? $this->module->tenantMenu : array() );
        if(!empty(Yii::app()->user->adminTenants)){
            $menuarray = array_merge($menuarray, array(array('label'=>'User Managment','url'=>array('/itsapp/user'),'visible'=>true,'items'=>array(array('label'=>'Role Managment','url'=>array('/itsapp/user/editRoles'),'visible'=>true)))));
        }else{
            $menuarray = array_merge($menuarray, array(array('label'=>'User Profile','url'=>array('/itsapp/user/'.Yii::app()->user->id),
            'htmlOptions'=>array('visible'=>true)))) ;
        }
    }
    $menuarray = array_merge($menuarray,array(
                array('label'=>'Rights Management', 'url'=>array('/rights'), 'visible'=>Yii::app()->user->checkAccess('userManagement')),
                array('label'=>'User Management', 'url'=>array('/user'), 'visible'=>Yii::app()->user->checkAccess('userManagement')),
                array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
                array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)));
    $this->widget('application.extensions.portal.EFilterMenu',array(
        'items'=>$menuarray,
    )); ?>
<div style="clear: both;"></div>
</div>
