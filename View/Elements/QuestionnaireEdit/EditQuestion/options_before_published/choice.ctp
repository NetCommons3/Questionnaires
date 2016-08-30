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
<input type="text"
	   name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choiceSequence}}][choice_label]"
	   class="form-control input-sm"
	   ng-model="choice.choiceLabel"
	   nc-focus = "true"
		/>
<span ng-if="choice.otherChoiceType != <?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED; ?>">
	<?php echo __d('questionnaires', '(This is [other] choice. Area to enter the text is automatically granted at the time of implementation.)'); ?>
</span>

<?php echo $this->element(
	'Questionnaires.QuestionnaireEdit/ng_errors', array(
	'errorArrayName' => 'choice.errorMessages.choiceLabel',
)); ?>

<?php // Version1ではchoice_valueの値はchoice_labelと同じにしておく ?>
<input type="hidden"
	   name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choiceSequence}}][choice_value]"
	   ng-value="choice.choiceLabel"
		/>
<input type="hidden"
	   name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choiceSequence}}][choice_sequence]"
	   ng-value="choice.choiceSequence"
		/>
<input type="hidden"
	   name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choiceSequence}}][matrix_type]"
	   ng-value="choice.matrixType"
		/>
<input type="hidden"
	   name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choiceSequence}}][other_choice_type]"
	   ng-value="choice.otherChoiceType"
		/>
<input type="hidden"
	   name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choiceSequence}}][key]"
	   ng-value="choice.key"
		/>
<input type="hidden"
	   name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choiceSequence}}][graph_color]"
	   ng-value="choice.graphColor"
		/>
