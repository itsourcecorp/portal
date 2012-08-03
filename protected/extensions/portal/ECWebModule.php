<?php
class ECWebModule extends CWebModule{
    public function init(){
        parent::init();
    }
    public function getName(){
        preg_match('/[a-zA-Z]+$/',$this->id,$matches);
        return $matches[0]; 
    } 
}
?>
