<?php
/**
 * 日・時範囲の設定部品
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php
/*
 * filed
 * model
 * min
 * max
 * error
 */
?>
<div ng-if="question.questionTypeOption == <?php echo QuestionnairesComponent::TYPE_OPTION_DATE; ?>">
	<?php echo $this->NetCommonsForm->input($field,
	array(
		'id' => 'date_' . $field,
		'div' => false,
		'label' => false,
		'data-toggle' => 'dropdown',
		'datetimepicker' => 'datetimepicker',
		'datetimepicker-options' => "{format:'YYYY-MM-DD'}",
		'ng-model' => $model,
		'ng-focus' => 'setMinMaxDate($event, pageIndex, qIndex)',
	));
	?>
</div>
<div ng-if="question.questionTypeOption == <?php echo QuestionnairesComponent::TYPE_OPTION_TIME; ?>">
	<?php echo $this->NetCommonsForm->input($field,
	array(
		'id' => 'time_' . $field,
		'div' => false,
		'label' => false,
		'data-toggle' => 'dropdown',
		'datetimepicker' => 'datetimepicker',
		'datetimepicker-options' => "{format:'HH:mm'}",
		'ng-model' => $model,
		'ng-focus' => 'setMinMaxDate($event, pageIndex, qIndex)',
	));
	?>
</div>
<div ng-if="question.questionTypeOption == <?php echo QuestionnairesComponent::TYPE_OPTION_DATE_TIME; ?>">
	<?php echo $this->NetCommonsForm->input($field,
	array(
		'id' => 'datetime_' . $field,
		'div' => false,
		'label' => false,
		'data-toggle' => 'dropdown',
		'datetimepicker' => 'datetimepicker',
		'datetimepicker-options' => "{format:'YYYY-MM-DD HH:mm'}",
		'ng-model' => $model,
		'ng-focus' => 'setMinMaxDate($event, pageIndex, qIndex)',
	));
	?>
</div>

<?php echo $this->element(
	'Questionnaires.QuestionnaireEdit/ng_errors', array(
	'errorArrayName' => $error,
));