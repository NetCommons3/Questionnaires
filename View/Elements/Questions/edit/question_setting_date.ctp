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
		'ng-model' => 'question.questionTypeOption'
	));
	?>
	<div class="col-sm-3">
	</div>
</div>


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
			<?php
			echo $this->element(
			'Questionnaires.Questions/edit/question_setting_date_element', array(
			'field' => 'QuestionnairePage.' . $pageIndex . '.QuestionnaireQuestion.' . $qIndex . '.min',
			'model' => 'question.min',
			'calOpenId' => 0,
			'min' => '',
			'max' => 'question.max',
			'error' => 'question.errorMessages.min',
			));
			?>
		</div>

		<div class="col-sm-2"><p class="form-control-static text-center"><?php echo __d('questionnaires', ' - '); ?></p></div>

		<div class="col-sm-5">
			<?php
			echo $this->element(
			'Questionnaires.Questions/edit/question_setting_date_element', array(
			'field' => 'QuestionnairePage.' . $pageIndex . '.QuestionnaireQuestion.' . $qIndex . '.max',
			'model' => 'question.max',
			'calOpenId' => 1,
			'min' => 'question.min',
			'max' => '',
			'error' => 'question.errorMessages.max',
			));
			?>
		</div>
	</div>
</div>