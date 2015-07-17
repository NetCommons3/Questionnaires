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
		<div class="row">

		<?php
		$icon = 'glyphicon-calendar';
		$options = '{';
		if ($question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_DATE) {
			$options .= "format:'YYYY-MM-DD'";
			if ($question['is_range'] == QuestionnairesComponent::USES_USE) {
				$options .= ", minDate:'" . $question['min'] . "', maxDate:'" . $question['max'] . "'";
			}
		} elseif ($question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_TIME) {
			$options .= "format:'HH:mm'";
			$icon = 'glyphicon-time';
			if ($question['is_range'] == QuestionnairesComponent::USES_USE) {
				$options .= ", minDate:'" . date('Y-m-d ', time()) . $question['min'] . "', maxDate:'" . date('Y-m-d ', time()) . $question['max'] . "'";
			}
		} elseif ($question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_DATE_TIME) {
			$options .= "format:'YYYY-MM-DD HH:mm'";
			if ($question['is_range'] == QuestionnairesComponent::USES_USE) {
				$options .= ", minDate:'" . $question['min'] . "', maxDate:'" . $question['max'] . "'";
			}
		}
		$options .= '}';
		?>


		<div class="col-sm-4">
			<?php echo $this->element('NetCommons.datetimepicker'); ?>
			<div class="input-group">
				<?php echo
				$this->Form->input('QuestionnaireAnswer.' . $index . '.answer_value',
				array('type' => 'text',
				'class' => 'form-control',
				'ng-model' => 'dateAnswer[' . $question['origin_id'] . ']',
				'datetimepicker',
				'datetimepicker-options' => $options,
				'label' => false));
				?>
				<div class="input-group-addon"><i class="glyphicon <?php echo $icon; ?>"></i></div>
			</div>
			<?php echo $this->element('Questionnaires.Answers/question_range_description', array('question' => $question)); ?>
		</div>

		<?php echo $this->Form->hidden('QuestionnaireAnswer.' . $index . '.matrix_choice_id', array(
		'value' => null
		));?>

		</div>
<?php endif;