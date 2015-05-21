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
<?php if ($readonly): ?>
	<?php echo $answer[0]['answer_value']; ?>
<?php else: ?>

	<?php echo $this->Form->input('QuestionnaireAnswer.' . $index . '.answer_value', array(
		'div' => 'form-inline',
		'type' => 'text',
		'label' => false,
		'class' => 'form-control',
		'value' => $answer[0]['answer_value'],
		'disabled' => $readonly,
		));?>
	<?php echo $this->element('Questionnaires.Answers/question_range_description', array('question' => $question)); ?>
	<?php echo $this->Form->hidden('QuestionnaireAnswer.' . $index . '.matrix_choice_id', array(
		'value' => null
		)); ?>
<?php endif;
