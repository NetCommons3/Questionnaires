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
echo $this->element('Questionnaires.scripts');
echo $this->NetCommonsHtml->script(array(
	'/components/moment/min/moment.min.js',
	'/components/moment/min/moment-with-locales.min.js',
	'/questionnaires/js/questionnaires_edit.js',
));
$jsQuestionnaire = NetCommonsAppController::camelizeKeyRecursive(QuestionnairesAppController::changeBooleansToNumbers($this->data));
?>

<?php echo $this->QuestionEdit->getEditFlowChart(3); ?>

<div
	id="nc-questionnaires-setting-edit"
	 ng-controller="Questionnaires.setting"
	 ng-init="initialize(<?php echo Current::read('Frame.id'); ?>,
									<?php echo h(json_encode($jsQuestionnaire)); ?>)">

	<?php echo $this->NetCommonsForm->create('Questionnaire', $postUrl);

		/* NetCommonsお約束:プラグインがデータを登録するところではFrame.id,Block.id,Block.keyの３要素が必ず必要 */
		echo $this->NetCommonsForm->hidden('Frame.id');
		echo $this->NetCommonsForm->hidden('Block.id');
		echo $this->NetCommonsForm->hidden('Block.key');

		echo $this->NetCommonsForm->hidden('Questionnaire.key');
		echo $this->NetCommonsForm->hidden('Questionnaire.import_key');
		echo $this->NetCommonsForm->hidden('Questionnaire.export_key');
	?>
		<div class="modal-body">
			<div class="form-group questionnaire-group">
				<?php /* アンケートタイトル設定 */
					echo $this->TitleIcon->inputWithTitleIcon('title', 'Questionnaire.title_icon',
					array('label' => __d('questionnaires', 'Title'),
						'ng-model' => 'questionnaires.questionnaire.title'
					));
				?>
				<?php echo $this->NetCommonsForm->input('sub_title',
					array('label' => __d('questionnaires', 'Sub Title'),
						'ng-model' => 'questionnaires.questionnaire.subTitle',
						'placeholder' => __d('questionnaires', 'Please enter if there is a sub title')
					));
				?>
			</div>

			<label class="h3"><?php echo __d('questionnaires', 'Questionnaire answer period'); ?></label>
			<div class="form-group questionnaire-group">

				<?php /* アンケート期間設定 */
					echo $this->QuestionEdit->questionnaireAttributeCheckbox('answer_timing',
						__d('questionnaires', 'set the answer period'),
						array(),
						__d('questionnaires', 'After approval will be immediately published . Stop of the questionnaire to select the stop from the questionnaire data list .'));
				?>
				<div class="row" ng-show="questionnaires.questionnaire.answerTiming == '<?php echo QuestionnairesComponent::USES_USE; ?>'">
					<div class="col-sm-5">
						<?php
							echo $this->QuestionEdit->questionnaireAttributeDatetime('answer_start_period', false,
								array('min' => '', 'max' => 'answer_end_period'));
						?>
					</div>
					<div class="col-sm-1">
						<?php echo __d('questionnaires', ' - '); ?>
					</div>
					<div class="col-sm-5">
						<?php
							echo $this->QuestionEdit->questionnaireAttributeDatetime('answer_end_period', false,
								array('min' => 'answer_start_period', 'max' => ''));
						?>
					</div>
				</div>
			</div>

			<div ng-show="questionnaires.questionnaire.isTotalShow == 1">
			<label class="h3"><?php echo __d('questionnaires', 'Counting result display start date'); ?></label>
			<div class="row form-group questionnaire-group">

				<?php /* 集計結果表示期間設定 */
					echo $this->QuestionEdit->questionnaireAttributeCheckbox('total_show_timing',
						__d('questionnaires', 'set the aggregate display period'),
						array(),
						__d('questionnaires', 'If not set , it will be displayed after the respondent answers.'));
				?>
				<div class="row" ng-show="questionnaires.questionnaire.totalShowTiming != 0">
					<div class="col-sm-5">
						<?php
							echo $this->QuestionEdit->questionnaireAttributeDatetime('total_show_start_period', false);
						?>
					</div>
					<div class="col-sm-6">
						<?php echo __d('questionnaires', 'Result will display at this time.'); ?>
					</div>
				</div>
			</div>
			</div>

			<label class="h3"><?php echo __d('questionnaires', 'Questionnaire method'); ?></label>
			<?php if (Current::read('Room.space_id') == Space::PUBLIC_SPACE_ID): ?>
				<?php echo $this->element('Questionnaires.QuestionnaireEdit/Edit/method_in_public'); ?>
			<?php else: ?>
				<?php echo $this->element('Questionnaires.QuestionnaireEdit/Edit/method_in_group'); ?>
			<?php endif; ?>

			<?php echo $this->element('Questionnaires.QuestionnaireEdit/Edit/open_mail'); ?>

			<label class="h3"><?php echo __d('questionnaires', 'Thanks page message settings'); ?></label>
			<div class="form-group questionnaire-group">
				<?php
					echo $this->NetCommonsForm->wysiwyg('thanks_content', array(
						'label' => false,
						'ng-model' => 'questionnaires.questionnaire.thanksContent'));
				?>
			</div>
			<?php echo $this->Workflow->inputComment('Questionnaire.status'); ?>
		</div>
		<?php echo $this->Workflow->buttons('Questionnaire.status', $cancelUrl, true, $backUrl); ?>

	<?php echo $this->NetCommonsForm->end(); ?>

	<?php if ($this->request->params['action'] === 'edit' && !empty($this->data['Questionnaire']['key']) && $this->Workflow->canDelete('Questionnaire', $this->data)) : ?>
		<div class="panel-footer text-right">
			<?php echo $this->element('Questionnaires.QuestionnaireEdit/Edit/delete_form'); ?>
		</div>
	<?php endif; ?>

	<?php echo $this->Workflow->comments(); ?>

</div>
