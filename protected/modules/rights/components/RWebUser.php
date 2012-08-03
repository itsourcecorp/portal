<?php
/**
* Rights web user class file.
*
* @author Christoffer Niska <cniska@live.com>
* @copyright Copyright &copy; 2010 Christoffer Niska
* @since 0.5
*/
class RWebUser extends CWebUser
{
	/**
	* Actions to be taken after logging in.
	* Overloads the parent method in order to mark superusers.
	* @param boolean $fromCookie whether the login is based on cookie.
	*/
	public function afterLogin($fromCookie)
	{
		parent::afterLogin($fromCookie);

		// Mark the user as a superuser if necessary.
		if( Rights::getAuthorizer()->isSuperuser($this->getId())===true )
			$this->isSuperuser = true;
	}

	/**
	* Performs access check for this user.
	* Overloads the parent method in order to allow superusers access implicitly.
	* @param string $operation the name of the operation that need access check.
	* @param array $params name-value pairs that would be passed to business rules associated
	* with the tasks and roles assigned to the user.
	* @param boolean $allowCaching whether to allow caching the result of access checki.
	* This parameter has been available since version 1.0.5. When this parameter
	* is true (default), if the access check of an operation was performed before,
	* its result will be directly returned when calling this method to check the same operation.
	* If this parameter is false, this method will always call {@link CAuthManager::checkAccess}
	* to obtain the up-to-date access result. Note that this caching is effective
	* only within the same request.
	* @return boolean whether the operations can be performed by this user.
	*/
	public function checkAccess($operation, $params=array(), $allowCaching=true)
	{
		// Allow superusers access implicitly and do CWebUser::checkAccess for others.
		return $this->isSuperuser===true ? true : parent::checkAccess($operation, $params, $allowCaching);
	}

	/**
	* @param boolean $value whether the user is a superuser.
	*/
	public function setIsSuperuser($value)
	{
		$this->setState('Rights_isSuperuser', $value);
	}

	/**
	* @return boolean whether the user is a superuser.
	*/
	public function getIsSuperuser()
	{
		return $this->getState('Rights_isSuperuser');
	}
	
	/**
	 * @param array $value return url.
	 */
	public function setRightsReturnUrl($value)
	{
		$this->setState('Rights_returnUrl', $value);
	}
	
	/**
	 * Returns the URL that the user should be redirected to 
	 * after updating an authorization item.
	 * @param string $defaultUrl the default return URL in case it was not set previously. If this is null,
	 * the application entry URL will be considered as the default return URL.
	 * @return string the URL that the user should be redirected to 
	 * after updating an authorization item.
	 */
	public function getRightsReturnUrl($defaultUrl=null)
	{
		if( ($returnUrl = $this->getState('Rights_returnUrl'))!==null )
			$this->returnUrl = null;
		
		return $returnUrl!==null ? CHtml::normalizeUrl($returnUrl) : CHtml::normalizeUrl($defaultUrl);
	}
    public function getAssignments(){
        $tenants = TenantAssignment::model()->findAllByAttributes(array('user_id'=>$this->id));
        $result = array();
        foreach($tenants as $tenant){
            $result[] = $tenant->tenant_id;
        }
        return $result;
    }
    public function getAdminTenantsArray(){
        $managedtenants = array();
        foreach(Yii::app()->authManager->getAuthItems(2,Yii::app()->user->id) as $key => $item){
            $level = explode('.',$key);
            if(count($level)>1 AND $level[1]==='Admin' || $level[1]==='Root'){
               $managedtenants[]=$level[0];
            }
        }
        return array_filter($managedtenants, 'strlen');
    }
    public function getAdminTenants(){
        $managedtenants = array();
        foreach(Yii::app()->authManager->getAuthItems(2,Yii::app()->user->id) as $key => $item){
            $level = explode('.',$key);
            if(count($level)>1 AND ($level[1]==='Admin' || $level[1]==='Root')){
               $tenant = Tenants::model()->findByPk($level[0]);
               if(!empty($tenant))
                $managedtenants[$tenant->id]=$tenant;
            }
        }
        return array_filter($managedtenants);
    } 
    public function getAdminTenantNames(){
        $managedtenants = array();
        foreach(Yii::app()->authManager->getAuthItems(2,Yii::app()->user->id) as $key => $item){
            $level = explode('.',$key);
            if(count($level)>1 AND ($level[1]==='Admin' || $level[1]==='Root')){
               $tenant = Tenants::model()->findByPk($level[0]);
                if(!empty($tenant))
                    $managedtenants[$tenant->name]=$tenant->name;
            }
        }
        return array_filter($managedtenants);
    }
    public function getAdminRoles(){
        $tenants = $this->getAdminTenants();
        if(empty($tenants)){
            return array();
        }
        $roles = array();
        $connection = Yii::app()->db;
        $query = 'SELECT * FROM AuthItem WHERE type = 2 AND (';
        foreach($tenants as $tenant){
            $query.= 'name LIKE "'.$tenant->id.'.%" OR ';
        }
        $query = substr($query,0,-3);
        $query.=')';
        $command = $connection->createCommand($query);
        return $command->queryAll();
        
    }
    public function getCheckAdminRole($role){
        $tenants = $this->getAdminTenants();
        if(empty($tenants)){
            return false;
        }
        $rolearray = explode('.',$role->name);
        foreach($tenants as $tenant){
            if($tenant->id==$rolearray[0])
             return true;
        }   
        return false;
        
    }
    /**
    * Return the available actions from the modules this user is the admin of 
    * 
    */
    public function getAvailableActions($role=Null){
        $admintenants = $this->adminTenants;
        $availableactions = array();
        $auth = Yii::app()->authManager;
        $roleitem = $auth->getAuthItem($role);
        $children = array();
        if(!empty($roleitem)){
            $children = $auth->getItemChildren($roleitem->name);
            $rolearray = explode('.', $roleitem->name);
        }
        $childarray=array();
         foreach($children as $child){
            $childarray[$child->name]=true;
         }
        if(isset($rolearray[0]) AND is_numeric($rolearray[0]))
            $roletenant = $rolearray[0];
            
        foreach($admintenants as $tenant){
            if(isset($roletenant) AND $roletenant != $tenant->id)
                continue;
            $modules = $tenant->getRelated('modules');
            foreach($modules as $module){
                try{
                    $actionarray = array('tenant'=>$tenant->name,'id'=>$tenant->id.'.Itsapp.'.ucfirst($module->module_name).'.*','module'=>ucfirst($module->module_name),'controller'=>'All Controllers','action'=>'All Actions');
                    if(!isset($childarray[$actionarray['id']]))
                        $availableactions[]=$actionarray;
                    $controllers = Yii::app()->metadata->getControllers($module->module_name);
                    //echo $module->module_name.': </br>';
                    foreach($controllers as $controller){
                         $controllername = explode('Controller',$controller);
                         $controllername = $controllername[0];
                        $actionarray = array('tenant'=>$tenant->name,'id'=>$tenant->id.'.Itsapp.'.ucfirst($module->module_name).'.'.$controllername.'.*','module'=>ucfirst($module->module_name),'controller'=>$controllername,'action'=>'All Actions');
                        if(!isset($childarray[$actionarray['id']]))
                            $availableactions[]=$actionarray;
                        try{
                            //echo $controller.": ";
                            $actions =Yii::app()->metadata->getActions($controller,$module->module_name);
                            foreach($actions as $action){
                                            $actionarray = array('tenant'=>$tenant->name,'id'=>$tenant->id.'.Itsapp.'.ucfirst($module->module_name).'.'.$controllername.'.'.$action,'module'=>ucfirst($module->module_name),'controller'=>$controllername,'action'=>$action);
                                            if(!isset($childarray[$actionarray['id']]))
                                                $availableactions[]=$actionarray;
                                        }
                            }catch(Exception $e){
                        }    
                    }
                    
                }catch(Exception $e){
                    //echo 'no actions for module</br>';
                }
            }
        }
        return $availableactions;
    }
    public function getAvailableRoles($tenant = NULL){
        $admintenants = Yii::app()->user->adminTenants;
        $roles = Yii::app()->authManager->getAuthItems(2);
        $tenantroles = array();
        foreach($roles as $tenantrole){
            $rolearray = explode('.', $tenantrole->name);
            if(count($rolearray)>1 AND is_numeric($rolearray[0])){
                if(isset($tenant)){
                  if(!empty($admintenants[$rolearray[0]]) AND $rolearray[0]==$tenant->id)
                    $tenantroles[]=array('role'=>$rolearray[1],'id'=>$tenantrole->name,'tenant'=>$admintenants[$rolearray[0]]->name);   
                }else{
                  if(!empty($admintenants[$rolearray[0]]))
                    $tenantroles[]=array('role'=>$rolearray[1],'id'=>$tenantrole->name,'tenant'=>$admintenants[$rolearray[0]]->name);
                }
            }
        } 
        return $tenantroles;
    }
    /*
    *  Return the child items that this user is the admin of
    */
    public function getAdminChildItems($role,$type){
        $admintenants = $this->adminTenants;
        $roles = Yii::app()->authManager->getItemChildren($role);
        $childrenroles = array();
        $childactions = array();
        
        foreach($roles as $role){
            $rolearray = explode('.', $role->name);
            if(count($rolearray)>1 AND is_numeric($rolearray[0])){
                if($role->type==2){
                    if(!empty($admintenants[$rolearray[0]])){
                        $childrenroles[]=array('role'=>$rolearray[1],'id'=>$role->name,'tenant'=>$admintenants[$rolearray[0]]->name);
                    }
                }else{
                    if(!empty($admintenants[$rolearray[0]])){
                        $actionarray = array('tenant'=>$admintenants[$rolearray[0]]->name,'id'=>$role->name);
                        if(isset($rolearray[2]))
                            $actionarray['module']= $rolearray[2];
                        if(isset($rolearray[3])){
                            if($rolearray[3]=='*'){
                                $actionarray['controller']= 'all';
                            }else{
                                $actionarray['controller']= $rolearray[3];
                            }
                        }
                        if(isset($rolearray[4])){
                            if($rolearray[4]=='*'){
                                $actionarray['action']= 'All Actions';
                            }else{
                                $actionarray['action']= $rolearray[4];
                            }
                        }elseif($actionarray['controller']== 'All Actions') {
                                $actionarray['action']= 'All Controllers';
                        }
                        
                        $childactions[]=$actionarray;
                    }
                }
            }
        }
        if($type==2){
            return $childrenroles;
        }elseif($type==0){
            return $childactions;
        }
    }
     public function accessChecker($tenant,$controllerid,$controller,$action){
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
}
