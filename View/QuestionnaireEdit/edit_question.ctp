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

<?php
	if ($isPublished) {
		$elementFolder = 'Questionnaires.QuestionnaireEdit/EditQuestion/options_after_published/';
	} else {
		$elementFolder = 'Questionnaires.QuestionnaireEdit/EditQuestion/options_before_published/';
	}
?>

<article id="nc-questionnaires-question-edit"
	 ng-controller="Questionnaires.edit.question"
	 ng-init="initialize(<?php echo Current::read('Frame.id'); ?>,
	 						<?php echo (int)$isPublished; ?>,
							<?php echo h(json_encode($jsQuestionnaire)); ?>,
							'<?php echo h($newPageLabel); ?>',
							'<?php echo h($newQuestionLabel); ?>',
							'<?php echo h($newChoiceLabel); ?>',
							'<?php echo h($newChoiceColumnLabel); ?>',
							'<?php echo h($newChoiceOtherLabel); ?>')">

	<?php echo $this->element('Questionnaires.QuestionnaireEdit/questionnaire_title'); ?>

	<?php echo $this->Wizard->navibar('edit_question'); ?>

	<div class="panel panel-default">

		<?php /* echo $this->element('Questionnaires.QuestionnaireEdit/questionnaire_description'); */ ?>

		<?php echo $this->NetCommonsForm->create('QuestionnaireQuestion', $postUrl); ?>

		<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
		<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>
		<?php echo $this->NetCommonsForm->hidden('Questionnaire.key');?>

		<?php $this->NetCommonsForm->unlockField('QuestionnairePage'); ?>

		<div class="panel-body">

			<uib-tabset active="activeTabIndex">
				<uib-tab ng-repeat="(pageIndex, page) in questionnaire.questionnairePage" index="$index">
					<uib-tab-heading>
						{{pageIndex+1}}<span class="glyphicon glyphicon-exclamation-sign text-danger" ng-if="page.hasError"></span>
					</uib-tab-heading>

					<div class="tab-body">
						<?php echo $this->element('Questionnaires.QuestionnaireEdit/EditQuestion/add_question_button', array('isPublished' => $isPublished)); ?>
						<div class="clearfix"></div>

						<?php echo $this->element('Questionnaires.QuestionnaireEdit/EditQuestion/hidden_page_info_set'); ?>

					<uib-accordion close-others="true">
						<uib-accordion-group
								class="form-horizontal"
								ng-repeat="(qIndex, question) in page.questionnaireQuestion"
								is-open="question.isOpen">

							<uib-accordion-heading>
								<?php /* 質問ヘッダーセット（移動ボタン、削除ボタンなどの集合体 */
									echo $this->element('Questionnaires.QuestionnaireEdit/EditQuestion/accordion_heading', array('isPublished' => $isPublished)); ?>
								<div class="clearfix"></div>
							</uib-accordion-heading>

							<?php echo $this->element('Questionnaires.QuestionnaireEdit/EditQuestion/hidden_question_info_set'); ?>

							<?php /* ここから質問本体設定 */
								/* 必須? */
								echo $this->QuestionEdit->questionInput('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.is_require',
									__d('questionnaires', 'Required'),
									array(	'type' => 'checkbox',
											'class' => '',
											'ng-checked' => 'question.isRequire == ' . QuestionnairesComponent::USES_USE,
											'ng-model' => 'question.isRequire',
											'ng-disabled' => 'isPublished != 0',
									),
									__d('questionnaires', 'set answer to this question is required'));
								/* 質問タイトル */
								echo $this->QuestionEdit->questionInput('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.question_value',
									__d('questionnaires', 'question title'),
									array('type' => 'text',
										'ng-model' => 'question.questionValue',
										'required' => 'required',
										'ng-disabled' => 'isPublished != 0',
									));
								/* 質問文 */
								echo $this->QuestionEdit->questionInput('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.description',
									__d('questionnaires', 'question sentence'),
									array('type' => 'wysiwyg',
										'id' => false,
										'ng-model' => 'question.description',
										'ui-tinymce' => 'tinymce.options',
										'rows' => 5,
										'ng-disabled' => 'isPublished != 0',
									));
								/* 質問種別 */
								echo $this->QuestionEdit->questionInput('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.question_type',
									__d('questionnaires', 'Question type'),
									array('type' => 'select',
										'required' => true,
										'options' => $questionTypeOptions,
										'ng-model' => 'question.questionType',
										'ng-change' => 'changeQuestionType($event, {{pageIndex}}, {{qIndex}})',
										'ng-disabled' => 'isPublished != 0',
										'empty' => null
									));
							?>
							<div class="row form-group">
								<div class="col-sm-12">
									<div class="well">
										<div ng-if="question.questionType == <?php echo QuestionnairesComponent::TYPE_SELECTION; ?>">
											<?php echo $this->element($elementFolder . 'simple_choice_option'); ?>
										</div>
										<div ng-if="question.questionType == <?php echo QuestionnairesComponent::TYPE_MULTIPLE_SELECTION; ?>">
											<?php echo $this->element($elementFolder . 'simple_choice_option'); ?>
										</div>
										<div ng-if="question.questionType == <?php echo QuestionnairesComponent::TYPE_TEXT; ?>">
											<?php echo $this->element($elementFolder . 'text_option'); ?>
										</div>
										<div ng-if="question.questionType == <?php echo QuestionnairesComponent::TYPE_TEXT_AREA; ?>">
											<?php echo $this->element($elementFolder . 'text_area_option'); ?>
										</div>
										<div ng-if="question.questionType == <?php echo QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST; ?>">
											<?php echo $this->element($elementFolder . 'matrix_choice_option'); ?>
										</div>
										<div ng-if="question.questionType == <?php echo QuestionnairesComponent::TYPE_MATRIX_MULTIPLE; ?>">
											<?php echo $this->element($elementFolder . 'matrix_choice_option'); ?>
										</div>
										<div ng-if="question.questionType == <?php echo QuestionnairesComponent::TYPE_DATE_AND_TIME; ?>">
											<?php echo $this->element($elementFolder . 'date_option'); ?>
										</div>
										<div ng-if="question.questionType == <?php echo QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX; ?>">
											<?php echo $this->element($elementFolder . 'simple_choice_option'); ?>
										</div>
									</div>
								</div>
							</div >

						</uib-accordion-group>
					</uib-accordion>

					<?php echo $this->element('Questionnaires.QuestionnaireEdit/EditQuestion/add_question_button'); ?>

					<?php if (! $isPublished): ?>
						<div class="text-center">
							<button class="btn btn-danger" type="button"
									ng-disabled="questionnaire.questionnairePage.length < 2"
									ng-click="deletePage($index, '<?php echo __d('questionnaires', 'Do you want to delete this page?'); ?>')">
								<span class="glyphicon glyphicon-remove"></span><?php echo __d('questionnaires', 'Delete this page'); ?>
							</button>
						</div>
					<?php endif; ?>
				</div>
			</uib-tab>
			<?php if (! $isPublished): ?>
				<a class="questionnaire-add-page-tab" ng-click="addPage($event)">
					<span class="glyphicon glyphicon-plus"></span>
					<span class=""><?php echo __d('questionnaires', 'Add Page'); ?></span>
				</a>
			<?php endif; ?>
		</uib-tabset>

	</div>

		<div class="panel-footer text-center">
			<?php echo $this->Wizard->buttons('edit_question'); ?>
		</div>


	<?php echo $this->NetCommonsForm->end(); ?>
</article>