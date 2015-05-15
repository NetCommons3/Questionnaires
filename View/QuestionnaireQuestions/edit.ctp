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


<div id="nc-questionnaires-setting-list-<?php echo (int)$frameId; ?>"
	 ng-controller="Questionnaires.edit.question"
	 ng-init="initialize(<?php echo (int)$frameId; ?>,
							<?php echo h(json_encode($questionnaire)); ?>,
							<?php echo h(json_encode($questionnaireValidationErrors)); ?>,
							'<?php echo h($newPageLabel); ?>',
							'<?php echo h($newQuestionLabel); ?>',
							'<?php echo h($newChoiceLabel); ?>',
							'<?php echo h($newChoiceColumnLabel); ?>',
							'<?php echo h($newChoiceOtherLabel); ?>')">

	<?php $this->start('title'); ?>
	<?php echo __d('questionnaires', 'plugin_name'); ?>
	<?php $this->end(); ?>

	<div class="modal-header">
		<?php $title = $this->fetch('title'); ?>
		<?php if ($title) : ?>
		<?php echo $title; ?>
		<?php else : ?>
		<br />
		<?php endif; ?>
	</div>

	<?php echo $this->Form->create('QuestionnaireQuestion', array(
	'name' => 'questionnaire_form_question',
	'type' => 'post',
	'novalidate' => true,
	'ng-keydown' => 'handleKeydown($event)'
	)); ?>

	<?php echo $this->Form->hidden('id'); ?>
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
					'label' => __d('questionnaires', 'Questionnaire title'),
					'class' => 'form-control',
					'ng-model' => 'questionnaire.Questionnaire.title',
					'placeholder' => __d('questionnaires', 'Please input questionnaire title')
				));
			?>
			<?php echo $this->Form->hidden(
			'Questionnaire.origin_id',
			array(
			'ng-value' => 'questionnaire.Questionnaire.origin_id',
			));
			?>
			<?php echo $this->Form->hidden(
			'Questionnaire.key',
			array(
			'ng-value' => 'questionnaire.Questionnaire.key',
			));
			?>
		</div>


		<tabset>
			<tab ng-repeat="(pageIndex, page) in questionnaire.QuestionnairePage" active="page.tab_active">
				<tab-heading>
					{{pageIndex+1}}
				</tab-heading>
				<div class="tab-body">
					<div class="form-group text-right">
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
					?>
					<div class="form-inline">
						<?php echo $this->Form->hidden('QuestionnairePage.{{pageIndex}}.page_title',
						array(
						'ng-value' => 'page.page_title'
						));
						?>
					</div>
					<accordion close-others="true">
						<accordion-group
								heading="{{question.question_value}}"
								ng-repeat="(qIndex, question) in page.QuestionnaireQuestion"
								is-open="question.isOpen"
								ng-class="{'panel-danger':(errors.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex])}">
							<accordion-heading>

								<div class="pull-right">
									<div class="btn-group">
										<button type="button" class="btn btn-default dropdown-toggle"><?php echo __d('questionnaires', 'move to another page'); ?><span class="caret"></span></button>
										<ul class="dropdown-menu" role="menu">
											<li ng-repeat="(movePageIndex, move_page) in questionnaire.QuestionnairePage" ng-class="{disabled:(page.page_sequence==move_page.page_sequence)}">
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

								<button class="btn btn-default pull-left" type="button" ng-disabled="$first" ng-click="moveQuestion($event, pageIndex, qIndex, qIndex-1)">
									<span class="glyphicon glyphicon-arrow-up"></span>
								</button>

								<button class="btn btn-default pull-left" type="button" ng-disabled="$last" ng-click="moveQuestion($event, pageIndex, qIndex, qIndex+1)">
									<span class="glyphicon glyphicon-arrow-down"></span>
								</button>

								{{question.question_value|htmlToPlaintext}}
								<span ng-if="errors.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex]">
									<?php echo __d('questionnaires', 'There is an error'); ?>
								</span>

								<span class="clearfix"></span>

							</accordion-heading>

							<div class="row form-group">
								<?php echo $this->Form->label('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.question_value',
									__d('questionnaires', 'question sentence'),
									array('class' => 'col-sm-2'));
								?>
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
								<div class="col-sm-10">
									<div class="checkbox">
										<label>
											<?php echo $this->Form->checkbox('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.is_require',
											array(
											'value' => true,
											'ng-model' => 'question.is_require'
											));
											?>
											<?php echo __d('questionnaires', 'Required'); ?>
										</label>
										<?php echo $this->element(
										'Questionnaires.errors', array(
										'errorArrayName' => 'errors.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex].is_require',
										)); ?>
									</div>
									<?php echo $this->Form->input('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.question_value',
									array(
									'type' => 'text',
									'label' => false,
									'class' => 'form-control',
									'ng-model' => 'question.question_value',
									'required' => 'required',
									)) ?>
									<?php echo $this->element(
									'Questionnaires.errors', array(
									'errorArrayName' => 'errors.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex].question_value',
									)); ?>
									<div class="nc-wysiwyg-alert">
										<?php echo $this->Form->textarea('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.description',
										array(
										'class' => 'form-control',
										'ng-model' => 'question.description',
										'ui-tinymce' => 'tinymce.options',
										'rows' => 5,
										'required' => 'required',
										)) ?>
									</div>
									<?php echo $this->element(
									'Questionnaires.errors', array(
									'errorArrayName' => 'errors.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex].description',
									)); ?>
								</div>
							</div>

							<div class="row form-group">
								<?php echo $this->Form->label('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.question_type',
								__d('questionnaires', 'Question type'),
								array('class' => 'col-sm-2'));
								?>
								<div class="col-sm-10">
									<?php
									echo $this->Form->select('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.question_type',
										$questionTypeOptions,
										array(
											'class' => 'form-control',
											'ng-model' => 'question.question_type',
											'empty' => null
										));
									?>
									<?php echo $this->element(
									'Questionnaires.errors', array(
									'errorArrayName' => 'errors.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex].question_type',
									)); ?>
								</div>
							</div>
							<div class="form-group well">
								<div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_SELECTION; ?>">
									<?php echo $this->element('Questionnaires.Questions/edit/question_setting_alternative', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}')); ?>
								</div>
								<div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_MULTIPLE_SELECTION; ?>">
									<?php echo $this->element('Questionnaires.Questions/edit/question_setting_alternative', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}')); ?>
								</div>
								<div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_TEXT; ?>">
									<?php echo $this->element('Questionnaires.Questions/edit/question_setting_text', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}')); ?>
								</div>
								<div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_TEXT_AREA; ?>">
									<?php /* 複数行テキストの場合は詳細設定がないです */ ?>
								</div>
								<div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST; ?>">
									<?php echo $this->element('Questionnaires.Questions/edit/question_setting_matrix_alternative', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}')); ?>
								</div>
								<div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_MATRIX_MULTIPLE; ?>">
									<?php echo $this->element('Questionnaires.Questions/edit/question_setting_matrix_alternative', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}')); ?>
								</div>
								<div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_DATE_AND_TIME; ?>">
									<?php echo $this->element('Questionnaires.Questions/edit/question_setting_date', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}')); ?>
								</div>
								<div ng-if="question.question_type == <?php echo QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX; ?>">
									<?php echo $this->element('Questionnaires.Questions/edit/question_setting_alternative', array('pageIndex' => '{{pageIndex}}', 'qIndex' => '{{qIndex}}')); ?>
								</div>
							</div>

						</accordion-group>
					</accordion>

					<div class="form-group text-right" ng-show="page.QuestionnaireQuestion.length > 0">
						<button class="btn btn-success" type="button" ng-click="addQuestion($event, pageIndex)">
							<span class="glyphicon glyphicon-plus"></span>
							<?php echo __d('questionnaires', 'Add Question'); ?>
						</button>
					</div>

					<div class="text-center">
						<button class="btn btn-danger" type="button"
								ng-disabled="questionnaire.QuestionnairePage.length < 2"
								ng-click="deletePage($index, '<?php echo __d('questionnaires', 'Do you want to delete this page?'); ?>')">
							<span class="glyphicon glyphicon-remove"></span><?php echo __d('questionnaires', 'Delete this page'); ?>
						</button>
					</div>
				</div>
			</tab>
			<tab class="questionnaire-add-page-tab" select="addPage($event)" >
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