<?
$dataProvider = $filtersForm->filter($availableactions);
$this->widget('zii.widgets.grid.CGridView', array(
            'dataProvider'=>$dataProvider,
            'id'=>'availableactions',
             'filter'=>$filtersForm,
            'emptyText'=>Rights::t('core', 'This item has no available child Actions.'),
            'columns'=>array(
                'module',
                'controller',
                'action',
                array(
                    'value'=>'CHtml::Link("Enable Action","#yw5_tab_0",array("onclick"=>"availableaction('."'".'".$data["id"]."'."'".')","id"=>"availableaction".str_replace(array("*","."),array("_ast","-"),$data["id"])))', 
                    'header'=>'"Add Action"',
                    'type'=>'raw',
                )
                
            )
        ));
         Yii::app()->clientScript->registerScript('avact','
                           availableaction = function(data){
                                $.ajax({
                                url: "'.CHtml::normalizeUrl(array("addChildItem")).'",
                                method: "GET",
                                data: {parent:"'.$model.'",child:data,type:0},
                                dataType: "json",
                                success: function(json){
                                                                    $("#aflashes").html(json.Message);
                                    $.fn.yiiGridView.update("availableactions");
                                },
                                
                                });
                                
                                }
                    
                    ',CClientScript::POS_END); 
       echo CHtml::link('Refresh',"#yw5_tab_0",array('onclick'=>"refresh('availableactions')"));
            

?>