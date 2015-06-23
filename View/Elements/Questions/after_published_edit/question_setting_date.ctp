<?php
/**
 * 実施後のアンケート
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
	'ng-model' => 'question.questionTypeOption',
	'disabled' => 'disabled',
	));
	?>
	<div class="col-sm-3">
	</div>
</div>


<div class="row">
	<div class="col-sm-12">
		<div class="checkbox disabled">
			<label>
				<input type="checkbox"
					   disabled
					   ng-model="question.setDateTimeRange">
				<?php echo __d('questionnaires', 'set range to answer date and time'); ?>
			</label>
		</div>
	</div>
</div>

<div class="row">
	<div ng-show="question.setDateTimeRange">
		<div class="col-sm-5">
			<?php echo $this->Form->input('QuestionnairePage.' . $pageIndex . '.QuestionnaireQuestion.' . $qIndex . '.min',
			array(
			'type' => 'text',
			'label' => false,
			'disabled' => 'disabled',
			'ng-value' => "question.min | date : 'yyyy-MM-dd HH:mm:ss'"
			));
			?>
		</div>

		<div class="col-sm-2"><p class="form-control-static text-center"><?php echo __d('questionnaires', ' - '); ?></p></div>

		<div class="col-sm-5">
			<?php echo $this->Form->input('QuestionnairePage.' . $pageIndex . '.QuestionnaireQuestion.' . $qIndex . '.max',
			array(
			'type' => 'text',
			'label' => false,
			'disabled' => 'disabled',
			'ng-value' => "question.max | date : 'yyyy-MM-dd HH:mm:ss'"
			)
			);
			?>
		</div>
	</div>
</div>

