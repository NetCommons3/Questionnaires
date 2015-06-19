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
<?php
echo $this->element('Questionnaires.scripts');
echo $this->Html->script(
	array('/components/visualcaptcha.jquery/visualcaptcha.jquery.js'),
	array(
		'plugin' => false,
		'inline' => false
	)
);
echo $this->Html->css(
	'/components/visualcaptcha.jquery/visualcaptcha.css',
	array(
		'plugin' => false,
		'inline' => false
	)
);
echo $this->element('Questionnaires.Answers/answer_test_mode_header');
?>

<articel>

	<?php echo $this->element('Questionnaires.Answers/answer_header'); ?>

	<?php echo $this->Form->create('PreAnswer', array(
	'name' => 'questionnaire_form_answer',
	'type' => 'post',
	'novalidate' => true,
	)); ?>

	<?php echo $this->Form->hidden('id'); ?>
	<?php echo $this->Form->hidden('Frame.id', array('value' => $frameId)); ?>
	<?php echo $this->Form->hidden('Block.id', array('value' => $blockId)); ?>
	<?php echo $this->Form->hidden('Questionnaire.id', array('value' => $questionnaire['questionnaire']['id'])); ?>
	<?php if ($questionnaire['questionnaire']['isKeyPassUse'] == QuestionnairesComponent::USES_USE): ?>
	<p>
		<?php echo __d('questionnaires', 'This questionnaire has been guarded by the key phrase. Please enter the first key phrase To answer .'); ?>
		</p>
		<?php echo $this->Form->input('key_phrase', array(
			'div' => 'form-group',
			'label' => __d('questionnaires', 'Please input Key-Phrase'),
			'class' => 'form-control'
		));?>
		<?php if (isset($errors['preAnswer']['keyPhrase'])): ?>
		<div class="has-error">
			<?php foreach ($errors['preAnswer']['keyPhrase'] as $message): ?>
			<div class="help-block">
				<?php echo $message ?>
			</div>
			<?php endforeach ?>
		</div>
		<?php endif ?>

	<?php echo $this->element(
		'NetCommons.errors', [
		'errors' => $this->validationErrors,
		'model' => 'questionnaire',
		'field' => 'keyPhrase',
		]) ?>
	<?php endif ?>

	<?php if ($questionnaire['questionnaire']['isImageAuthentication'] == QuestionnairesComponent::USES_USE): ?>
		<?php echo $this->element(
			'NetCommons.visual_captcha', [
			'elementId' => 'questionnaire-captcha',
			'path' => 'questionnaires/questionnaire_answers/',
			]) ?>
		<?php if (isset($errors['preAnswer']['imageAuth'])): ?>
		<div class="has-error">
			<?php foreach ($errors['preAnswer']['imageAuth'] as $message): ?>
			<div class="help-block">
				<?php echo $message ?>
			</div>
			<?php endforeach ?>
		</div>
		<?php endif ?>
	<?php endif ?>

	<div class="text-center">
		<?php echo $this->BackToPage->backToPageButton(__d('net_commons', 'Cancel'), 'remove'); ?>
		<?php echo $this->Form->button(
		__d('net_commons', 'NEXT') . ' <span class="glyphicon glyphicon-chevron-right"></span>',
		array(
		'class' => 'btn btn-primary',
		'name' => 'next_' . '',
		)) ?>
	</div>
<?php echo $this->Form->end(); ?>

</articel>

