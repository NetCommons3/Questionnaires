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
	<?php
		$initStr = "QuestionnaireDateTimeAnswer" . $question['id'] . "= Date('" . $answer[0]['answer_value'] . "')";
		?>

		<div class="row" ng-init="<?php echo $initStr; ?>">

		<?php if ($question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_DATE): ?>
		<div class="col-sm-3 form-group">
			 <div class="input-group">
				<?php echo
				$this->Form->input('QuestionnaireAnswer.' . $index . '.answer_value',
				array('type' => 'text',
				'class' => 'form-control',
				'datepicker-popup' => 'yyyy-MM-dd',
				'ng-model' => 'QuestionnaireDateTimeAnswer' . $question['id'],
				'show-weeks' => 'false',
				'min' => 'Date(\'' . $question['min'] . '\')',
				'max' => 'Date(\'' . $question['max'] . '\')',
				'label' => false));
				?>
				<div class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></div>
			 </div>
			<?php echo $this->element('Questionnaires.Answers/question_range_description', array('question' => $question)); ?>
		</div>
		<?php endif ?>

		<?php if ($question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_TIME): ?>
		<div class="col-sm-3">
			<timepicker
					ng-model="QuestionnaireDateTimeAnswer<?php echo $question['id']; ?>"
					hour-step="1"
					minute-step="15" >

			</timepicker>
			<?php echo
				$this->Form->input('QuestionnaireAnswer.' . $index . '.answer_value', array(
				'type' => 'hidden',
				'ng-value' => "QuestionnaireDateTimeAnswer" . $question['id'] . " | date : 'yyyy-MM-dd HH:mm:ss'"
				));
				?>
			<?php echo $this->element('Questionnaires.Answers/question_range_description', array('question' => $question)); ?>
		</div>
		<?php endif ?>

		<?php if ($question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_DATE_TIME): ?>
			<div class="col-sm-4">
				<div class="input-group">
					<?php echo
					$this->Form->input('QuestionnaireAnswer.' . $index . '.answer_value',
					array('type' => 'text',
					'class' => 'form-control',
					'ng-model' => 'QuestionnaireDateTimeAnswer' . $question['id'],
					'datetimepicker',
					'min' => 'Date(\'' . $question['min'] . '\')',
					'max' => 'Date(\'' . $question['max'] . '\')',
					'label' => false));
					?>
					<div class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></div>
				</div>
				<?php echo $this->element('Questionnaires.Answers/question_range_description', array('question' => $question)); ?>
			</div>
		<?php endif ?>

		<?php echo $this->Form->hidden('QuestionnaireAnswer.' . $index . '.matrix_choice_id', array(
		'value' => null
		));?>

		</div>
<?php endif;