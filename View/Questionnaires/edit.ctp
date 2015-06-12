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
									<?php echo h(json_encode($questionnaire)); ?>)">


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
					'ng-model' => 'questionnaires.Questionnaire.sub_title',
					'placeholder' => __d('questionnaires', 'Please enter if there is a sub title')
					)
				);
			?>
		</div>

		<label class="h3"><?php echo __d('questionnaires', 'Questionnaire answer period'); ?></label>
		<div class="form-group questionnaire-group">
			<div class="checkbox">
				<label>
					<?php echo $this->Form->input('is_period',
					array(
					'type' => 'checkbox',
					'div' => false,
					'label' => false,
					'ng-model' => 'questionnaires.Questionnaire.is_period',
					'error' => false
					));
					?>
					<?php echo __d('questionnaires', 'set the answer period'); ?>
					<span class="help-block"><?php echo __d('questionnaires', 'After approval will be immediately published . Stop of the questionnaire to select the stop from the questionnaire data list .'); ?></span>
				</label>
				<?php echo $this->element(
				'NetCommons.errors', [
				'errors' => $this->validationErrors,
				'model' => 'Questionnaire',
				'field' => 'is_period',
				]) ?>
			</div>
			<div class="row" ng-show="questionnaires.Questionnaire.is_period">
				<div class="col-sm-5">
					<?php echo $this->element(
					'Questionnaires.Questionnaires/datetime', [
					'fieldName' => 'start_period',
					'ngModelName' => 'questionnaires.Questionnaire.start_period',
					'min' => 'minDate',
					'max' => 'questionnaires.Questionnaire.end_period',
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
					'ngModelName' => 'questionnaires.Questionnaire.end_period',
					'min' => 'questionnaires.Questionnaire.start_period',
					'max' => '',
					'model' => 'Questionnaire',
					]) ?>
				</div>
			</div>
		</div>
		<label class="h3"><?php echo __d('questionnaires', 'Counting result display start date'); ?></label>
		<div class="row form-group questionnaire-group">
			<div class="col-sm-12 checkbox">
				<label>
					<?php echo $this->Form->input('total_show_timing',
					array(
					'type' => 'checkbox',
					'div' => false,
					'label' => false,
					'ng-model' => 'questionnaires.Questionnaire.total_show_timing',
					'ng-true-value' => QuestionnairesComponent::USES_USE,
					'ng-false-value' => QuestionnairesComponent::USES_NOT_USE,
					'error' => false
					));
					?>
					<?php echo __d('questionnaires', 'set the aggregate display period'); ?>
					<span class="help-block"><?php echo __d('questionnaires', 'If not set , it will be displayed after the respondent answers.'); ?></span>
				</label>
				<?php echo $this->element(
				'NetCommons.errors', [
				'errors' => $this->validationErrors,
				'model' => 'Questionnaire',
				'field' => 'total_show_timing',
				]) ?>
			</div>

			<div class="row" ng-show="questionnaires.Questionnaire.total_show_timing">
				<div class="col-sm-5">
					<?php echo $this->element(
					'Questionnaires.Questionnaires/datetime', [
					'fieldName' => 'total_show_start_period',
					'ngModelName' => 'questionnaires.Questionnaire.total_show_start_period',
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
			<?php echo $this->Form->input('is_no_member_allow',
			array(
			'type' => 'checkbox',
			'label' => false,
			'before' => '<div class="checkbox"><label>',
			'after' => __d('questionnaires', 'accept the non-members answer') . '</label></div>',
			'ng-model' => 'questionnaires.Questionnaire.is_no_member_allow',
			'error' => false,
			)
			);
			?>
			<?php echo $this->element(
			'NetCommons.errors', [
			'errors' => $this->validationErrors,
			'model' => 'Questionnaire',
			'field' => 'is_no_member_allow',
			]) ?>

			<?php echo $this->Form->input('is_key_pass_use',
			array(
			'type' => 'checkbox',
			'label' => false,
			'before' => '<div class="checkbox"><label>',
			'after' => __d('questionnaires', 'use key phrase') . '</label></div>',
			'ng-model' => 'questionnaires.Questionnaire.is_key_pass_use',
			'error' => false,
			)
			);
			?>
			<?php echo $this->element(
			'NetCommons.errors', [
			'errors' => $this->validationErrors,
			'model' => 'Questionnaire',
			'field' => 'is_key_pass_use',
			]) ?>

			<?php echo $this->Form->input('key_phrase',
			array(
			'label' => false,
			'class' => 'form-control',
			'ng-show' => 'questionnaires.Questionnaire.is_key_pass_use',
			'ng-model' => 'questionnaires.Questionnaire.key_phrase'
			)
			);
			?>

			<?php echo $this->Form->input('is_anonymity',
			array(
			'type' => 'checkbox',
			'label' => false,
			'before' => '<div class="checkbox"><label>',
			'after' => __d('questionnaires', 'anonymous answer') . '</label></div>',
			'ng-model' => 'questionnaires.Questionnaire.is_anonymity',
			'error' => false,
			)
			);
			?>
			<?php echo $this->element(
			'NetCommons.errors', [
			'errors' => $this->validationErrors,
			'model' => 'Questionnaire',
			'field' => 'is_anonymity',
			]) ?>

			<?php echo $this->Form->input('is_repeat_allow',
			array(
			'type' => 'checkbox',
			'label' => false,
			'before' => '<div class="checkbox"><label>',
			'after' => __d('questionnaires', 'forgive the repetition of the answer') . '</label></div>',
			'ng-model' => 'questionnaires.Questionnaire.is_repeat_allow',
			'error' => false,
			)
			);
			?>
			<?php echo $this->element(
			'NetCommons.errors', [
			'errors' => $this->validationErrors,
			'model' => 'Questionnaire',
			'field' => 'is_repeat_allow',
			]) ?>

			<?php echo $this->Form->input('is_image_authentication',
			array(
			'type' => 'checkbox',
			'label' => false,
			'before' => '<div class="checkbox"><label>',
			'after' => __d('questionnaires', 'do image authentication') . '</label></div>',
			'ng-model' => 'questionnaires.Questionnaire.is_image_authentication',
			'error' => false,
			)
			);
			?>
			<?php echo $this->element(
			'NetCommons.errors', [
			'errors' => $this->validationErrors,
			'model' => 'Questionnaire',
			'field' => 'is_image_authentication',
			]) ?>

			<?php echo $this->Form->input('is_answer_mail_send',
			array(
			'type' => 'checkbox',
			'label' => false,
			'before' => '<div class="checkbox"><label>',
			'after' => __d('questionnaires', 'Deliver e-mail when submitted?') . '</label></div>',
			'ng-model' => 'questionnaires.Questionnaire.is_answer_mail_send',
			'error' => false,
			)
			);
			?>
			<?php echo $this->element(
			'NetCommons.errors', [
			'errors' => $this->validationErrors,
			'model' => 'Questionnaire',
			'field' => 'is_answer_mail_send',
			]) ?>
		</div>

		<label class="h3"><?php echo __d('questionnaires', 'Thanks page message settings'); ?></label>
		<div class="form-group questionnaire-group">
			<div class="nc-wysiwyg-alert">
				<?php echo $this->Form->textarea('thanks_content',
				array(
				'class' => 'form-control',
				'ng-model' => 'questionnaires.Questionnaire.thanks_content',
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
