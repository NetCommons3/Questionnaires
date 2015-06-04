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
			$otherOpt = array();
			$afterLabel = '</label></div>';
			foreach ($question['QuestionnaireChoice'] as $choice) {
				if ($choice['other_choice_type'] == QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
					$options[QuestionnairesComponent::ANSWER_DELIMITER . $choice['origin_id'] . QuestionnairesComponent::ANSWER_VALUE_DELIMITER . $choice['choice_label']] = $choice['choice_label'];
				} else {
					$otherInput = $this->Form->input('QuestionnaireAnswer.' . $index . '.other_answer_value', array(
						'type' => 'text',
						'label' => false,
						'div' => false,
						'value' => $answer[0]['other_answer_value'],
						'disabled' => $readonly,
					));
					$otherOpt[QuestionnairesComponent::ANSWER_DELIMITER . $choice['origin_id'] . QuestionnairesComponent::ANSWER_VALUE_DELIMITER . $choice['choice_label']] = $choice['choice_label'];
					$afterLabel = '&nbsp;&nbsp;&nbsp;&nbsp;' . $otherInput . '</label></div>';
				}
			}

			$options += $otherOpt;
			echo $this->Form->input('QuestionnaireAnswer.' . $index . '.answer_value', array(
					'type' => 'radio',
					'options' => $options,
					'legend' => false,
					'label' => false,
					'before' => '<div class="radio"><label>',
					'separator' => '</label></div><div class="radio"><label>',
					'after' => $afterLabel,
					'value' => $answer[0]['answer_value'],
					'disabled' => $readonly,
				));
		}
	?>
	<?php echo $this->Form->hidden('QuestionnaireAnswer.' . $index . '.matrix_choice_id', array(
		'value' => null
	));