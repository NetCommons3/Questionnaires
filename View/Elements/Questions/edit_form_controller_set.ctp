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
		/*
				$name
				$label
				$diabled
				$input
				$isPublished
		*/
?>
<div class="row form-group">
	<?php echo $this->Form->label('QuestionnairePage.{{pageIndex}}.QuestionnaireQuestion.{{qIndex}}.' . $name,
		$label,
		array('class' => 'col-sm-2 control-label'));
	?>
	<div class="col-sm-10">
		<?php echo $input; ?>
		<?php echo $this->element(
		'Questionnaires.errors', array(
		'errorArrayName' => 'errors.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex].' . $name,
		)); ?>
	</div>
</div>