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

echo $this->element('Questionnaires.scripts');
echo $this->NetCommonsHtml->script(array(
	'/components/moment/min/moment.min.js',
	'/components/moment/min/moment-with-locales.min.js',
	'/components/tinymce-dist/tinymce.min.js',
	'/components/angular-ui-tinymce/src/tinymce.js',
	'/net_commons/js/wysiwyg.js',
	'/questionnaires/js/questionnaires_edit_question.js',
));
$jsQuestionnaire = NetCommonsAppController::camelizeKeyRecursive(QuestionnairesAppController::changeBooleansToNumbers($this->data));
?>

<div id="nc-questionnaires-question-edit-result"
	 ng-controller="Questionnaires.edit.question"
	 ng-init="initialize(<?php echo Current::read('Frame.id'); ?>,
	 						<?php echo (int)$isPublished; ?>,
							<?php echo h(json_encode($jsQuestionnaire)); ?>)">

	<?php echo $this->NetCommonsForm->create('QuestionnaireQuestion'); ?>

		<?php $this->NetCommonsForm->unlockField('QuestionnairePage'); ?>

		<?php echo $this->NetCommonsForm->hidden('Questionnaire.key'); ?>
		<?php
		echo $this->NetCommonsForm->hidden('Questionnaire.status', array('value' => WorkflowComponent::STATUS_IN_DRAFT));
		?>
		<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
		<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>

		<div class="modal-body">

			<?php echo $this->element('Questionnaires.QuestionnaireEdit/edit_flow_chart', array('current' => '2')); ?>

			<?php echo $this->element('Questionnaires.QuestionnaireEdit/questionnaire_title'); ?>

			<div class="row form-group questionnaire-group">
				<label ><?php echo __d('questionnaires', 'Published aggregate results'); ?></label>
					<?php echo $this->NetCommonsForm->input('Questionnaire.is_total_show',
							array('type' => 'radio',
							'options' => array(QuestionnairesComponent::EXPRESSION_NOT_SHOW => __d('questionnaires', 'not disclose the total result'), QuestionnairesComponent::EXPRESSION_SHOW => __d('questionnaires', 'publish aggregate result')),
							'legend' => false,
							'class' => '',
							'label' => false,
							'before' => '<div class="radio"><label>',
							'separator' => '</label></div><div class="radio"><label>',
							'after' => '</label></div>',
							'ng-model' => 'questionnaire.questionnaire.isTotalShow'
					)); ?>
			</div>

			<div ng-show="questionnaire.questionnaire.isTotalShow == <?php echo QuestionnairesComponent::EXPRESSION_SHOW; ?>">

				<div class="form-group questionnaire-group">
					<label><?php echo __d('questionnaires', 'Text to be displayed in the aggregate results page'); ?></label>
					<div class="nc-wysiwyg-alert">
						<?php echo $this->NetCommonsForm->textarea('Questionnaire.total_comment',
						array(
						'class' => 'form-control',
						'ng-model' => 'questionnaire.questionnaire.totalComment',
						'ui-tinymce' => 'tinymce.options',
						'rows' => 5,
						)) ?>
					</div>
				</div>

				<div class="questionnaire-group">
					<label><?php echo __d('questionnaires', 'Question you want to display the aggregate results'); ?></label>
					<accordion ng-repeat="(pageIndex, page) in questionnaire.questionnairePage">
						<accordion-group ng-repeat="(qIndex, question) in page.questionnaireQuestion">

							<accordion-heading>
								<?php echo $this->element('Questionnaires.QuestionnaireEdit/EditResult/accordion_heading'); ?>
							</accordion-heading>

							<?php echo $this->element('Questionnaires.QuestionnaireEdit/EditResult/is_display_set'); ?>

							<div ng-show="question.isResultDisplay == <?php echo QuestionnairesComponent::EXPRESSION_SHOW; ?>">

								<?php echo $this->element('Questionnaires.QuestionnaireEdit/EditResult/display_type_set'); ?>

								<div class="form-group" ng-show="question.resultDisplayType != <?php echo QuestionnairesComponent::RESULT_DISPLAY_TYPE_TABLE; ?>">

									<?php echo $this->element('Questionnaires.QuestionnaireEdit/EditResult/graph_color_set'); ?>

								</div>
							</div>
						</accordion-group>
					</accordion>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<div class="text-center">
				<?php echo $this->BackTo->pageLinkButton(__d('net_commons', 'Cancel'), array('icon' => 'remove')); ?>
				<?php echo $this->Backto->linkButton(__d('net_commons', 'BACK'), $backUrl, array('icon' => 'chevron-left')); ?>
				<?php echo $this->Button->save(__d('net_commons', 'NEXT'), array('icon' => 'chevron-right')) ?>
			</div>
		</div>
	<?php echo $this->NetCommonsForm->end(); ?>
</div>