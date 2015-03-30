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
<?php echo $this->Form->input('QuestionnaireAnswer.'.$index.'.answer_value', array(
    'div' => 'form-inline',
    'type' => 'text',
    'label' => false,
    'class' => 'form-control',
    'value' => $answer[0]['answer_value'],
    'disabled' => $readonly,
));?>
<?php if (!is_null($question['min']) && !is_null($question['max'])): ?>
    <span class="help-block"><?php echo sprintf(__d('questionnaires', 'Please %d <= enter <= %d'), $question['min'], $question['max']); ?></span>
<?php elseif (!is_null($question['min'])): ?>
    <span class="help-block"><?php echo sprintf(__d('questionnaires', 'Please %d <= enter'), $question['min']); ?></span>
<?php elseif (!is_null($question['max'])): ?>
    <span class="help-block"><?php echo sprintf(__d('questionnaires', 'Please enter <= %d'), $question['max']); ?></span>
<?php endif ?>
<?php echo $this->Form->hidden('QuestionnaireAnswer.'.$index.'.matrix_choice_id', array(
'value' => null
));?>

