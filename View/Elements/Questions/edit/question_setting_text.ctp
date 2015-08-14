<?php
/**
 * questionnaire comment template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="row">
	<div class="col-sm-12">
		<?php
		echo $this->Form->hidden('QuestionnairePage.' . $pageIndex . '.QuestionnaireQuestion.' . $qIndex . '.is_choice_random',
		array(
		'value' => QuestionnairesComponent::USES_NOT_USE,
		));
		echo $this->Form->hidden('QuestionnairePage.' . $pageIndex . '.QuestionnaireQuestion.' . $qIndex . '.is_skip',
		array(
		'value' => QuestionnairesComponent::SKIP_FLAGS_NO_SKIP,
		));
		?>

		<div class="form-group">
			<label class="checkbox-inline">
				<?php echo $this->Form->checkbox('QuestionnairePage.' . $pageIndex . '.QuestionnaireQuestion.' . $qIndex . '.question_type_option',
				array(
				'value' => QuestionnairesComponent::TYPE_OPTION_NUMERIC,
				'ng-model' => 'question.questionTypeOption',
				'ng-checked' => 'question.questionTypeOption == ' . QuestionnairesComponent::TYPE_OPTION_NUMERIC
				));
				?>
				<?php echo __d('questionnaires', 'Numeric'); ?>
			</label>
		</div>
		<div class="form-group">
			<label class="checkbox-inline">
				<?php echo $this->Form->checkbox('QuestionnairePage.' . $pageIndex . '.QuestionnaireQuestion.' . $qIndex . '.is_range',
				array(
				'value' => QuestionnairesComponent::USES_USE,
				'ng-model' => 'question.isRange',
				'ng-checked' => 'question.isRange == ' . QuestionnairesComponent::USES_USE
				));
				?>
				<?php echo __d('questionnaires', 'Please check if you want to set limit(or length) value.'); ?>
			</label>
			<?php echo $this->element(
			'Questionnaires.errors', array(
			'errorArrayName' => 'question.errorMessages.isRange',
			)); ?>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="form-inline" ng-if="question.isRange == <?php echo QuestionnairesComponent::USES_USE; ?>">
			<?php echo $this->Form->input('QuestionnairePage.' . $pageIndex . '.QuestionnaireQuestion.' . $qIndex . '.min',
			array(
			'div' => array('class' => 'form-group'),
			'label' => __d('questionnaires', 'Minimum'),
			'class' => 'form-control',
			'ng-model' => 'question.min'
			));
			?>
		</div>
		<?php echo $this->element(
		'Questionnaires.errors', array(
		'errorArrayName' => 'question.errorMessages.min',
		)); ?>
	</div>

	<div class="col-sm-6">
		<div class="form-inline" ng-if="question.isRange == <?php echo QuestionnairesComponent::USES_USE; ?>">
			<?php echo $this->Form->input('QuestionnairePage.' . $pageIndex . '.QuestionnaireQuestion.' . $qIndex . '.max',
			array(
			'div' => array('class' => 'form-group'),
			'label' => __d('questionnaires', 'Maximum'),
			'class' => 'form-control',
			'ng-model' => 'question.max'
			));
			?>
		</div>
		<?php echo $this->element(
		'Questionnaires.errors', array(
		'errorArrayName' => 'question.errorMessages.max',
		)); ?>
	</div>
</div>

