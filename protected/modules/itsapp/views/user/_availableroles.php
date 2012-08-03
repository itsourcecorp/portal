<?php                  
$this->widget('zii.widgets.grid.CGridView', array(
            'dataProvider'=>$filtersForm4->filter($actions),
            'id'=>'availableroles',
            'filter'=>$filtersForm4,
            'emptyText'=>Rights::t('core', 'This item has no Available Roles.'),
            'htmlOptions'=>array('class'=>'grid-view user-assignment-table mini'),
            'columns'=>array(
                'role',
                array(
                    'class'=>'CLinkColumn',
                    'urlExpression'=>'array("editRole","role"=>$data["id"])',
                    'labelExpression'=>'"Edit Role"'
                ),
                 array(
                    'value'=>'CHtml::Link("Enable Role","#yw5_tab_1",array("onclick"=>"availablerole('."'".'".$data["id"]."'."'".')","id"=>"availablerole".str_replace(array("*","."),array("_ast","-"),$data["id"])))', 
                    'header'=>'"Add Role"',
                    'type'=>'raw',
                )
            )
        ));
        Yii::app()->clientScript->registerScript('availrole','
                           availablerole = function(data){
                                $.ajax({
                                url: "'.CHtml::normalizeUrl(array("addChildItem")).'",
                                method: "GET",
                                data: {parent:"'.$model.'",child:data,type:2},
                                dataType: "json",
                                success: function(json){
                                    $("#aflashes").html(json.Message).fadeIn("slow");
                                    $.fn.yiiGridView.update("roles");

                                },
                                 
                                });

                                }
                    
                    ',CClientScript::POS_END); 
                           echo CHtml::link('Refresh',"#yw5_tab_1",array('onclick'=>"refresh('availableroles')"));


?>
