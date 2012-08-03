<?php 
$this->beginContent('//layouts/main'); ?>
<?php $this->renderPartial('//site/home_menu'); ?>
<?php 
if(isset($this->breadcrumbs) && isset(Yii::app()->params['tenant'])){
    $tenant_crumbs = array();
    foreach($this->breadcrumbs as $k => $v){
        $tenant_crumbs[$k] = $v;
        if(is_array($v)){
            $tenant_crumbs[$k]['tenant'] = Yii::app()->params['tenant']->id;
        }
    }
    $this->breadcrumbs = $tenant_crumbs;
}
?>
<?php if(isset($this->breadcrumbs)):?>
    <?php $this->widget('zii.widgets.CBreadcrumbs', array(
        'links'=>$this->breadcrumbs,
    )); ?>
<?php endif?>
<div id="content">
	<?php echo $content; ?>
</div>
<?php $this->endContent(); ?>