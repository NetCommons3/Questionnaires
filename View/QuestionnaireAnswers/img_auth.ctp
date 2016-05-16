<?php
/**
 * questionnaire answer image authentication guard view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<article>
	<?php echo $this->element('Questionnaires.Answers/answer_header'); ?>

	<?php echo $this->element('Questionnaires.Answers/answer_test_mode_header'); ?>

	<?php
		echo $this->NetCommonsForm->create(null, array('type' => 'post', 'url' => $postUrl));
		echo $this->NetCommonsForm->hidden('Frame.id');
		echo $this->NetCommonsForm->hidden('Block.id');

		echo $this->element(
		'VisualCaptcha.visual_captcha', array(
		'identifyKey' => 'Questionnaires_' . Current::read('Frame.id'),
		));
	?>

	<div class="text-center">
		<?php if ($displayType == QuestionnairesComponent::DISPLAY_TYPE_LIST): ?>
			<?php echo $this->BackTo->pageLinkButton(__d('net_commons', 'Cancel'), array('icon' => 'remove')); ?>
		<?php endif; ?>
		<?php echo $this->NetCommonsForm->button(
		__d('net_commons', 'NEXT') . ' <span class="glyphicon glyphicon-chevron-right"></span>',
		array(
		'class' => 'btn btn-primary',
		'name' => 'next_' . '',
		)) ?>
	</div>

	<?php echo $this->NetCommonsForm->end(); ?>
</article>