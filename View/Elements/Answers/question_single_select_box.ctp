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
	<?php echo substr($answer[0]['answerValue'], strrpos($answer[0]['answerValue'], QuestionnairesComponent::ANSWER_VALUE_DELIMITER) + 1); ?>
<?php else: ?>
	<?php
		if (isset($question['questionnaireChoice'])) {
			$options = array();
			foreach ($question['questionnaireChoice'] as $choice) {
				$options[QuestionnairesComponent::ANSWER_DELIMITER . $choice['originId'] . QuestionnairesComponent::ANSWER_VALUE_DELIMITER . $choice['choiceLabel']] = $choice['choiceLabel'];
			}
			echo $this->Form->input('QuestionnaireAnswer.' . $index . '.answer_value', array(
				'type' => 'select',
				'options' => $options,
				'label' => false,
				'div' => 'form-inline',
				'class' => 'form-control',
				'value' => $answer[0]['answerValue'],
				'disabled' => $readonly,
				'empty' => __d('questionnaires', 'Please choose one')
			));
		}
		?>
	<?php echo $this->Form->hidden('QuestionnaireAnswer.' . $index . '.matrix_choice_id', array(
		'value' => null
		)); ?>
<?php endif;