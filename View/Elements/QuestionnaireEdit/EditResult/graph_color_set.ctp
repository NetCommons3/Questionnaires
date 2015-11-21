<?php
/**
 * questionnaire edit result "graph_color" option set template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<label><?php echo __d('questionnaires', 'graph color');?></label>

<?php /* 択一選択、複数選択、リスト選択用グラフ設定 */ ?>

<table class="table table-condensed"
	ng-show="question.questionType != <?php echo QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST; ?>
		&& question.questionType != <?php echo QuestionnairesComponent::TYPE_MATRIX_MULTIPLE; ?>">
	<tr ng-repeat="(cIndex, choice) in question.questionnaireChoice">
		<td>
			<div class="col-sm-8">
				{{choice.choiceLabel}}

				<?php echo $this->element(
				'Questionnaires.errors', array(
				'errorArrayName' => 'choice.errorMessages.graphColor',
				)); ?>

			</div>
			<div class="col-sm-4">
				<?php echo $this->element('NetCommons.color_palette_picker', array(
					'ngAttrName' => 'data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choiceSequence}}][graph_color]',
					'ngModel' => 'choice.graphColor',
					'colorValue' => '{{choice.graphColor}}',
				)); ?>
			</div>
		</td>
	</tr>
</table>

<?php /* マトリクス択一選択、マトリクス複数選択用グラフ設定 */ ?>

<table class="table table-condensed"
	ng-show="question.questionType == <?php echo QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST; ?>
	|| question.questionType == <?php echo QuestionnairesComponent::TYPE_MATRIX_MULTIPLE; ?>">
	<tr ng-repeat="(cIndex, choice) in question.questionnaireChoice | filter : {matrixType:<?php echo QuestionnairesComponent::MATRIX_TYPE_COLUMN; ?>}">
		<td>
			<div class="col-sm-8">
				{{choice.choiceLabel}}

				<?php echo $this->element(
				'Questionnaires.errors', array(
				'errorArrayName' => 'choice.errorMessages.graphColor',
				)); ?>

			</div>
			<div class="col-sm-4">
				<?php echo $this->element('NetCommons.color_palette_picker', array(
					'ngAttrName' => 'data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choiceSequence}}][graph_color]',
					'ngModel' => 'choice.graphColor',
					'colorValue' => '{{choice.graphColor}}',
				)); ?>
			</div>
		</td>
	</tr>
</table>
