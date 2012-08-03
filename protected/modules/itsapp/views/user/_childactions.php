<?php
  $this->widget('zii.widgets.grid.CGridView', array(
            'dataProvider'=>$filtersForm3->filter($actions),
            'id'=>'childactions',
            'filter'=>$filtersForm3,

            'emptyText'=>Rights::t('core', 'This item has no child Actions.'),
            'htmlOptions'=>array('class'=>'grid-view user-assignment-table mini'),
            'columns'=>array(
                'module',
                'controller',
                'action',
                 array(
                    'value'=>'CHtml::Link("Revoke Action","#yw2_tab_1",array("onclick"=>"childaction('."'".'".$data["id"]."'."'".')","id"=>"childaction".str_replace(array("*","."),array("_ast","-"),$data["id"])))', 
                    'header'=>'Revoke Action',
                    'type'=>'raw',
                )
            )
        ));
        Yii::app()->clientScript->registerScript('childactions','
                           childaction = function(data){
                                $.ajax({
                                url: "'.CHtml::normalizeUrl(array("removeChildItem")).'",
                                method: "GET",
                                data: {parent:"'.$model.'",child:data,type:0},
                                dataType: "json",
                                success: function(json){
                                    $("#aflashes").html(json.Message).fadeIn("slow");
                                    $.fn.yiiGridView.update("childactions").fadeIn("fast");
                                    
                                },
                                complete: function(){
                                    
                                } 
                                });

                                }
                    
                    ',CClientScript::POS_END); 
        
        echo CHtml::link('Refresh',"#yw2_tab_1",array('onclick'=>"refresh('childactions')"));


?>
