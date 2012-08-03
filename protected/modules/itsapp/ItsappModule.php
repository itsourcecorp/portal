<?php

class ItsappModule extends ETenantModule
{
	public function init()
	{   
        $this->setImport(array(
            'application.modules.itsapp.models.*',
            'application.modules.itsapp.components.*',
        ));
        $this->setModulePath(Yii::app()->basePath . '/modules');
        if(isset($_GET['tenant'])){
            $this->setModules( array_merge($this->BaseModules, $this->PaidModules));
        }else{
            $this->setModules( array_merge($this->BaseModules));    
        }
        parent::init();
	}         
    public function getMenu(){
        $menu = array();
        foreach($this->modules as $key =>$module){
                $module_menu =  $this->availableMenus[$key];
                $menu[] = $module_menu;    
        }
        return  $menu;
    }
    public function beforeControllerAction($controller, $action)
    {
       $tenants =Yii::app()->user->adminTenantsArray;
       $adminroles = Yii::app()->user->adminRoles;
       
       $user = Yii::app()->user;
       if(!$user->isGuest){
           //Any user can access the default app page
           if($controller->id == 'default')
             return true;
           //Rules for using the user controller are as follows
           if(ucfirst($controller->id) === 'User'){
                $authority = false;
               if($action->id==='view' OR $action->id ==='update'){
                   if(isset($_GET['id'])){
                        if($_GET['id']===$user->id){
                            return true;
                        }
                        foreach($tenants as $tenant){
                            $assignment = TenantAssignment::model()->findByAttributes(array('user_id'=>$_GET['id'],'tenant_id'=>$tenant));
                            if(!empty($assignment)){
                               $authority = true;
                            }
                            unset($assignment);
                        }
                        if($authority){
                            return true;
                        }else{
                            $controller->redirect(array('index'));
                            return false;
                        }
                        
                   }else{
                           $controller->redirect(array('index'));
                           return false;
                   }
               }else{
                 if(empty($tenants)){
                    throw new CHttpException(403,Yii::t('yii','You are not authorized to perform this action.'));
                 }else{
                     $authority = false;
                     if($action->id == 'editRole' || $action->id == 'deleteRole' || $action->id == 'updateRole' || $action->id == 'revokeRole' || $action->id=='assignRole'){
                        foreach($adminroles as $role){
                            if($role['name'] == $_GET['role']){
                               $authority = true;
                            }
                        }
                        if($authority){
                            return true;
                        }else{
                            throw new CHttpException(403,Yii::t('yii','You are not authorized to perform this action.'));
                        }
                     }elseif($action->id == 'removeChildItem' || $action->id == 'addChildItem' ){
                        foreach($adminroles as $role){
                            if($role['name'] == $_GET['parent']){
                               $authority = true;
                            }
                        }
                        if($authority){
                            return true;
                        }else{
                            throw new CHttpException(403,Yii::t('yii','You are not authorized to perform this action.'));
                        } 
                     }else{
                        return true;                         
                     }
                 }
                   
               }
           }
           
       }else{
           Yii::app()->user->returnUrl = Yii::app()->request->requestUri;
           Yii::app()->getRequest()->redirect(Yii::app()->baseUrl.'/site/login',true,302);
       }

        
    }
    public function getAvailableMenus(){
        return array(

                
                'tenantMgr'=>array('label'=>'Tenant Manager','url'=>array('/itsapp/tenantMgr/?tenant='.$this->tenant->id)),
                'admin'=>array('label'=>'System Manager','url'=>array('/itsapp/admin/?tenant='.$this->tenant->id)),
                
                //telecom module
                'telecom' =>array('label'=>'Telecom','url'=>array('/itsapp/telecom/?tenant='.$this->tenant->id),
                    'items'=>array(
                                    //customers
                                    array('label'=>'Customers', 'url'=>'#',
                                        'items'=>array(
                                            array('label'=>'Customer List','url'=>array("/itsapp/telecom/customers?tenant=".$this->tenant->id)),
                                            array('label'=>'Create Customer','url'=>array("/itsapp/telecom/customers/create?tenant=".$this->tenant->id)),
                                          ),
                                    ),
                                    //vendors
                                    array(
                                        'label'=>'Vendors','url'=>"#",
                                            'items'=>array(
                                                array('label'=>'Vendor List','url'=>array('/itsapp/telecom/vendors/?tenant='.$this->tenant->id)),
                                                array('label'=>'Create Vendor','url'=>array('/itsapp/telecom/vendors/create/?tenant='.$this->tenant->id)),
                                            ),
                                         ),
                                     //resource    
                                    array( 'label'=>'Resource','url'=>"#",'items'=>array(

                                        array('label'=>'Customer Resources', 'url'=>'#', 'items'=>array(
                                            array('label'=>'Add Customer Resource','url'=>array("/itsapp/telecom/CustomerResources/createResource?tenant=".$this->tenant->id)),
                                                 array('label'=>'Customer Resource List','url'=>array("/itsapp/telecom/CustomerResources?tenant=".$this->tenant->id))    
                                                    )),
                                                    
                                            array('label'=>'Vendor Resources', 'url'=>'#', 'items'=>array(
                                            array('label'=>'Add Vendor Resource','url'=>array("/itsapp/telecom/VendorResources/createResource?tenant=".$this->tenant->id)),
                                            array('label'=>'Vendor Resource List','url'=>array("/itsapp/telecom/VendorResources?tenant=".$this->tenant->id)), 

                                            )),

                                        ),
                                    ),  
                                    //products
                                    array(
                                        'label'=>'Products','url'=>"#",
                                            'items'=>array(
                                                        //calling products
                                                        array(
                                                            'label'=>'Calling Products','url'=>"#",
                                                                'items'=>array(
                                                                    array('label'=>'Product List','url'=>array('/itsapp/telecom/products','tenant'=>$this->tenant->id)),
                                                                    array('label'=>'Create Calling Product','url'=>array('/itsapp/telecom/products/create','tenant'=>$this->tenant->id)),
                                                                    //LCR
                                                                    array('label'=>'LCR', 'url'=>'#',
                                                                        'items'=>array(
                                                                                array('label'=>'Product LCR', 'url'=>array('/itsapp/telecom/routeList/productLCR?tenant='.$this->tenant->id)),
                                                                                array('label'=>'Export LCR', 'url'=>array('/itsapp/telecom?tenant='.$this->tenant->id)),
                                                                                array('label'=>'LCR List', 'url'=>array('/itsapp/telecom/routeList/index?tenant='.$this->tenant->id))
                                                                                )
                                                                    ),
                                                                ),
                                                        ),
                                                        //recurring products
                                                        array(
                                                            'label'=>'Recurring Products','url'=>'#',
                                                                'items'=>array(
                                                                    array('label'=>'Product List','url'=>array('/itsapp/telecom/products/RecurringProducts','tenant'=>$this->tenant->id)),
                                                                    array('label'=>'Create Product','url'=>array('/itsapp/telecom/products/createRecurringProduct','tenant'=>$this->tenant->id)),
                                                                ),
                                                        )
                                            ),    
                                    ),
                                    array(
                                        'label'=>'Invoice', 'url'=>'#',
                                            'items'=>array(
                                                array('label'=>'Find Invoice','url'=>array("/itsapp/telecom/invoice?tenant=".$this->tenant->id)),
                                                array('label'=>'Tax Categories','url'=>array("/itsapp/telecom/invoice/TaxCategories?tenant=".$this->tenant->id)),
                                                array('label'=>'Surcharge Categories','url'=>array("/itsapp/telecom/invoice/SurchargeCategories?tenant=".$this->tenant->id)),
                                                array('label'=>'Late Fee Categories','url'=>array("/itsapp/telecom/invoice/LateFeeCategories?tenant=".$this->tenant->id)),
                                            ),  
                                    ),
                                    array(
                                        'label'=>'Tariffs', 'url'=>array('/itsapp/telecom/tariffs?tenant='.$this->tenant->id),
                                        'items'=>array(
                                             array('label'=>'Tariffs','url'=>array('/itsapp/telecom/tariffs?tenant='.$this->tenant->id)),
                                             array('label'=>'Code Groups','url'=>array('/itsapp/telecom/destinationGroups?tenant='.$this->tenant->id)),
                                             array('label'=>'Destination List','url'=>array('/itsapp/telecom/DestinationList?tenant='.$this->tenant->id)),
                                             )
                                    ),
                                    array('label'=>'Reports', 'url'=>'#',
                                        'items'=>array(
                                            array('label'=>'Customer Hourly','url'=>array('/itsapp/telecom/reports/customers','tenant'=>$this->tenant->id)),
                                            array('label'=>'Vendor Hourly','url'=>array('/itsapp/telecom/reports/vendors','tenant'=>$this->tenant->id)),
                                        ),            
                                    ),
                                    
                                    
                                    
                    ),
                                    
                ),
                                                
                                                
                );
    }
     
    
}
