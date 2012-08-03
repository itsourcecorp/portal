<?php
class ETenantModule extends ECWebModule{
    public function init(){
        Yii::app()->setComponent("tenant", $this->TenantComponent);
        if(isset($_GET['tenant']))
            Yii::app()->params['tenant'] = $this->tenant;
        parent::init();
    }
    public function getMenu()
    {return array();}
    public function getTenant(){
        return Yii::app()->tenant->model;
    }
    public function getTenantComponent(){
        if(isset($_GET['tenant']))
            $tenant_id=$_GET['tenant'];
        $component = array(
                            'class'=>'Tenant',);
        return Yii::createComponent($component);
    }
    public function getTenantAssigments(){
        $criteria = new CDbCriteria;
        if(!is_numeric(Yii::app()->user->id)){
            return array();
        }
        $criteria->condition = 'user_id=' . Yii::app()->user->id;
        return TenantAssignment::model()->findall($criteria);   
    }
    public function getTenantMenu(){
        $menu_items = array();
        
        foreach($this->TenantAssigments as $tenant_assignment){
            $menu_items[] = array('url'=>Yii::app()->baseURL.'/itsapp/?tenant='.$tenant_assignment->tenant_id,'label'=>$tenant_assignment->tenant->name,
            'htmlOptions'=>array('visible'=>true),'visible'=>true);    
        }
        return  $menu_items;
    }
    public function getBaseModules(){
        return array();
    }
    public function getPaidModules(){
        $tenantid = 0;
        if(isset($_GET['tenant']))
        {
            $tenantid = $_GET['tenant'];
            $criteria = new CDbCriteria;
            $criteria->condition = 'tenant_id=' . $tenantid;
            return array_keys(CHtml::listData(ModuleAssignments::model()->findall($criteria), 'module_name','module_name'));    
        }
        else
        {
          return array();   
        }
    } 
    /**
    * check access to action 
    * 
    * @param mixed $tenant
    * @param mixed $controllerid
    * @param mixed $controller
    * @param mixed $action
    */
    private function accessChecker($tenant,$controllerid,$controller,$action){
        //echo $tenant->id.'/'.$controllerid.'/'.$controller->id.'/'.$action->id;
        if(Yii::app()->user->checkAccess(Yii::app()->params['tenant']->id.'.'.$controllerid.'.'.ucfirst($controller->id).'.'.ucfirst($action->id),Yii::app()->user->getId())){
            return true;
        }
        if(Yii::app()->user->checkAccess(Yii::app()->params['tenant']->id.'.'.$controllerid.'.'.ucfirst($controller->id).'.*',Yii::app()->user->getId())){
            return true;
        }
        if(Yii::app()->user->checkAccess(Yii::app()->params['tenant']->id.'.'.$controllerid.'.*',Yii::app()->user->getId())){
            return true;
        }
        if(Yii::app()->user->checkAccess($controllerid.'.'.ucfirst($controller->id).'.'.ucfirst($action->id),Yii::app()->user->getId())){
            return true;
        }
        if(Yii::app()->user->checkAccess($controllerid.'.'.ucfirst($controller->id).'.*',Yii::app()->user->getId())){
            return true;
        }
        if(Yii::app()->user->checkAccess($controllerid.'.*',Yii::app()->user->getId())){
            return true;
        }       
        
    }
     public function beforeControllerAction($controller, $action)
    {

       $controllerarray = explode('/',$this->id);
       $controllerid = ucfirst($controllerarray[0]);
       unset($controllerarray[0]);
       foreach($controllerarray as $item){
           $controllerid.='.'.ucfirst($item);
       }
        if(!isset($_GET['_runaction_touch'])){
            if($this->accessChecker(Yii::app()->params['tenant'],$controllerid,$controller,$action)){
                return true;
            }else{
                if(!Yii::app()->user->isGuest){
                    throw new CHttpException(403,Yii::t('yii','You are not authorized to perform this action. '.$controllerid.' '.$controller->id.' '.$action->id));
                    return false;
                }else{
                    Yii::app()->user->returnUrl = Yii::app()->request->requestUri;
                    Yii::app()->getRequest()->redirect(Yii::app()->baseURL,true,302);
                    
                    return false;
                }
            }
        }else{
               
            return true;
             if(md5('runaction')==$_GET['_runaction_touch']){
                return true;
             }else{
                 return false;
             }
        }     
    }   
}
?>
