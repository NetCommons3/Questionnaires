<?php
/**
 * questionnaire setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php echo $this->element('Questionnaires.scripts'); ?>
<?php echo $this->Html->script('Questionnaires.questionnaires_edit.js');?>


<div id="nc-questionnaires-setting-<?php echo (int)$frameId; ?>"
	 ng-controller="Questionnaires.setting"
	 ng-init="initialize(<?php echo (int)$frameId; ?>,
									<?php echo h(json_encode($jsQuestionnaire)); ?>)">


	<?php echo $this->Form->create('Questionnaire', array(
	'id' => 'questionnairePublishedForm-' . $frameId,
	'type' => 'post',
	'novalidate' => true,
	)); ?>

	<?php echo $this->Form->hidden('id', array(
	'value' => $questionnaire['Questionnaire']['id']
	)); ?>
	<?php echo $this->Form->hidden('Frame.id', array(
	'value' => $frameId,
	)); ?>
	<?php echo $this->Form->hidden('Block.id', array(
	'value' => $blockId,
	)); ?>

	<div class="modal-body">

		<?php echo $this->element('Questionnaires.edit_flow_chart', array('current' => '3')); ?>

		<div class="page-header">
			<h1><?php echo __d('questionnaires', 'Questionnaire setting'); ?></h1>
		</div>

		<h2>{{questionnaires.Questionnaire.title}}</h2>

		<div class="form-group questionnaire-group">
			<?php echo $this->Form->input('sub_title',
				array('class' => 'form-control',
					'label' => __d('questionnaires', 'Sub Title'),
					'ng-model' => 'questionnaires.questionnaire.subTitle',
					'placeholder' => __d('questionnaires', 'Please enter if there is a sub title')
					)
				);
			?>
		</div>

		<label class="h3"><?php echo __d('questionnaires', 'Questionnaire answer period'); ?></label>
		<div class="form-group questionnaire-group">
			<?php echo $this->element(
				'Questionnaires.Questionnaires/checkbox_set', [
				'fieldName' => 'is_period',
				'modelName' => 'isPeriod',
				'label' => __d('questionnaires', 'set the answer period'),
				'help' => __d('questionnaires', 'After approval will be immediately published . Stop of the questionnaire to select the stop from the questionnaire data list .')
				]) ?>
			<div class="row" ng-show="questionnaires.questionnaire.isPeriod">
				<div class="col-sm-5">
					<?php echo $this->element(
					'Questionnaires.Questionnaires/datetime', [
					'fieldName' => 'start_period',
					'ngModelName' => 'questionnaires.questionnaire.startPeriod',
					'min' => '',
					'max' => 'end_period',
					'model' => 'Questionnaire',
					]) ?>
				</div>
				<div class="col-sm-1">
					<?php echo __d('questionnaires', ' - '); ?>
				</div>
				<div class="col-sm-5">
					<?php echo $this->element(
					'Questionnaires.Questionnaires/datetime', [
					'fieldName' => 'end_period',
					'ngModelName' => 'questionnaires.questionnaire.endPeriod',
					'min' => 'start_period',
					'max' => '',
					'model' => 'Questionnaire',
					]) ?>
				</div>
			</div>
		</div>
		<label class="h3"><?php echo __d('questionnaires', 'Counting result display start date'); ?></label>
		<div class="row form-group questionnaire-group">
			<?php echo $this->element(
			'Questionnaires.Questionnaires/checkbox_set', [
			'fieldName' => 'total_show_timing',
			'modelName' => 'totalShowTiming',
			'label' => __d('questionnaires', 'set the aggregate display period'),
			'help' => __d('questionnaires', 'If not set , it will be displayed after the respondent answers.')
			]) ?>

			<div class="row" ng-show="questionnaires.questionnaire.totalShowTiming">
				<div class="col-sm-5">
					<?php echo $this->element(
					'Questionnaires.Questionnaires/datetime', [
					'fieldName' => 'total_show_start_period',
					'ngModelName' => 'questionnaires.questionnaire.totalShowStartPeriod',
					'min' => '',
					'max' => '',
					'model' => 'Questionnaire',
					]) ?>
				</div>
				<div class="col-sm-6">
					<?php echo __d('questionnaires', 'Result will display at this time.'); ?>
				</div>
			</div>
		</div>

		<label class="h3"><?php echo __d('questionnaires', 'Questionnaire method'); ?></label>
		<div class="form-group questionnaire-group">
			<?php echo $this->element(
			'Questionnaires.Questionnaires/checkbox_set', [
			'fieldName' => 'is_no_member_allow',
			'modelName' => 'isNoMemberAllow',
			'label' => __d('questionnaires', 'accept the non-members answer'),
			'help' => ''
			]) ?>

			<?php echo $this->element(
			'Questionnaires.Questionnaires/checkbox_set', [
			'fieldName' => 'is_key_pass_use',
			'modelName' => 'isKeyPassUse',
			'label' => __d('questionnaires', 'use key phrase'),
			'help' => ''
			]) ?>

			<?php echo $this->Form->input('key_phrase',
			array(
			'label' => false,
			'class' => 'form-control',
			'ng-show' => 'questionnaires.questionnaire.isKeyPassUse',
			'ng-model' => 'questionnaires.questionnaire.keyPhrase'
			)
			);
			?>

			<?php echo $this->element(
			'Questionnaires.Questionnaires/checkbox_set', [
			'fieldName' => 'is_anonymity',
			'modelName' => 'isAnonymity',
			'label' => __d('questionnaires', 'anonymous answer'),
			'help' => ''
			]) ?>

			<?php echo $this->element(
			'Questionnaires.Questionnaires/checkbox_set', [
			'fieldName' => 'is_repeat_allow',
			'modelName' => 'isRepeatAllow',
			'label' => __d('questionnaires', 'forgive the repetition of the answer'),
			'help' => ''
			]) ?>

			<?php echo $this->element(
			'Questionnaires.Questionnaires/checkbox_set', [
			'fieldName' => 'is_image_authentication',
			'modelName' => 'isImageAuthentication',
			'label' => __d('questionnaires', 'do image authentication'),
			'help' => ''
			]) ?>

			<?php echo $this->element(
			'Questionnaires.Questionnaires/checkbox_set', [
			'fieldName' => 'is_answer_mail_send',
			'modelName' => 'isAnswerMailSend',
			'label' => __d('questionnaires', 'Deliver e-mail when submitted'),
			'help' => ''
			]) ?>
		</div>

		<label class="h3"><?php echo __d('questionnaires', 'Thanks page message settings'); ?></label>
		<div class="form-group questionnaire-group">
			<div class="nc-wysiwyg-alert">
				<?php echo $this->Form->textarea('thanks_content',
				array(
				'class' => 'form-control',
				'ng-model' => 'questionnaires.questionnaire.thanksContent',
				'ui-tinymce' => 'tinymce.options',
				'rows' => 5,
				'error' => false,
				)) ?>
			</div>
			<?php echo $this->element(
			'NetCommons.errors', [
			'errors' => $this->validationErrors,
			'model' => 'Questionnaire',
			'field' => 'thanks_content',
			]) ?>
		</div>

		<?php echo $this->element('Comments.form'); ?>

		<?php echo $this->element('Comments.index'); ?>

	</div>

	<div class="modal-footer">
		<div class="text-center">
			<?php echo $this->element('Questionnaires.Questionnaires/workflow_buttons', [
			'isPublished' => $isPublished,
			]); ?>
		</div>
	</div>
	<?php echo $this->Form->end(); ?>

	<?php if (! empty($questionnaire['Questionnaire']['origin_id'])) : ?>
	<?php echo $this->Form->create('Questionnaire', array(
	'id' => 'questionnaireDeleteForm-' . $frameId,
	'type' => 'post',
	'action' => 'delete',
	'novalidate' => true,
	)); ?>

	<?php echo $this->Form->hidden('id', array(
	'value' => $questionnaire['Questionnaire']['id']
	)); ?>
	<?php echo $this->Form->hidden('origin_id', array(
	'value' => $questionnaire['Questionnaire']['origin_id']
	)); ?>
	<?php echo $this->Form->hidden('key', array(
	'value' => $questionnaire['Questionnaire']['key']
	)); ?>
	<?php echo $this->Form->hidden('Frame.id', array(
	'value' => $frameId,
	)); ?>
	<?php echo $this->Form->hidden('Block.id', array(
	'value' => $blockId,
	)); ?>

	<div class="text-right">
			<?php echo $this->Form->button(
			'<span class="glyphicon glyphicon-trash"></span>',
			array(
			'class' => 'btn btn-danger btn-lg',
			'type' => 'button',
			'name' => 'questionnaire_deleted',
			'ng-click' => 'deleteQuestionnaire($event' . ", '" . __d('questionnaires', 'Do you want to delete this questionnaire ?') . "')"
			)) ?>
		</div>
	<?php echo $this->Form->end(); ?>
	<?php endif; ?>
</div>
