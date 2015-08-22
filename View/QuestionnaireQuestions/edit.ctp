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
<?php echo $this->element('Questionnaires.scripts'); ?>
<?php echo $this->Html->script(
array(
'/components/moment/min/moment.min.js',
'/components/moment/min/moment-with-locales.min.js',
),
array(
'plugin' => false,
'once' => true,
'inline' => false
)
);
?>

<?php echo $this->Html->script('Questionnaires.questionnaires_edit.js');?>
<?php echo $this->Html->script('Questionnaires.questionnaires_edit_question.js');?>

<?php
	if ($isPublished) {
		$disabled = 'disabled';
		$elementFolder = 'Questionnaires.Questions/after_published_edit/';
	} else {
		$disabled = '';
		$elementFolder = 'Questionnaires.Questions/edit/';
	}
?>

<div id="nc-questionnaires-setting-list-<?php echo (int)$frameId; ?>"
	 ng-controller="Questionnaires.edit.question"
	 ng-init="initialize(<?php echo (int)$frameId; ?>,
	 						<?php echo (int)$isPublished; ?>,
							<?php echo h(json_encode($jsQuestionnaire)); ?>,
							'<?php echo h($newPageLabel); ?>',
							'<?php echo h($newQuestionLabel); ?>',
							'<?php echo h($newChoiceLabel); ?>',
							'<?php echo h($newChoiceColumnLabel); ?>',
							'<?php echo h($newChoiceOtherLabel); ?>')">

	<?php echo $this->Form->create('QuestionnaireQuestion', array(
	'type' => 'post',
	'novalidate' => true,
	'ng-keydown' => 'handleKeydown($event)'
	)); ?>

	<?php $this->Form->unlockField('Questionnaire.origin_id'); ?>
	<?php $this->Form->unlockField('Questionnaire.key'); ?>
	<?php $this->Form->unlockField('QuestionnairePage'); ?>

	<?php echo $this->Form->hidden('Frame.id', array(
	'value' => $frameId,
	)); ?>
	<?php echo $this->Form->hidden('Block.id', array(
	'value' => $blockId,
	)); ?>

	<div class="modal-body">

		<?php echo $this->element('Questionnaires.edit_flow_chart', array('current' => '1')); ?>

		<div class="form-group">
			<?php echo $this->Form->input(
				'Questionnaire.title',
				array(
					'label' => __d('questionnaires', 'Questionnaire title') . $this->element('NetCommons.required'),
					'class' => 'form-control',
					'ng-model' => 'questionnaire.questionnaire.title',
					'placeholder' => __d('questionnaires', 'Please input questionnaire title')
				));
			?>
			<?php echo $this->Form->hidden(
			'Questionnaire.origin_id',
			array(
			'ng-value' => 'questionnaire.questionnaire.originId',
			));
			?>
			<?php echo $this->Form->hidden(
			'Questionnaire.key',
			array(
			'ng-value' => 'questionnaire.questionnaire.key',
			));
			?>
		</div>


		<tabset>
			<tab ng-repeat="(pageIndex, page) in questionnaire.questionnairePage" active="page.tabActive">
				<tab-heading>
					{{pageIndex+1}}<span class="glyphicon glyphicon-exclamation-sign text-danger" ng-if="page.hasError"></span>
				</tab-heading>
				<div class="tab-body">
					<div class="form-group" ng-if="isPublished == 0">

						<button class="btn btn-success pull-right" type="button" ng-click="addQuestion($event, pageIndex)">
							<span class="glyphicon glyphicon-plus"></span>
							<?php echo __d('questionnaires', 'Add Question'); ?>
						</button>

						<div class="pull-left form-inline">
							<label><?php echo __d('questionnaires', 'next display page'); ?></label>
							<select name="data[QuestionnairePage][{{pageIndex}}][next_page_sequence]"
									class="form-control input-sm"
									ng-disabled="isDisabledSetSkip(page, null)">
								<option ng-repeat="nextPage in questionnaire.questionnairePage | filter: greaterThan('pageSequence', page)"
										ng-selected="page.nextPageSequence == nextPage.pageSequence || (page.nextPageSequence == null && nextPage.pageSequence == pageIndex+1)"
										value="{{nextPage.pageSequence}}">
									{{1 * nextPage.pageSequence + 1}}
								</option>
								<option value="<?php echo QuestionnairesComponent::SKIP_GO_TO_END ?>"
										ng-selected="page.nextPageSequence == <?php echo QuestionnairesComponent::SKIP_GO_TO_END ?> || (page.nextPageSequence >= questionnaire.questionnairePage.length)">
									<?php echo __d('questionnaires', 'goto end'); ?>
								</option>
							</select>
						</div>

					</div>

					<div class="clearfix"></div>

					<?php
						echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.page_sequence',
						array(
							'ng-value' => 'page.pageSequence'
						));
						echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.id',
						array(
						'ng-value' => 'page.id'
						));
						echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.origin_id',
						array(
						'ng-value' => 'page.originId'
						));
						echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.key',
						array(
						'ng-value' => 'page.key'
						));
						$this->Form->hidden('QuestionnairePage.{{pageIndex}}.page_title',
						array(
						'ng-value' => 'page.pageTitle'
						));
					?>

					<accordion close-others="true">
						<accordion-group
								class="form-horizontal"
								ng-repeat="(qIndex, question) in page.questionnaireQuestion"
								is-open="question.isOpen"
								>
							<accordion-heading>

								<div class="pull-right" ng-if="isPublished == 0">
									<div class="btn-group" dropdown dropdown-append-to-body>
										<button type="button" class="btn btn-default" dropdown-toggle >
											<?php echo __d('questionnaires', 'copy to another page'); ?>
											<span class="caret"></span>
										</button>
										<ul class="dropdown-menu" role="menu">
											<li role="presentation" class="dropdown-header"><?php echo __d('questionnaires', 'destination page number'); ?></li>
											<li ng-repeat="(copyPageIndex, copyPage) in questionnaire.questionnairePage">
												<a href="#" ng-click="copyQuestionToAnotherPage($event, pageIndex, qIndex, copyPage.pageSequence)">{{1 * copyPage.pageSequence + 1}}</a>
											</li>
										</ul>
									</div>
									<button class="btn btn-danger " type="button"
											ng-disabled="page.questionnaireQuestion.length < 2"
											ng-click="deleteQuestion($event, pageIndex, qIndex, '<?php echo __d('questionnaires', 'Do you want to delete this question ?'); ?>')">
										<span class="glyphicon glyphicon-remove"> </span>
									</button>
								</div>

								<button ng-if="isPublished == 0"
										class="btn btn-default pull-left"
										type="button"
										ng-disabled="$first"
										ng-click="moveQuestion($event, pageIndex, qIndex, qIndex-1)">
									<span class="glyphicon glyphicon-arrow-up"></span>
								</button>

								<button ng-if="isPublished == 0"
										class="btn btn-default pull-left"
										type="button"
										ng-disabled="$last"
										ng-click="moveQuestion($event, pageIndex, qIndex, qIndex+1)">
									<span class="glyphicon glyphicon-arrow-down"></span>
								</button>

								<span class="questionnaire-accordion-header-title">

									<span class="glyphicon glyphicon-exclamation-sign text-danger" ng-if="question.hasError"></span>

									{{question.questionValue}}
									<strong ng-if="question.isRequire != 0" class="text-danger h4">
										<?php echo __d('net_commons', 'Required'); ?>
									</strong>
									<span ng-if="question.isSkip != 0" class="badge">
										<?php echo __d('questionnaires', 'Skip'); ?>
									</span>
								</span>
								<span class="glyphicon glyphicon-exclamation-sign text-danger" ng-if="question.hasError"></span>

								<div class="clearfix"></div>

							</accordion-heading>

							<?php
								echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.question_sequence',
							array(
							'ng-value' => 'question.questionSequence'
							));
							echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.id',
							array(
							'ng-value' => 'question.id'
							));
							echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.origin_id',
							array(
							'ng-value' => 'question.originId'
							));
							echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.key',
							array(
							'ng-value' => 'question.key'
							));
							echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.is_result_display',
							array(
							'ng-value' => 'question.isResultDisplay'
							));
							echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.result_display_type',
							array(
							'ng-value' => 'question.resultDisplayType'
							));
							?>

							<?php echo $this->element('Questionnaires.Questions/edit_form_controller_set',
							array(
								'name' => 'is_require',
								'jsName' => 'isRequire',
								'label' => __d('questionnaires', 'Required'),
								'input' => $this->Form->input('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.is_require',
									array(
									'type' => 'checkbox',
									'div' => array('class' => 'checkbox ' . $disabled),
									'label' => __d('questionnaires', 'set answer to this question is required'),
									'ng-checked' => 'question.isRequire == ' . QuestionnairesComponent::USES_USE,
									'ng-model' => 'question.isRequire',
									'ng-disabled' => 'isPublished != 0',
									)),
							));?>

							<?php echo $this->element('Questionnaires.Questions/edit_form_controller_set',
							array(
								'name' => 'question_value',
								'jsName' => 'questionValue',
								'label' => __d('questionnaires', 'question title') . $this->element('NetCommons.required'),
								'input' => $this->Form->input('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.question_value',
									array(
										'type' => 'text',
										'label' => false,
										'class' => 'form-control',
										'ng-model' => 'question.questionValue',
										'required' => 'required',
										'ng-disabled' => 'isPublished != 0',
									)),
							));?>

							<?php
								if ($isPublished == 0) {
									$textarea = $this->Form->textarea('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.description',
										array(
										'class' => 'form-control',
										'id' => false,	// id に{{}}のバインド値が混じっているとtinymceが発動しない
										'ng-model' => 'question.description',
										'ui-tinymce' => 'tinymce.options',
										'rows' => 5,
										));
								} else {
									$textarea = $this->Form->textarea('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.description',
										array(
										'class' => 'form-control',
										'ng-model' => 'question.description',
										'rows' => 5,
										'ng-disabled' => 'isPublished != 0',
										));
								}
								echo $this->element('Questionnaires.Questions/edit_form_controller_set',
								array(
									'name' => 'description',
									'jsName' => 'description',
									'label' => __d('questionnaires', 'question sentence'),
									'input' => '<div class="nc-wysiwyg-alert">' . $textarea . '</div>',
							));?>

							<?php echo $this->element('Questionnaires.Questions/edit_form_controller_set',
								array(
								'name' => 'question_type',
								'jsName' => 'questionType',
								'label' => __d('questionnaires', 'Question type') . $this->element('NetCommons.required'),
								'input' => $this->Form->select('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.question_type',
									$questionTypeOptions,
									array(
										'class' => 'form-control',
										'ng-model' => 'question.questionType',
										'ng-change' => 'changeQuestionType($event, {{pageIndex}}, {{qIndex}})',
										'ng-disabled' => 'isPublished != 0',
										'empty' => null
									)),
							));?>
							<div class="row form-group">
								<div class="col-sm-12">
									<div class="well">
										<div ng-if="question.questionType == <?php echo QuestionnairesComponent::TYPE_SELECTION; ?>">
											<?php echo $this->element($elementFolder . 'question_setting_alternative', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}', 'isPublisehd' => $isPublished)); ?>
										</div>
										<div ng-if="question.questionType == <?php echo QuestionnairesComponent::TYPE_MULTIPLE_SELECTION; ?>">
											<?php echo $this->element($elementFolder . 'question_setting_alternative', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}')); ?>
										</div>
										<div ng-if="question.questionType == <?php echo QuestionnairesComponent::TYPE_TEXT; ?>">
											<?php echo $this->element($elementFolder . 'question_setting_text', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}')); ?>
										</div>
										<div ng-if="question.questionType == <?php echo QuestionnairesComponent::TYPE_TEXT_AREA; ?>">
											<?php /* 複数行テキストの場合は詳細設定がないです */ ?>
										</div>
										<div ng-if="question.questionType == <?php echo QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST; ?>">
											<?php echo $this->element($elementFolder . 'question_setting_matrix_alternative', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}')); ?>
										</div>
										<div ng-if="question.questionType == <?php echo QuestionnairesComponent::TYPE_MATRIX_MULTIPLE; ?>">
											<?php echo $this->element($elementFolder . 'question_setting_matrix_alternative', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}')); ?>
										</div>
										<div ng-if="question.questionType == <?php echo QuestionnairesComponent::TYPE_DATE_AND_TIME; ?>">
											<?php echo $this->element($elementFolder . 'question_setting_date', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}')); ?>
										</div>
										<div ng-if="question.questionType == <?php echo QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX; ?>">
											<?php echo $this->element($elementFolder . 'question_setting_alternative', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}')); ?>
										</div>
									</div>
								</div>
							</div >

						</accordion-group>
					</accordion>

					<div class="form-group text-right"
						 ng-if="isPublished == 0"
						 ng-show="page.questionnaireQuestion.length > 0">
						<button class="btn btn-success" type="button" ng-click="addQuestion($event, pageIndex)">
							<span class="glyphicon glyphicon-plus"></span>
							<?php echo __d('questionnaires', 'Add Question'); ?>
						</button>
					</div>

					<div class="text-center" ng-if="isPublished == 0">
						<button class="btn btn-danger" type="button"
								ng-disabled="questionnaire.questionnairePage.length < 2"
								ng-click="deletePage($index, '<?php echo __d('questionnaires', 'Do you want to delete this page?'); ?>')">
							<span class="glyphicon glyphicon-remove"></span><?php echo __d('questionnaires', 'Delete this page'); ?>
						</button>
					</div>
				</div>
			</tab>
			<tab class="questionnaire-add-page-tab" ng-click="addPage($event)" ng-if="isPublished == 0">
				<tab-heading>
					<span class="glyphicon glyphicon-plus">
					</span>
					<span class=""><?php echo __d('questionnaires', 'Add Page'); ?></span>
				</tab-heading>
			</tab>
		</tabset>

	</div>
	<div class="modal-footer">
		<div class="text-center">
			<?php echo $this->BackToPage->backToPageButton(__d('net_commons', 'Cancel'), 'remove'); ?>
			<?php echo $this->Form->button(
			__d('net_commons', 'NEXT') . ' <span class="glyphicon glyphicon-chevron-right"></span>',
			array(
			'class' => 'btn btn-primary',
			'name' => 'next_' . '',
			)) ?>
		</div>
	</div>
	<?php echo $this->Form->end(); ?>
</div>