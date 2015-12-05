<?php
/**
 * questionnaire edit result "is_display" option set template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<div class="form-group">
	<label><?php echo __d('questionnaires', 'aggregate display');?></label>
	<div class="radio"><label>

		<?php
			/*
			 * Formヘルパー使うとAngularのrepeatとバッティングしてradioのcheckedがうまく動作しなくなる
			 * 仕方ないのでべタにHTMLタグを書くことにする
			 */
		?>

		<input type="radio"
			ng-attr-name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][is_result_display]"
			value="<?php echo QuestionnairesComponent::EXPRESSION_NOT_SHOW; ?>"
			ng-model="question.isResultDisplay"
			ng-disabled="isDisabledDisplayResult(question.questionType)" />
		<?php echo __d('questionnaires', 'The results of this question will not be displayed'); ?>
	</label></div>

	<div class="radio"><label>
		<input type="radio"
		   ng-attr-name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][is_result_display]"
		   value="<?php echo QuestionnairesComponent::EXPRESSION_SHOW; ?>"
		   ng-model="question.isResultDisplay"
		   ng-disabled="isDisabledDisplayResult(question.questionType)"	/>
		<?php echo __d('questionnaires', 'The results of this question will be displayed'); ?>
	</label></div>

	<?php echo $this->NetCommonsForm->hidden(
		'QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.is_result_display',
		array('ng-value' => 'question.isResultDisplay'));
	?>
	<?php echo $this->element(
		'Questionnaires.QuestionnaireEdit/ng_errors', array(
		'errorArrayName' => 'question.errorMessages.isResultDisplay',
	)); ?>
</div>
