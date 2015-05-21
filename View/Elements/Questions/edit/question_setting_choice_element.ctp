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
       name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choice_sequence}}][choice_label]"
       class="form-control input-sm"
       ng-model="choice.choice_label"
        />
<span ng-if="choice.other_choice_type != <?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED; ?>">
    <?php echo __d('questionnaires', '(This is [other] choice. Area to enter the text is automatically granted at the time of implementation.)'); ?>
</span>
<?php echo $this->element(
'Questionnaires.errors', array(
'errorArrayName' => 'errors.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex].QuestionnaireChoice[choice.choice_sequence]',
)); ?>

<?php // Version1ではchoice_valueの値はchoice_labelと同じにしておく ?>
<input type="hidden"
       name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choice_sequence}}][choice_value]"
       ng-value="choice.choice_label"
        />
<input type="hidden"
       name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choice_sequence}}][choice_sequence]"
       ng-value="choice.choice_sequence"
        />
<input type="hidden"
       name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choice_sequence}}][matrix_type]"
       ng-value="choice.matrix_type"
        />
<input type="hidden"
       name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choice_sequence}}][other_choice_type]"
       ng-value="choice.other_choice_type"
        />
<input type="hidden"
       name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choice_sequence}}][id]"
       ng-value="choice.id"
        />
<input type="hidden"
       name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choice_sequence}}][origin_id]"
       ng-value="choice.origin_id"
        />
<input type="hidden"
       name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choice_sequence}}][key]"
       ng-value="choice.key"
        />
<input type="hidden"
       name="data[QuestionnairePage][{{pageIndex}}][QuestionnaireQuestion][{{qIndex}}][QuestionnaireChoice][{{choice.choice_sequence}}][graph_color]"
       ng-value="choice.graph_color"
        />
