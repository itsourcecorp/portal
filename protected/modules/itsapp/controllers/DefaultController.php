<?php

class DefaultController extends Controller
{   

    public function actionIndex($tenant=null)
	{            
        //print_r(Yii::app()->params['menus']);
        $this->layout = "//layouts/home_column1";
            $this->render('index');
        
        
	}

}