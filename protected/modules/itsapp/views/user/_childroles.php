<?php
  $this->widget('zii.widgets.grid.CGridView', array(
            'dataProvider'=>$filtersForm2->filter($dataprovider),
            'id'=>'roles',
             'filter'=>$filtersForm2,
            'emptyText'=>Rights::t('core', 'This item has no child roles.'),
            'htmlOptions'=>array('class'=>'grid-view user-assignment-table mini'),
            'columns'=>array(
                'role',
                array(
                    'class'=>'CLinkColumn',
                    'urlExpression'=>'array("editRole","role"=>$data["id"])',
                    'labelExpression'=>'"Edit Role"'
                ),
                 array(
                    'value'=>'CHtml::Link("Revoke Role","#yw2_tab_0",array("onclick"=>"childrole('."'".'".$data["id"]."'."'".')","id"=>"childaction".str_replace(array("*","."),array("_ast","-"),$data["id"])))', 
                    'header'=>'"Add Role"',
                    'type'=>'raw',
                )
            )
        ));
        Yii::app()->clientScript->registerScript('childactioner','
                           childrole = function(data){
                                $.ajax({
                                url: "'.CHtml::normalizeUrl(array("removeChildItem")).'",
                                method: "GET",
                                data: {parent:"'.$model.'",child:data,type:2},
                                dataType: "json",
                                success: function(json){
                                                                    $("#aflashes").html(json.Message).fadeIn("slow");

                                    $.fn.yiiGridView.update("roles");
                                },
                                complete: function(){

                                }
                                });

                                }
                    
                    ',CClientScript::POS_END); 
          echo CHtml::link('Refresh',"#yw2_tab_0",array('onclick'=>"refresh('roles')"));
                                            
                                                
?>
