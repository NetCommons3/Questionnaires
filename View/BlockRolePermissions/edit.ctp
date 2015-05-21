<?php
/**
 * BbsSettings edit template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>


<div class="modal-body">
	<?php echo $this->element('NetCommons.setting_tabs', $settingTabs); ?>

	<div class="tab-content">

		<?php echo $this->element('Blocks.edit_form', array(
				'controller' => 'BlockRolePermission',
				'action' => 'edit' . '/' . $frameId . '/' . $blockId,
				'callback' => 'Questionnaires.BlockRolePermissions/edit_form',
				'cancelUrl' => '/questionnaires/blocks/index/' . $frameId,
			)); ?>
	</div>
</div>
