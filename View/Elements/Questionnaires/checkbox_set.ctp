<?php
/**
 * questionnaire setting view template
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
 * filedName
 * modelName
 * label
 * help
 */
?>

<div class=" checkbox">
	<label>
		<?php echo $this->Form->input($fieldName,
		array(
		'type' => 'checkbox',
		'div' => false,
		'label' => false,
		'ng-model' => 'questionnaires.questionnaire.' . $modelName,
		'ng-checked' => 'questionnaires.questionnaire.' . $modelName . '==' . QuestionnairesComponent::USES_USE,
		'error' => false
		));
		?>
		<?php echo $label; ?>
		<?php if (!empty($help)): ?>
			<span class="help-block"><?php echo $help; ?></span>
		<?php endif; ?>
	</label>
	<?php echo $this->element(
	'NetCommons.errors', [
	'errors' => $this->validationErrors,
	'model' => 'Questionnaire',
	'field' => $fieldName,
	]) ?>
</div>
