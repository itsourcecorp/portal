<?php

class UserController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/home_column2';

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
       /*
        $connection =Yii::app()->db;
        $command = $connection->createCommand('SELECT * FROM _yii_tenants');
        $tenants = $command->queryAll();
        $command = $connection->createCommand("SELECT * FROM `AuthItem` WHERE type = 0 AND name NOT REGEXP '[0-9]' AND name REGEXP '[\*]' AND name != 'Itsapp.User.*'");
        $items = $command->queryAll();
        $auth = Yii::app()->authManager;
        //foreach($items as $item){
            foreach($tenants as $tenant){
                //$auth->createAuthItem($tenant['id'].'.Itsapp.Telecom.Customers.Index',0,$tenant['name'].' Product Index');
                $auth->addItemChild($tenant['id'].'.User',$tenant['id'].'.Itsapp.Default.*');
            }
        //}
        die();
       */
        $criteria = new CDbCriteria;
        $criteria->condition = 'user_id=' . Yii::app()->user->id;
        $tenantassignment =  TenantAssignment::model()->findall($criteria);
        $temp = array();
        foreach($tenantassignment as $ten){
            $temp[$ten->tenant->id] = $ten->tenant->name; 
        }
        $tenantassignment = $temp;
        $admintenants = Yii::app()->user->adminTenants;
        $roles = Yii::app()->authManager->getAuthItems(2,$id);
        $tenantroles = array();
        foreach($roles as $role){
            $rolearray = explode('.', $role->name);
            if(count($rolearray)>1 AND is_numeric($rolearray[0])){
                if(!empty($admintenants[$rolearray[0]])){
                    $tenantroles[]=array('role'=>$rolearray[1],'id'=>$role->name,'tenant'=>$admintenants[$rolearray[0]]->name);
                }elseif($id == Yii::app()->user->id AND isset($tenantassignment[$rolearray[0]])){
                    $tenantroles[]=array('role'=>$rolearray[1],'id'=>$role->name,'tenant'=>$tenantassignment[$rolearray[0]]);
                }
            }
        }
        
        $dataprovider = new CArrayDataProvider($tenantroles,array('id'=>'id'));
        $assignments = new TenantAssignment('search');
        $assignments->unsetAttributes();
        $assignments->user_id = $id;
		$this->render('view',array(
			'model'=>$this->loadModel($id), 'roles'=>$tenantroles,'dataprovider'=>$dataprovider
		));
	}
    /**
     * Update user's roles'.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionAssignRoles($id)
    {
        $availableroles = array();
        $admintenants = Yii::app()->user->adminTenants;
        $roles = Yii::app()->authManager->getAuthItems(2,$id);
        $aroles =Yii::app()->authManager->getAuthItems(2);
        /*/
        foreach($roles as $arole){
            $arolearray = explode('.', $arole['name']);
            if(count($arolearray)>1 AND is_numeric($arolearray[0])){
                if(!empty($admintenants[$arolearray[0]])){
                    $availableroles[]=array('role'=>$arolearray[1],'id'=>$arole['name'],'tenant'=>$admintenants[$arolearray[0]]->name);
                }
            }
        }
        */  
        foreach($aroles as $role){
            $rolearray = explode('.', $role->name);
            if(count($rolearray)>1 AND is_numeric($rolearray[0])){
                if(!empty($admintenants[$rolearray[0]]))
                    $availableroles[]=array('role'=>$rolearray[1],'id'=>$role->name,'tenant'=>$admintenants[$rolearray[0]]->name);
            }
        }
        $tenant = array();
        foreach ($availableroles as $key => $row) {
            $tenant[$key] = $row['tenant'];
        }
        if(!empty($availableroles))
        array_multisort($tenant,SORT_ASC,$availableroles);
           
        $tenantroles = array();
        foreach($roles as $role){
            $rolearray = explode('.', $role->name);
            if(count($rolearray)>1 AND is_numeric($rolearray[0])){
                if(!empty($admintenants[$rolearray[0]])){
                    $tenantroles[]=array('role'=>$rolearray[1],'id'=>$role->name,'tenant'=>$admintenants[$rolearray[0]]->name);
                    foreach($availableroles as $key => $aroles){
                          if($aroles['id'] === $role->name){
                              unset($availableroles[$key]);
                          }     
                    }
                }
            }
        }       
        foreach($availableroles as $key => $role){
            if(!Yii::app()->user->checkAccess($role['id']) && $role['role']=='Root'){
              unset($availableroles[$key]);
            }
        }
        $tenant = array();
        foreach ($tenantroles as $key => $row) {
            $tenant[$key] = $row['tenant'];
        }
        if(!empty($tenantroles))
        array_multisort($tenant,SORT_ASC,$tenantroles);
        $dataprovider = new CArrayDataProvider($tenantroles,array('id'=>'id','pagination'=>array('pageSize'=>count($tenantroles))));
        $availableroles = new CArrayDataProvider($availableroles,array('id'=>'id','pagination'=>array('pageSize'=>count($availableroles))));
        $this->render('updateRoles',array(
            'model'=>$this->loadModel($id), 'dataprovider'=>$dataprovider, 'availableroles'=>$availableroles
        ));
    }
    /**
    * put your comment there...
    * 
    * @param mixed $id
    * @param mixed $role
    */
    public function actionRevokeRole($id,$role){
        
        $user = $this->loadModel($id);
        $tenant = explode('.',$role);
        
        $tenant = $tenant[0];
        $tenant = Tenants::model()->findByPK($tenant);
        $tenantassignment = TenantAssignment::model()->findByAttributes(array('user_id'=>$id,'tenant_id'=>$tenant->id)); 
        if($role!=$tenant->id.'.Root'){
            Yii::app()->authManager->revoke($role, $_GET['id']); 
            Yii::app()->user->setFlash('Success','Role Revoked');
        }else{
            Yii::app()->user->setFlash('Error','Only System Superuser can revoke Root privledges');
        }
        $tenantroles = $user->getTenantRoles($tenant->id);
        if(empty($tenantroles) AND !empty($tenantassignment) AND $role!=$tenant->id.'.Root'){
            $tenantassignment->delete();            
        }

         $this->redirect("../AssignRoles/$id");
    }
     /**
    * put your comment there...
    * 
    * @param mixed $id
    * @param mixed $role
    */
    public function actionAddExistingUser(){
        $user =  new User();
        if(isset($_GET['user']))
            $user->email = $_GET['user'];
        $admintenants = array();
        $admintenants = Yii::app()->user->adminTenants;
        foreach($admintenants as $tenants){
           $tenantselect[$tenants->id] = $tenants->name; 
        }
        if(isset($_POST['User'])  AND array_key_exists($_POST['Tenant'],$tenantselect)){
            $tenant = Tenants::model()->findByPK($_POST['Tenant']);
            $user = User::model()->findByAttributes(array('email'=>$_POST['User']['email']));
            if(empty($user)){
                $user = new User();
                $user->addError('email','No user found with this email');                
            }else{
                $assignment = new TenantAssignment();
                $assignment->tenant_id = $tenant->id;
                $assignment->user_id = $user->id;
                $oldassignment = TenantAssignment::model()->findByAttributes(array('user_id'=>$user->id,'tenant_id'=>$tenant->id));
                if(empty($oldassignment)){
                    if($assignment->save()){
                        Yii::app()->authManager->assign($tenant->id.'.User',$user->id);
                        $this->redirect('index');
                    }
                }else{
                    $user->addError('email','User already Assigned to this tenant');
                }
            }
            $this->render('addExistingUser',array(
                    'model'=>$user, 'tenants'=>$tenantselect, 'tenant'=>$_POST['Tenant']
                ));
        }else{
          $this->render('addExistingUser',array(
            'model'=>$user, 'tenants'=>$tenantselect, 'tenant'=>0
        ));
        }
    }
    /**
    * put your comment there...
    * 
    * @param mixed $id
    * @param mixed $role
    */
    public function actionAssignRole($id,$role){
        $authitem = Yii::app()->authManager->getAuthItem($role);
        if(empty($authitem))
            throw new CHttpException(400,'Auth Item does not exist');
        if(!Yii::app()->user->getCheckAdminRole($authitem))
            throw new CHttpException(400,'Auth Item does not belong to you');

        
            
            $tenant = explode('.',$role);
            $tenant = $tenant[0];
            $tenant = Tenants::model()->findByPK($tenant);
        if($role!=$tenant->id.'.Root'){
            Yii::app()->user->setFlash('Success','Role Assigned');
            $tenantassignment = new TenantAssignment();
            $tenantassignment->user_id = $id;
            $tenantassignment->tenant_id=$tenant->id;
            $oldassignment = TenantAssignment::model()->findByAttributes(array('user_id'=>$id,'tenant_id'=>$tenant->id));
            if(empty($oldassignment))
                $tenantassignment->save();
            if(!empty($tenant)){
                Yii::app()->authManager->assign($role, $_GET['id']);
            }
        }else{
            Yii::app()->user->setFlash('Error','Only System Superuser can assign Root privledges');
        }
         $this->redirect("../AssignRoles/$id");
    }
    /**
     * Update user's roles'.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionEditRoles()
    {
        
       $admintenants = Yii::app()->user->adminTenants;
        $roles = Yii::app()->authManager->getAuthItems(2);
        $tenantroles = array();
        foreach($roles as $role){
            $rolearray = explode('.', $role->name);
            if(count($rolearray)>1 AND is_numeric($rolearray[0])){
                if(!empty($admintenants[$rolearray[0]]))
                    $tenantroles[]=array('role'=>$rolearray[1],'id'=>$role->name,'tenant'=>$admintenants[$rolearray[0]]->name);
            }
        }
       
        $dataprovider = new CArrayDataProvider($tenantroles,array('id'=>'id'));
        $this->render('editRoles',array('dataprovider'=>$dataprovider));
        
    } 
    /**
     * Update user's roles'.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionEditRole($role)
    {
        $filtersForm=new FiltersForm;
        if(isset($_GET['FiltersForm']) AND $_GET['ajax']=='availableactions')
            $filtersForm->filters=$_GET['FiltersForm'];
        $filtersForm2=new FiltersForm;
        if(isset($_GET['FiltersForm']) AND $_GET['ajax']=='roles')
            $filtersForm2->filters=$_GET['FiltersForm'];
        $filtersForm3=new FiltersForm;
        if(isset($_GET['FiltersForm']) AND $_GET['ajax']=='childactions')
            $filtersForm3->filters=$_GET['FiltersForm'];
        $filtersForm4=new FiltersForm;
        if(isset($_GET['FiltersForm']) AND $_GET['ajax']=='availableroles')
            $filtersForm4->filters=$_GET['FiltersForm'];
        
        $user = Yii::app()->user->id;
        $rolearray = explode('.',$role);
        $model = implode('.',array_slice($rolearray,1));        
        if(isset($rolearray[0]) AND is_numeric($rolearray[0]))
            $roletenant = Tenants::model()->findByPk($rolearray[0]);        
        if(empty($roletenant))
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
         
          
        $childrenroles = Yii::app()->user->getAdminChildItems($role,2);
        $childactions = Yii::app()->user->getAdminChildItems($role,0);
       
        $availableactions = Yii::app()->user->getAvailableActions($role);
        
        
        $tenantroles = Yii::app()->user->getAvailableRoles($roletenant);        
        $rolearray = array();
        foreach($childrenroles as $trole){
            $rolearray[]=$trole['id'];
        }
        foreach($tenantroles as $key=>$trole){
            if($trole['role']=='Root'){
                unset($tenantroles[$key]);
            }elseif($trole['id']==$role){
                unset($tenantroles[$key]);
            }elseif(in_array($trole['id'],$rolearray)){
                unset($tenantroles[$key]);
            }
            
        }
        
        $dataprovider = new CArrayDataProvider($childrenroles,array('keyField'=>'id','id'=>'id','pagination' =>array('pageSize'=>10)));
        $actions = new CArrayDataProvider($childactions,array('keyField'=>'id','id'=>'id','pagination' => array('pageSize'=>10)));
        $availableactions = new CArrayDataProvider($availableactions,array('keyField'=>'id','id'=>'id','pagination' => array('pageSize'=>10)));
        $tenantroles = new CArrayDataProvider($tenantroles,array('keyField'=>'id','id'=>'id','pagination' => array('pageSize'=>10)));
        if(isset($_GET['ajax'])){
           if($_GET['ajax']=='availableactions'){
            $this->renderPartial('_availableactions',array('availableactions'=>$availableactions,'filtersForm'=>$filtersForm,'model'=>$role),false,true);
           }
           
           if($_GET['ajax']=='roles'){
            $this->renderPartial('_childroles',array('dataprovider'=>$dataprovider,'model'=>$role,'filtersForm2'=>$filtersForm2),false,true);
           }
           if($_GET['ajax']=='availableroles'){
            $this->renderPartial('_availableroles',array('actions'=>$tenantroles,'model'=>$role,'filtersForm4' => $filtersForm4),false,true);
           }
           if($_GET['ajax']=='childactions'){
            $this->renderPartial('_childactions',array('actions'=>$actions,'model'=>$role,'filtersForm3' => $filtersForm3),false,true);
           }
           return;
        }
            
        
        if(!isset($_GET['ajax']))
        $this->render('editRole',array(
                            'model'=>$model,
                            'role'=>$role,
                            'roletenant'=>$roletenant,
                            'dataprovider'=>$dataprovider,
                            'actions'=>$actions,
                            'availableroles'=>$tenantroles,
                            'availableactions'=>$availableactions,
                            'filtersForm' => $filtersForm,
                            'filtersForm2' => $filtersForm2,
                            'filtersForm3' => $filtersForm3,
                            'filtersForm4' => $filtersForm4,
                            ),false
        );
        
    }
    /**
    * Update the name and description of a role
    * 
    * @param mixed $role
    */
    public function actionUpdateRole($role){
        $rolearray = explode('.',$role);
        $name = implode('.',array_slice($rolearray,1));        
        $namecheck = implode('.',array_slice($rolearray,1,1));  
        
              
        if(isset($rolearray[0]) AND is_numeric($rolearray[0]))
            $roletenant = Tenants::model()->findByPk($rolearray[0]);   
        $model = Yii::app()->authManager->getAuthItem($role);
        $formModel = new AuthItemForm();
        if( isset($_POST['AuthItemForm'])===true )
        {
            $formModel->attributes = $_POST['AuthItemForm'];
            $nameholder = $formModel->name;
            if(!($formModel->name != $namecheck AND in_array($namecheck, array('User','Root','Admin')))){
                if($nameholder!=''){
                $formModel->name = $roletenant->id.'.'.$formModel->name;
                if( $formModel->validate()===true )
                {
                    // Update the item and load it
                    $this->updateAuthItem($role, $formModel->name, $formModel->description, $formModel->bizRule, $formModel->data);
                    $item = Yii::app()->authManager->getAuthItem($formModel->name);

                    // Set a flash message for updating the item
                    Yii::app()->user->setFlash('Success','Role Updated');

                    // Redirect to the correct destination
                    $this->redirect(array('editRole','role'=>$model->name));
                }
                }
                 $formModel->name = $nameholder;
                 $formModel->addError('name','name cannot be blank');
            }else{
                $formModel->addError('name','Cannot Change names of the Default Roles(Root, User, Admin)');  
            }

        }
        $formModel->name = $name;
        $formModel->description = $model->description;
        $this->render('updateRole',array('role'=>$model,'model'=>$formModel,'name'=>$name,'roletenant'=>$roletenant));
    }
    /**
    * Updates an authorization item.
    * @param string $oldName the item name. This must be a unique identifier.
    * @param integer $name the item type (0: operation, 1: task, 2: role).
    * @param string $description the description for the item.
    * @param string $bizRule business rule associated with the item. This is a piece of
    * PHP code that will be executed when {@link checkAccess} is called for the item.
    * @param mixed $data additional data associated with the item.
    */
    private function updateAuthItem($oldName, $name, $description='', $bizRule=null, $data=null)
    {
        $authItem = Yii::app()->authManager->getAuthItem($oldName);
        $authItem->name = $name;
        $authItem->description = $description!=='' ? $description : null;
        $authItem->bizRule = $bizRule!=='' ? $bizRule : null;
        $connection = Yii::app()->db;
        //$q = 'UPDATE IGNORE AuthAssignment SET itemname='.$name.'WHERE itemname ='.$oldname;
        //$command = $connection->createCommand($q)->execute();
        

        Yii::app()->authManager->saveAuthItem($authItem, $oldName);
    }
    /**
    * Delete a role
    * 
    * @param mixed $role
    */
    public function actionDeleteRole($role){
        $role = Yii::app()->authManager->getAuthItem($role);
        if($role->type == 2){
            $rolename = explode('.',$role->name);
            if(count($rolename)>1){
                $rolename=$rolename[1];
            }
            if(in_array($rolename, array('User','Root','Admin'))){
                Yii::app()->user->setFlash("Error",'Cannot Create/Delete the Default Roles');
                $this->redirect('editRole?role='.$role->name);
            }
        }
        $rolename = explode('.',$role->name);
       
        if(empty($role))
                $this->redirect('editRoles');
        $privledge = Yii::app()->user->getCheckAdminRole($role);
        if($privledge){
            try{
                Yii::app()->authManager->removeAuthItem($role->name);
                Yii::app()->user->setFlash("Success",'Role Deleted');
                $this->redirect('editRoles');
            }catch(Exception $e){
                Yii::app()->user->setFlash("Error",$e->getMessage());
                $this->redirect('editRole?role='.$role->name);
            }
        }else{
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
        }
    }
    /**
    * Assign an item to be child to another item
    * 
    * @param mixed $parent
    * @param mixed $child
    * @param mixed $type
    */
    public function actionAddChildItem($parent,$child,$type){
        $response = array();
        if(!Yii::app()->request->isAjaxRequest){
           throw new CHttpException(400,'Bad Request');
        }
        if($parent===$child){
            $response['Message']=$child.' Cannot Assign Item To Itself';
                if($type==2){
                echo CJSON::encode($response);
            }else{
                echo CJSON::encode($response);
            }
        }else{
            $model = Yii::app()->authManager->getAuthItem($parent);
            if(empty($model)){
                $model = Yii::app()->authManager->createAuthItem($parent, $type,$parent,NULL, NULL);
            }
            $childName = urldecode($child);
            $childitem = Yii::app()->authManager->getAuthItem($child);
            if(empty($childitem)){
                $childitem = Yii::app()->authManager->createAuthItem($child, $type, $child,NULL, NULL);
            }
            if( $childName!==null && $model->hasChild($childName)==false ){
                try{
                    $model->addChild($childName);
                     $response['Message']=$child.' Child Assigned';
                }catch(Exception $e){
                    $response['Message']=$e->getMessage();
                }

            }else{
                 $response['Message']=$child.' Child Already Assigned';
            }
            // if AJAX request, we should not redirect the browser
            if(isset($_POST['ajax'])===false)
            if($type==2){
                
                echo CJSON::encode($response);
            }else{
                echo CJSON::encode($response);
                //$this->renderPartial('_availableactions',array('availableactions'=>$availableactions,'filtersForm'=>$filtersForm,'model'=>$role),false,true);
                //$this->redirect(array('editRole?role='.$parent.'#yw2_tab_1'));
            }
        }
    }
    /**
    * Create a role to assign items to
    * 
    */
    public function actionCreateRole(){
        $model = new AuthItemForm();
        $tenant = new Tenants();
        $admintenants = array();
        $admintenants = Yii::app()->user->adminTenants;
        foreach($admintenants as $tenants){
           $tenantselect[$tenants->id] = $tenants->name; 
        }

        if(isset($_POST['AuthItemForm']) AND isset($_POST['Tenant']) AND isset($tenantselect[$_POST['Tenant']])){
            $model->attributes = $_POST['AuthItemForm'];
            if(!in_array($model->name, array('User','Root','Admin','user','root','admin'))){
                if($model->name!=''){
                    $auth = Yii::app()->authManager;
                    $nameholder = $model->name;
                    $model->name = $_POST['Tenant'].'.'.$model->name;
                    if($model->validate()){
                        $auth->createAuthItem($model->name,2);
                        $this->redirect('editRole?role='.$model->name);

                    }else{
                        $model->name = $nameholder;
                    }
                }else{
                    $model->addError('name','Name Cannot Be Blank');
                }
            }else{
                $model->addError('name','Cannot Create A role with a default name (Root,Admin,User)');
            }
        }
        $this->render('createRole',array('tenants'=>$tenantselect,'model'=>$model,'tenant'=>$tenant));
    }
    /**
    * Remove parent child item relationship
    * 
    * @param mixed $parent
    * @param mixed $child
    */
    /**
    * Removes a child from an authorization item.
    */
    public function actionRemoveChildItem($parent,$child)
    {
        if(!Yii::app()->request->isAjaxRequest){
           throw new CHttpException(400,'Bad Request');
        }
            $itemName = urldecode($parent);
            $childName = urldecode($child);
            
            // Remove the child and load it
            Yii::app()->authManager->removeItemChild($itemName, $childName);
            $child = Yii::app()->authManager->getAuthItem($childName);

            // Set a flash message for removing the child
            $response['Message'] = $childName.' Child Removed';

            // If AJAX request, we should not redirect the browser
            if($child->type==0){
                                echo CJSON::encode($response);
                //$this->redirect(array('editRole?role='.$itemName.'#yw2_tab_1'));
            }else{
                                echo CJSON::encode($response);
                //$this->redirect(array('editRole?role='.$itemName));
            }
    }
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new User;
        $model->unsetAttributes();
		// Uncomment the following line if AJAX validation is needed
		 $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
            if(isset($model->password))
                
                $model->password= md5($model->password);
                $model->repeat_password= md5($model->repeat_password);
                $model->created = date( 'Y-m-d H:i:s',time());
			if($model->save())
				$this->redirect(array('addExistingUser', 'user'=>$model->email));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
        $oldmodel = $this->loadModel($id);
		// Uncomment the following line if AJAX validation is needed
		 $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
            if($model->password != $oldmodel->password){
                $model->password = md5($model->password);
                $model->repeat_password = md5($model->repeat_password);
            }else{
                $model->repeat_password = $model->password;
            }
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular users model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
            $criteria = new CDbCriteria;
            $criteria->condition = 'user_id=' . Yii::app()->user->id;
            $tenantassignment =  TenantAssignment::model()->findall($criteria);
            $temp = array();
            foreach($tenantassignment as $ten){
                $temp[$ten->tenant->id] = $ten->tenant->name; 
            }
            $tenantassignment = $temp;
            $admintenants = Yii::app()->user->adminTenants;
            $roles = Yii::app()->authManager->getAuthItems(2,$id);
            $tenantroles = array();
            foreach($roles as $role){
                $rolearray = explode('.', $role->name);
                if(count($rolearray)>1 AND is_numeric($rolearray[0])){
                    if(!empty($admintenants[$rolearray[0]])){
                        $tenantroles[]=array('role'=>$rolearray[1],'id'=>$role->name,'tenant'=>$admintenants[$rolearray[0]]->name);
                    }elseif($id == Yii::app()->user->id AND isset($tenantassignment[$rolearray[0]])){
                        $tenantroles[]=array('role'=>$rolearray[1],'id'=>$role->name,'tenant'=>$tenantassignment[$rolearray[0]]);
                    }
                }
            }
            foreach($tenantroles as $tenantrole){
                if(!in_array($tenantrole['role'],User::model()->specialRoles))
                    Yii::app()->authManager->revoke($tenantrole['id'], $id); 
            }
        //die();
        //$dataprovider = new CArrayDataProvider($tenantroles,array('id'=>'id'));
			// we only allow deletion via POST request
            if(!empty($tenantroles) || Yii::app()->user->id == $id){
                try{
			        $this->loadModel($id)->delete();
                }catch(Exception $e){
                    throw new CHttpException(400,'This User Cannot be deleted while he has roles in other tenants.');
                } 
            }   
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new TenantAssignment();
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['tenant'])){
            $model->tenant_id = Yii::app()->params['tenant']->id;
        }else{
            $model->tenant_id = Yii::app()->user->adminTenantsArray; 
        }    
        
        $this->render('index',array(
            'model'=>$model,
        ));
	}



	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=User::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
