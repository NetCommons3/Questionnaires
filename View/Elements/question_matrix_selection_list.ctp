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
<?php
    if (isset($question['QuestionnaireChoice'])) {
        $options = array();
        $rowChoices = array();
        foreach ($question['QuestionnaireChoice'] as $choice) {
            if ($choice['matrix_type'] == QuestionnairesComponent::MATRIX_TYPE_COLUMN) {
                $options[QuestionnairesComponent::ANSWER_DELIMITER . $choice['id']. QuestionnairesComponent::ANSWER_VALUE_DELIMITER . $choice['choice_label']] = $choice['choice_label'];
            }
            else {
                $rowChoices[] = $choice;
            }
        }
    }
?>
<table class="table table-striped table-bordered table-hover text-center questionnaire-matrix-table">
    <thead>
    <tr>
        <th></th>
        <?php foreach ($options as $opt): ?>
            <th class="text-center">
                <?php echo $opt; ?>
            </th>
        <?php endforeach; ?>
    </thead>
    <tbody>
    <?php foreach ($rowChoices as $rowIndex=>$row): ?>
        <tr>
            <th>
                <?php echo $row['choice_label']; ?>
                <?php echo $this->Form->hidden('QuestionnaireAnswer.'.$index.'_'.$rowIndex.'.questionnaire_question_id', array(
                'value' => $question['id']
                ));?>
                <?php echo $this->Form->hidden('QuestionnaireAnswer.'.$index.'_'.$rowIndex.'.matrix_choice_id', array(
                'value' => $row['id']
                ));?>
                <?php echo $this->Form->hidden('QuestionnaireAnswer.'.$index.'_'.$rowIndex.'.id', array(
                'value' => isset($questionPage['QuestionnaireAnswer'][$question['id']]['id']) ? $questionPage['QuestionnaireAnswer'][$question['id']]['id'] : null,
                ));?>
                <?php if ($row['other_choice_type'] != QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED): ?>
                    <?php echo $this->Form->input('QuestionnaireAnswer.'.$index.'_'.$rowIndex.'.other_answer_value', array(
                    'type' => 'text',
                    'label' => false,
                    'div' => false,
                    'value' => $answer[$rowIndex]['other_answer_value'],
                    )); ?>
                <?php endif ?>
            </th>
            <?php foreach ($options as $key=>$opt): ?>
                <td>
                    <?php echo $this->Form->input('QuestionnaireAnswer.'.$index.'_'.$rowIndex.'.answer_value', array(
                        'type' => 'radio',
                        'options' => array($key => null),
                        'legend' => false,
                        'label' => false,
                        'div' => false,
                        'hiddenField' => false,
                        'checked' => ($key == $answer[$rowIndex]['answer_value']) ? true : false,
                        'disabled' => $readonly,
                        ));
                    ?>
                </td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
