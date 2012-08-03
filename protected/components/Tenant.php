<?php
class Tenant extends CApplicationComponent{
    
    private $_model=null;
    private $_telcoDb=null;
    private $_cdrDb=null;
    
    public function init(){
    }
    public function getModel(){
        if($this->_model === null){ 
            $this->_model=Tenants::model()->findByPk($_GET['tenant']);    
        }
        return $this->_model;
    }
    public function setModel($id)
    {
        $this->_model=Tenants::model()->findByPk($id);
    }
    public function getId(){ 
        return $this->model->id;
    }
    public function getTelcoDb(){
        //use TelcoLocations->getConfig
        //to create an array and pass
        //it to the application
        //to create as a component
        if($this->_telcoDb === null){
            $this->_telcoDb = yii::createComponent( $this->model->TelcoConfig->dbLocation->config );           
        }        
        return $this->_telcoDb;
    }
    public function getCdrDb(){
        //use TelcoLocations->getConfig
        //to create an array and pass
        //it to the application
        //to create as a component
        if($this->_cdrDb === null){
            $this->_cdrDb = yii::createComponent( $this->model->TelcoConfig->cdrLocation->config );           
        }        
        return $this->_cdrDb;
    }
    
    public function getCustomerList(){
      return array_keys(CHtml::listData($this->model->customers,'customer_num','customer_num'));
    }
    
    public function getInvoiceList(){
        $invoices = array();
        foreach($this->model->customers as $customer){
            $invoices = array_merge($invoices, array_keys(CHtml::listData($customer->invoices,'id','id')));        
        }
        return $invoices;
    }
    public function getCustomerTrunkAssignments(){
        $query = "SELECT trunk_id, db_stat_id FROM _yii_available_trunks WHERE tenant_id=$this->id AND assignee < 60000";
        return Yii::app()->db->createCommand($query)->queryAll();
    }
    public function getVendorTrunkAssignments(){
        $query = "SELECT trunk_id, db_stat_id FROM _yii_available_trunks WHERE tenant_id=$this->id AND assignee >= 60000";
        return Yii::app()->db->createCommand($query)->queryAll();
    }
}
?>
