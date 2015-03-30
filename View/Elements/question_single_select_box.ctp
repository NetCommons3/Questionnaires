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
        foreach ($question['QuestionnaireChoice'] as $choice) {
            $options[QuestionnairesComponent::ANSWER_DELIMITER . $choice['id']. QuestionnairesComponent::ANSWER_VALUE_DELIMITER .$choice['choice_label']] = $choice['choice_label'];
        }


        echo   $this->Form->input('QuestionnaireAnswer.'.$index.'.answer_value', array(
            'type' => 'select',
            'options' => $options,
            'label' => false,
            'div' => 'form-inline',
            'class' => 'form-control',
            'value' => $answer[0]['answer_value'],
            'disabled' => $readonly,
            'empty' => __d('questionnaires', 'Please choose one')
        ));
    }
?>
<?php echo $this->Form->hidden('QuestionnaireAnswer.'.$index.'.matrix_choice_id', array(
'value' => null
));?>