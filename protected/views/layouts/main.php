<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <?php
    $jqueryslidemenupath = Yii::app()->assetManager->publish(Yii::app()->basePath.'/scripts/jqueryslidemenu/');
    //Register jQuery, JS and CSS files
    Yii::app()->clientScript->registerCssFile($jqueryslidemenupath.'/jqueryslidemenu.css');
    Yii::app()->clientScript->registerCoreScript('jquery');
    Yii::app()->clientScript->registerScriptFile($jqueryslidemenupath.'/jqueryslidemenu.js');
    ?>    
</head>
<body>
<div class="container" id="page">
	<div id="header">
		<div id="logo">
        <?php
            if(!isset(Yii::app()->params['tenant'])){
                echo CHtml::encode(Yii::app()->name);
            }else{
                echo empty(Yii::app()->params['tenant'])?'Company Not Found':CHtml::encode(Yii::app()->params['tenant']->name);
            } 
        ?>
        </div>
	</div><!-- header -->
	<?php echo $content; ?>
	<div class="clear"></div>
	<div id="footer">
        itsourcecorp.net
	</div>
</div>
</body>
</html>
