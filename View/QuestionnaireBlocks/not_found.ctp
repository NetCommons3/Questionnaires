<?php
/**
 * Blocks view for editor template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<article class="block-setting-body">
	<?php echo $this->BlockTabs->main(BlockTabsHelper::MAIN_TAB_BLOCK_INDEX); ?>

	<?php echo $this->BlockIndex->notFoundDescription(); ?>

	<div class="tab-content">
		<div class="text-right clearfix">
			<?php echo $this->element('Questionnaires.Questionnaires/add_button'); ?>
		</div>

		<div class="text-left">
			<?php echo __d('net_commons', 'Not found.'); ?>
		</div>
	</div>
</article>
