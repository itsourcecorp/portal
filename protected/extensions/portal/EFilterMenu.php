<?php
Yii::import('zii.widgets.CMenu');
/**
* Class to filter out links to which the current user does not have access
*/
  Class EFilterMenu extends CMenu{
      private $modules;
      private $permissions;
  public function init()
    {
        $mods = Modules::model()->findAll();
        $this->modules = array();
        foreach($mods as $module){
            $this->modules[] = $module->module_name;
        }
        //$this->permissions = Yii::app()->authManager->getAuthAssignments();
        $this->htmlOptions['id']=$this->getId();
        $route=$this->getController()->getRoute();
        $this->items=$this->normalizeItems($this->items,$route,$hasActiveChild);
        
    }
/**
     * Recursively renders the menu items.
     * @param array $items the menu items to be rendered recursively
     */
    protected function renderMenuRecursive($items)
    {
        $count=0;
        $n=count($items);
        foreach($items as $item)
        {
            $count++;
            $options=isset($item['itemOptions']) ? $item['itemOptions'] : array();
            $class=array();
            if($item['active'] && $this->activeCssClass!='')
                $class[]=$this->activeCssClass;
            if($count===1 && $this->firstItemCssClass!==null)
                $class[]=$this->firstItemCssClass;
            if($count===$n && $this->lastItemCssClass!==null)
                $class[]=$this->lastItemCssClass;
            if($this->itemCssClass!==null)
                $class[]=$this->itemCssClass;
            if($class!==array())
            {
                if(empty($options['class']))
                    $options['class']=implode(' ',$class);
                else
                    $options['class'].=' '.implode(' ',$class);
            }


            $menu=$this->renderMenuItem($item);
            if(!empty($menu))
                echo CHtml::openTag('li', $options);

            if(isset($this->itemTemplate) || isset($item['template']))
            {
                $template=isset($item['template']) ? $item['template'] : $this->itemTemplate;
                echo strtr($template,array('{menu}'=>$menu));
            }
            else
                echo $menu;
            if(isset($item['items']) && count($item['items']) && !empty($menu))
            {
                echo "\n".CHtml::openTag('ul',isset($item['submenuOptions']) ? $item['submenuOptions'] : $this->submenuHtmlOptions)."\n";
                $this->renderMenuRecursive($item['items']);
                echo CHtml::closeTag('ul')."\n";
            }
            if(!empty($menu))
                echo CHtml::closeTag('li')."\n";
        }
    }
      protected function renderMenuItem($item)
    {
        if(isset($item['url']))
        {
            $linkoptions = array();
            if(isset($item['linkOptions'])){
                $linkoptions = $item['linkOptions'];
            }
            if(isset($item['htmlOptions'])){
                $htmloptions = $item['htmlOptions'];
            }
            
            $label=$this->linkLabelWrapper===null ? $item['label'] : '<'.$this->linkLabelWrapper.'>'.$item['label'].'</'.$this->linkLabelWrapper.'>';

            if(isset($item['items'])){
                if($this->recurseCheck($item)){
                    return CHtml::link($label,$item['url'],$linkoptions); 
                }else{
                    return null;
                }
            }elseif((isset($htmloptions['visible']) AND $htmloptions['visible']==1) || (isset($item['visible']) AND $item['visible']==true)){
                return CHtml::link($label,$item['url'],$linkoptions); 
            }elseif($this->parseUrl($item['url'])){
                return CHtml::link($label,$item['url'],$linkoptions);
            }

            
        }
        else
            return CHtml::tag('span',isset($item['linkOptions']) ? $item['linkOptions'] : array(), $item['label']);
    }
    protected function recurseCheck($item){
        if(isset($item['items'])){ 
            foreach($item['items'] as $item){
               if($this->recurseCheck($item))
                return true;
            }
        }else{
           if($this->parseUrl($item['url']) || (isset($item['visible']) AND $item['visible']==true)){
               return true;
           }
        }
    }
    protected function parseUrl($url){
        
        //modified from beta///////////JS 2012-08-03
        $url = CHtml::normalizeUrl($url);
        preg_match('/itsapp[^?]/', $url, $matches);
        $url =  substr($matches[0],0,-1);
        $pathinfo = explode('/',$url);        
        $base = array_shift($pathinfo);
        ///////////////////////////////////////////
        $request = array();
        if(!empty(Yii::app()->params['tenant'])){
                $request['tenant']=Yii::app()->params['tenant']->id;
        }else{
             $request['tenant']='';
        }
        if(isset($pathinfo[0])){
            $request['module']=ucfirst($pathinfo[0]);
            
        }else{
            return true;
        }
        if(isset($pathinfo[1])){
            if(in_array($pathinfo[1],$this->modules)){
                $request['submodule']=ucfirst($pathinfo[1]);
            }else{
                $request['controller']=ucfirst($pathinfo[1]);
            }
        }else{
            $request['controller']='Default';
        }
        if(isset($pathinfo[2]) AND !isset($request['controller'])){
            $querystart = strpos($pathinfo[2],'?');
            if($querystart){
                 $request['controller'] = ucfirst(substr($pathinfo[2],0,$querystart));
            }else{
                 $request['controller'] = ucfirst($pathinfo[2]);
            }
        }else{
            $request['action']='Index';
        }
        if(isset($pathinfo[3])){
            $querystart = strpos($pathinfo[3],'?');
            if($querystart){
                 $request['action'] = ucfirst(substr($pathinfo[3],0,$querystart));
            }else{
                 $request['action'] = ucfirst($pathinfo[3]);
            }
        }else{
            $request['action']='Index';
        }
        if(!isset($request['tenant']))
            $request['tenant'] = '';
        if(isset($request['submodule'])){
            $request['module'].='.'.$request['submodule'];
        }
        if(!isset($request['controller']))
            $request['controller']= '';
        if(!isset($request['action']))
            $request['action']= '';
        return $this->accessChecker($request['tenant'],$request['module'],$request['controller'],$request['action']);
        
    }
     private function accessChecker($tenant,$controllerid,$controller,$action){
        if(Yii::app()->user->checkAccess($tenant.'.'.$controllerid.'.*',Yii::app()->user->getId())){
            return true;
        } 
         if(Yii::app()->user->checkAccess($tenant.'.'.$controllerid.'.'.ucfirst($controller).'.*',Yii::app()->user->getId())){
            return true;
        }
        
        if(Yii::app()->user->checkAccess($tenant.'.'.$controllerid.'.'.ucfirst($controller).'.'.ucfirst($action),Yii::app()->user->getId())){
            return true;
        }
        
        if(Yii::app()->user->checkAccess($controllerid.'.'.ucfirst($controller).'.'.ucfirst($action),Yii::app()->user->getId())){
            return true;
        }
        if(Yii::app()->user->checkAccess($controllerid.'.'.ucfirst($controller).'.*',Yii::app()->user->getId())){
            return true;
        }
        if(Yii::app()->user->checkAccess($controllerid.'.*',Yii::app()->user->getId())){
            return true;
        } 
        return false;
        
    }
  }
?>
