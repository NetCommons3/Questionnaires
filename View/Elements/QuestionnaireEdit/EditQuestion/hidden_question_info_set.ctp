<?php
/**
 * questionnaire hidden question info set template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php
	echo $this->NetCommonsForm->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.question_sequence',
		array('ng-value' => 'question.questionSequence'));
	echo $this->NetCommonsForm->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.id',
		array('ng-value' => 'question.id'));
	echo $this->NetCommonsForm->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.key',
		array('ng-value' => 'question.key'));
	echo $this->NetCommonsForm->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.is_result_display',
		array('ng-value' => 'question.isResultDisplay'));
	echo $this->NetCommonsForm->hidden('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.result_display_type',
		array('ng-value' => 'question.resultDisplayType'));