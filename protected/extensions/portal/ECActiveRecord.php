<?php
class ECActiveRecord extends CActiveRecord
{
    //this is a base class for all applications
    //so that we can add new shared functionality
    
    const INACTIVE = 0;
    const ACTIVE = 1;
    private $_oldattributes = array();

    public function getStatusOptions(){
         return array(
            self::INACTIVE => 'No',
            self::ACTIVE => 'Yes',
        ); 
    }
    public function getEnabledOptions(){
         return array(
            self::ACTIVE => 'Enabled',
            self::INACTIVE => 'Disabled',
        ); 
    }
    public function validateIpAddress($ip_addr)
    {
      //first of all the format of the ip address is matched
      if(preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$ip_addr))
      {
        //now all the intger values are separated
        $parts=explode(".",$ip_addr);
        //now we need to check each part can range from 0-255
        foreach($parts as $ip_parts)
        {
          if(intval($ip_parts)>255 || intval($ip_parts)<0)
          return false; //if number is not within range of 0-255
        }
        return true;
      }
      else
        return false; //if format of ip address doesn't matches
    }
    public function afterSave(){
        if (!$this->isNewRecord) {
 
            // new attributes
            $newattributes = $this->getAttributes();
            $oldattributes = $this->getOldAttributes();
 
            // compare old and new
            foreach ($newattributes as $name => $value) {
                if (!empty($oldattributes)) {
                    $old = $oldattributes[$name];
                } else {
                    $old = '';
                }
 
                if ($value != $old) {
                    //$changes = $name . ' ('.$old.') => ('.$value.'), ';
 
                    $log=new ActiveRecordLog;
                    $log->description=  'User ' . Yii::app()->user->Name 
                                            . ' changed ' . $name . ' for ' 
                                            . get_class($this) 
                                            . '[' . $this->getPrimaryKey() .'].';
                    $log->action=       'CHANGE';
                    $log->model=        get_class($this);
                    $log->idModel=      $this->getPrimaryKey();
                    $log->field=        $name;
                    $log->creationdate= new CDbExpression('NOW()');
                    $log->userid=       Yii::app()->user->id;
                    if(isset(Yii::app()->params['tenant']))
                        $log->tenantid = Yii::app()->params['tenant']->id;
                    $changedfrom = array_diff_assoc($oldattributes,$newattributes);
                    $changedto = array_diff_assoc($newattributes,$oldattributes);
                    $changedfromstring = '';
                    $changedtostring = '';
                    foreach($changedfrom as $key =>$item){
                       $changedfromstring .= "[$key]=>$item"; 
                    }
                    foreach($changedto as $key =>$item){
                       $changedtostring .= "[$key]=>$item"; 
                    }
                    
                    $log->changesum = $changedfromstring.' Changed to '.$changedtostring;
                    $log->save();
                }
            }
            } else {
                $log=new ActiveRecordLog;
                $log->description=  'User ' . Yii::app()->user->Name 
                                        . ' created ' . get_class($this) 
                                        . '[' . $this->getPrimaryKey() .'].';
                $log->action=       'CREATE';
                $log->model=        get_class($this);
                $log->idModel=      $this->getPrimaryKey();
                $log->field=        '';
                $log->creationdate= new CDbExpression('NOW()');
                $log->userid=       Yii::app()->user->id;
                if(isset(Yii::app()->params['tenant']))
                    $log->tenantid = Yii::app()->params['tenant']->id;
                $log->changesum = 'New Record';
                $log->save();
                
            }
        }
     public function afterDelete()
    {
        $log=new ActiveRecordLog;
        $oldattributes = $this->getOldAttributes();
        $log->description=  'User ' . Yii::app()->user->Name . ' deleted ' 
                                . get_class($this) 
                                . '[' . $this->getPrimaryKey() .'].';
        $log->action=       'DELETE';
        $log->model=        get_class($this);
        $log->idModel=      $this->getPrimaryKey();
        $log->field=        '';
        $log->creationdate= new CDbExpression('NOW()');
        $log->userid=       Yii::app()->user->id;
        if(isset(Yii::app()->params['tenant']))
            $log->tenantid = Yii::app()->params['tenant']->id;
        $changedfrom = $oldattributes;
        $changedfromstring = '';
        foreach($changedfrom as $key =>$item){
           $changedfromstring .= "[$key]=>$item"; 
        }
        $log->changesum = 'DELETED VALUES: '.$changedfromstring;
        $log->save();
    }
    public function afterFind()
    {
        // Save old values
        $this->setOldAttributes($this->getAttributes());
        parent::afterFind();
    }
 
    public function getOldAttributes()
    {
        return $this->_oldattributes;
    }
 
    public function setOldAttributes($value)
    {
        $this->_oldattributes=$value;
    }
    
     public function Enum($field){
        $enums = array();
        preg_match_all('/\'.+?\'/', $this->tableSchema->columns[$field]->dbType,$matches);
        foreach($matches[0] as $v){
            $enums[] = substr($v,1,-1);   
        }
        return $enums;
    }
    
    //creates  a dropdown list for enum fields
    public function createEnumDropdown($field)
    {
        $enums =  $this->tableSchema->columns[$field]->dbType;
        $array = array();
        $enum_array = explode("','", preg_replace("/(enum|set)\('(.+?)'\)/","\\2",$enums)); 
       
        if(count($enum_array) > 1)
        {
            foreach($enum_array as $enum)
            {
                $array[$enum]=ucwords($enum);
            }
        }
        else
        {
          throw new Exception($field.' is not an enum. You cannot use this function.');
          exit();  
        }
        
        return $array;
    }
    
        
        protected function beforeSave()
        {   //validate fields before saving mainly
            //used for resources
            $this->validateFields();
            return parent::beforeSave();   
        }
        
        
        public function validateFields()
        {
           $eValidation = new EValidator();
           //get all the fields for this table
           $fieldArrays = $this->attributeNames(); 
           foreach($fieldArrays as $field)
           {
              $eValidation->validateAttribute( $this, $field );
           } 
        }
   
        //returns the directory of a child class
        //needed to determine the right class to validate
        public function getDir() {       
         $reflector = new ReflectionClass(get_class($this));
         return dirname($reflector->getFileName());
      }
 
}
?>
