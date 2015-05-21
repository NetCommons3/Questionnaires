<?php
/**
 * アンケート質問の種別によって異なる詳細設定のファイル
 * このファイルでは日付け・時間入力タイプをフォローしている
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="row">
	<?php echo $this->Form->input(
	'QuestionnairePage.' . $pageIndex . '.QuestionnaireQuestion.' . $qIndex . '.question_type_option',
	array(
		'type' => 'radio',
		'before' => '<div class="col-sm-3"><div class="radio"><label>',
		'separator' => '</label></div></div><div class="col-sm-3"><div class="radio"><label>',
		'after' => '</label></div></div>',
		'options' => array(QuestionnairesComponent::TYPE_OPTION_DATE => __d('questionnaires', 'Date'),
							QuestionnairesComponent::TYPE_OPTION_TIME => __d('questionnaires', 'Time'),
							QuestionnairesComponent::TYPE_OPTION_DATE_TIME => __d('questionnaires', 'Date and Time')),
		'legend' => false,
		'label' => false,
		'ng-model' => 'question.question_type_option'
	));
	?>
	<div class="col-sm-3">
	</div>
</div>

<div ng-show="question.question_type_option == <?php echo QuestionnairesComponent::TYPE_OPTION_DATE; ?>
						|| question.question_type_option == <?php echo QuestionnairesComponent::TYPE_OPTION_DATE_TIME; ?>">

	<div class="row">
		<div class="col-sm-12">
			<div class="checkbox">
				<label>
					<input type="checkbox" ng-model="question.setDateTimeRange"><?php echo __d('questionnaires', 'set range to answer date and time'); ?>
				</label>
			</div>
		</div>
	</div>

	<div class="row">
		<div ng-show="question.setDateTimeRange">
			<div class="col-sm-5">
				<p class="input-group">
					<input type="text" class="form-control" datepicker-popup ng-model="question.min" show-weeks="false" is-open="question.calendar_opened[0]"  max="question.max" />
					<span class="input-group-btn">
						<button type="button" class="btn btn-default" ng-click="openCal($event, pageIndex, qIndex, 0)"><i class="glyphicon glyphicon-calendar"></i></button>
					</span>
				</p>
				<div ng-model="question.min"
					ng-show="question.question_type_option == <?php echo QuestionnairesComponent::TYPE_OPTION_DATE_TIME; ?>">
					<timepicker hour-step="1" minute-step="15" ></timepicker>
				</div>
				<?php echo $this->Form->input('QuestionnairePage.' . $pageIndex . '.QuestionnaireQuestion.' . $qIndex . '.min',
					array(
					'type' => 'hidden',
					'label' => false,
					'ng-value' => "question.min | date : 'yyyy-MM-dd HH:mm:ss'"
					));
					?>
				<?php echo $this->element(
				'Questionnaires.errors', array(
				'errorArrayName' => 'errors.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex].min',
				)); ?>
			</div>

			<div class="col-sm-2"><p class="form-control-static text-center"><?php echo __d('questionnaires', ' - '); ?></p></div>

			<div class="col-sm-5">
				<p class="input-group">
					<input type="text" class="form-control" datepicker-popup ng-model="question.max" show-weeks="false" is-open="question.calendar_opened[1]"  min="question.min" />
					<span class="input-group-btn">
						<button type="button" class="btn btn-default" ng-click="openCal($event, pageIndex, qIndex, 1)"><i class="glyphicon glyphicon-calendar"></i></button>
					</span>
				</p>
				<div ng-model="question.max"
					 ng-show="question.question_type_option == <?php echo QuestionnairesComponent::TYPE_OPTION_DATE_TIME; ?>">
					<timepicker hour-step="1" minute-step="15" ></timepicker>
				</div>
				<?php echo $this->Form->input('QuestionnairePage.' . $pageIndex . '.QuestionnaireQuestion.' . $qIndex . '.max',
				array(
				'type' => 'hidden',
				'label' => false,
				'ng-value' => "question.max | date : 'yyyy-MM-dd HH:mm:ss'"
				)
				);
				?>
				<?php echo $this->element(
				'Questionnaires.errors', array(
				'errorArrayName' => 'errors.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex].max',
				)); ?>
			</div>
		</div>
	</div>

</div>