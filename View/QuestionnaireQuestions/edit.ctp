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
							<?php echo h(json_encode($questionnaire)); ?>,
							<?php echo h(json_encode($questionnaireValidationErrors)); ?>,
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
					'ng-model' => 'questionnaire.Questionnaire.title',
					'placeholder' => __d('questionnaires', 'Please input questionnaire title')
				));
			?>
			<?php echo $this->Form->hidden(
			'Questionnaire.origin_id',
			array(
			'value' => isset($questionnaire['Questionnaire']['origin_id']) ? $questionnaire['Questionnaire']['origin_id'] : 0,
			));
			?>
			<?php echo $this->Form->hidden(
			'Questionnaire.key',
			array(
			'value' => $questionnaire['Questionnaire']['key'],
			));
			?>
		</div>


		<tabset>
			<tab ng-repeat="(pageIndex, page) in questionnaire.QuestionnairePage" active="page.tab_active">
				<tab-heading>
					{{pageIndex+1}}
				</tab-heading>
				<div class="tab-body">
					<div class="form-group text-right" ng-if="isPublished == 0">
						<button class="btn btn-success" type="button" ng-click="addQuestion($event, pageIndex)">
							<span class="glyphicon glyphicon-plus"></span>
							<?php echo __d('questionnaires', 'Add Question'); ?>
						</button>
					</div>

					<div class="clearfix"></div>

					<?php
						echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.page_sequence',
						array(
							'ng-value' => 'page.page_sequence'
						));
						echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.id',
						array(
						'ng-value' => 'page.id'
						));
						echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.origin_id',
						array(
						'ng-value' => 'page.origin_id'
						));
						echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.key',
						array(
						'ng-value' => 'page.key'
						));
						$this->Form->hidden('QuestionnairePage.{{pageIndex}}.page_title',
						array(
						'ng-value' => 'page.page_title'
						));
					?>

					<accordion close-others="true">
						<accordion-group
								class="form-horizontal"
								ng-repeat="(qIndex, question) in page.QuestionnaireQuestion"
								is-open="question.isOpen"
								ng-class="{'panel-danger':(errors.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex])}">
							<accordion-heading>

								<div class="pull-right" ng-if="isPublished == 0">
									<div class="btn-group">
										<button type="button" class="btn btn-default dropdown-toggle" ng-click="question.isOpen = true">
											<?php echo __d('questionnaires', 'move to another page'); ?>
											<span class="caret"></span>
										</button>
										<ul class="dropdown-menu" role="menu">
											<li role="presentation" class="dropdown-header"><?php echo __d('questionnaires', 'destination page number'); ?></li>
											<li ng-repeat="(movePageIndex, move_page) in questionnaire.QuestionnairePage | filter: {page_sequence: '!' + page.page_sequence}">
												<a href="#" ng-click="moveQuestionToAnotherPage($event, pageIndex, qIndex, movePageIndex)">{{1 * move_page.page_sequence + 1}}</a>
											</li>
										</ul>
									</div>
									<button class="btn btn-danger " type="button"
											ng-disabled="page.QuestionnaireQuestion.length < 2"
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
									{{question.question_value}}
									<strong ng-if="question.is_require" class="text-danger h4">
										<?php echo __d('net_commons', 'Required'); ?>
									</strong>
								</span>
								<span ng-if="errors.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex]">
									<?php echo __d('questionnaires', 'There is an error'); ?>
								</span>

								<div class="clearfix"></div>

							</accordion-heading>

							<?php
								echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.question_sequence',
							array(
							'ng-value' => 'question.question_sequence'
							));
							echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.id',
							array(
							'ng-value' => 'question.id'
							));
							echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.origin_id',
							array(
							'ng-value' => 'question.origin_id'
							));
							echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.key',
							array(
							'ng-value' => 'question.key'
							));
							echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.is_result_display',
							array(
							'ng-value' => 'question.is_result_display'
							));
							echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.result_display_type',
							array(
							'ng-value' => 'question.result_display_type'
							));
							?>

							<?php echo $this->element('Questionnaires.Questions/edit_form_controller_set',
							array(
								'name' => 'is_require',
								'label' => __d('questionnaires', 'Required'),
								'disabled' => $disabled,
								'input' => $this->Form->input('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.is_require',
									array(
									'type' => 'checkbox',
									'div' => array('class' => 'checkbox ' . $disabled),
									'label' => __d('questionnaires', 'set answer to this question is required'),
									'value' => true,
									'ng-model' => 'question.is_require',
									'ng-disabled' => 'isPublished != 0',
									)),
								'isPublished' => $isPublished,
								'value' => 'question.is_require'
							));?>

							<?php echo $this->element('Questionnaires.Questions/edit_form_controller_set',
							array(
								'name' => 'question_value',
								'label' => __d('questionnaires', 'question title') . $this->element('NetCommons.required'),
								'disabled' => $disabled,
								'input' => $this->Form->input('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.question_value',
									array(
										'type' => 'text',
										'label' => false,
										'class' => 'form-control',
										'ng-model' => 'question.question_value',
										'required' => 'required',
										'ng-disabled' => 'isPublished != 0',
									)),
								'isPublished' => $isPublished,
								'value' => 'question.question_value'
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
									'label' => __d('questionnaires', 'question sentence'),
									'disabled' => $disabled,
									'input' => '<div class="nc-wysiwyg-alert">' . $textarea . '</div>',
								'isPublished' => $isPublished,
								'value' => 'question.description'
							));?>

							<?php echo $this->element('Questionnaires.Questions/edit_form_controller_set',
								array(
								'name' => 'question_type',
								'label' => __d('questionnaires', 'Question type') . $this->element('NetCommons.required'),
								'disabled' => $disabled,
								'input' => $this->Form->select('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.question_type',
									$questionTypeOptions,
									array(
										'class' => 'form-control',
										'ng-model' => 'question.question_type',
										'ng-disabled' => 'isPublished != 0',
										'empty' => null
									)),
								'isPublished' => $isPublished,
								'value' => 'question.question_type'
							));?>
							<div class="row form-group">
								<div class="col-sm-12">
									<div class="well">
										<div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_SELECTION; ?>">
											<?php echo $this->element($elementFolder . 'question_setting_alternative', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}', 'isPublisehd' => $isPublished)); ?>
										</div>
										<div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_MULTIPLE_SELECTION; ?>">
											<?php echo $this->element($elementFolder . 'question_setting_alternative', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}')); ?>
										</div>
										<div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_TEXT; ?>">
											<?php echo $this->element($elementFolder . 'question_setting_text', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}')); ?>
										</div>
										<div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_TEXT_AREA; ?>">
											<?php /* 複数行テキストの場合は詳細設定がないです */ ?>
										</div>
										<div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST; ?>">
											<?php echo $this->element($elementFolder . 'question_setting_matrix_alternative', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}')); ?>
										</div>
										<div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_MATRIX_MULTIPLE; ?>">
											<?php echo $this->element($elementFolder . 'question_setting_matrix_alternative', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}')); ?>
										</div>
										<div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_DATE_AND_TIME; ?>">
											<?php echo $this->element($elementFolder . 'question_setting_date', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}')); ?>
										</div>
										<div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX; ?>">
											<?php echo $this->element($elementFolder . 'question_setting_alternative', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}')); ?>
										</div>
									</div>
								</div>
							</div >

						</accordion-group>
					</accordion>

					<div class="form-group text-right"
						 ng-if="isPublished == 0"
						 ng-show="page.QuestionnaireQuestion.length > 0">
						<button class="btn btn-success" type="button" ng-click="addQuestion($event, pageIndex)">
							<span class="glyphicon glyphicon-plus"></span>
							<?php echo __d('questionnaires', 'Add Question'); ?>
						</button>
					</div>

					<div class="text-center" ng-if="isPublished == 0">
						<button class="btn btn-danger" type="button"
								ng-disabled="questionnaire.QuestionnairePage.length < 2"
								ng-click="deletePage($index, '<?php echo __d('questionnaires', 'Do you want to delete this page?'); ?>')">
							<span class="glyphicon glyphicon-remove"></span><?php echo __d('questionnaires', 'Delete this page'); ?>
						</button>
					</div>
				</div>
			</tab>
			<tab class="questionnaire-add-page-tab" select="addPage($event)" ng-if="isPublished == 0">
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