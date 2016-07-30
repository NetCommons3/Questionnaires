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
	'/questionnaires/js/questionnaires_edit_question.js',
));
$jsQuestionnaire = NetCommonsAppController::camelizeKeyRecursive(QuestionnairesAppController::changeBooleansToNumbers($this->data));
?>

<article id="nc-questionnaires-question-edit-result"
	 ng-controller="Questionnaires.edit.question"
	 ng-init="initialize(<?php echo Current::read('Frame.id'); ?>,
	 						<?php echo (int)$isPublished; ?>,
							<?php echo h(json_encode($jsQuestionnaire)); ?>)">

	<?php echo $this->element('Questionnaires.QuestionnaireEdit/questionnaire_title'); ?>

	<?php echo $this->Wizard->navibar('edit_result'); ?>

	<div class="panel panel-default">

	<?php echo $this->NetCommonsForm->create('QuestionnaireQuestion', $postUrl); ?>

		<?php $this->NetCommonsForm->unlockField('QuestionnairePage'); ?>

		<?php echo $this->NetCommonsForm->hidden('Questionnaire.key'); ?>
		<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
		<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>

		<div class="panel-body">

			<div class="form-group">
				<?php echo $this->NetCommonsForm->input('Questionnaire.is_total_show',
						array('type' => 'radio',
						'options' => array(QuestionnairesComponent::EXPRESSION_NOT_SHOW => __d('questionnaires', 'not disclose the total result'), QuestionnairesComponent::EXPRESSION_SHOW => __d('questionnaires', 'publish aggregate result')),
						'label' => __d('questionnaires', 'Published aggregate results'),
						'ng-model' => 'questionnaire.questionnaire.isTotalShow'
				)); ?>
			</div>

			<div ng-show="questionnaire.questionnaire.isTotalShow == <?php echo QuestionnairesComponent::EXPRESSION_SHOW; ?>">

				<div class="form-group">
					<?php echo $this->NetCommonsForm->wysiwyg('Questionnaire.total_comment',
					array(
					'label' => __d('questionnaires', 'Text to be displayed in the aggregate results page'),
					'ng-model' => 'questionnaire.questionnaire.totalComment',
					)) ?>
				</div>

				<div class="">
					<label><?php echo __d('questionnaires', 'Question you want to display the aggregate results'); ?></label>
					<div ng-cloak uib-accordion ng-repeat="(pageIndex, page) in questionnaire.questionnairePage">
						<div uib-accordion-group ng-repeat="(qIndex, question) in page.questionnaireQuestion" class="{{getResultAccordionClass(question)}}">

							<div uib-accordion-heading>
								<?php echo $this->element('Questionnaires.QuestionnaireEdit/EditResult/accordion_heading'); ?>
							</div>

							<?php echo $this->element('Questionnaires.QuestionnaireEdit/EditResult/is_display_set'); ?>

							<div ng-show="question.isResultDisplay == <?php echo QuestionnairesComponent::EXPRESSION_SHOW; ?>">

								<?php echo $this->element('Questionnaires.QuestionnaireEdit/EditResult/display_type_set'); ?>

								<div class="form-group" ng-show="question.resultDisplayType != <?php echo QuestionnairesComponent::RESULT_DISPLAY_TYPE_TABLE; ?>">

									<?php echo $this->element('Questionnaires.QuestionnaireEdit/EditResult/graph_color_set'); ?>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer text-center">
			<?php echo $this->Wizard->buttons('edit_result', $cancelUrl); ?>
		</div>
	<?php echo $this->NetCommonsForm->end(); ?>
</article>