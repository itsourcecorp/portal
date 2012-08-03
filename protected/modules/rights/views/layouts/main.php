<?php $this->beginContent(Rights::module()->appLayout); ?>

        <?php if( $this->id!=='install' ): ?>


                <?php $this->renderPartial('/site/menu'); ?>


        <?php endif; ?>

        <?php $this->renderPartial('/_flash'); ?>

<div id="rights" class="container">

	<div id="content">

		<?php echo $content; ?>

	</div><!-- content -->

</div>

<?php $this->endContent(); ?>