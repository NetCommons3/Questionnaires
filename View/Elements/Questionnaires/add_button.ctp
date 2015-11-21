<?php
/**
 * questionnaire page setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php if (Current::permission('content_creatable')) : ?>
	<div class="pull-right">
		<?php echo $this->Button->addLink(
			'',
			array(
				'controller' => 'questionnaire_add',
				'action' => 'add',
				'frame_id' => Current::read('Frame.id'),
				'block_id' => Current::read('Block.id'),
			),
			array('tooltip' => __d('questionnaires', 'Create article'))); ?>
	</div>
<?php endif;