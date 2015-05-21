<?php
/**
 * Questionnaire frame display setting
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<div class='form-group'>
	<?php echo $this->Form->label(__d('questionnaires', 'Visible questionnaire row')); ?>
	<?php echo $this->Form->input('QuestionnairesFrameSetting.display_num_per_page', array(
	'label' => false,
	'type' => 'select',
	'class' => 'form-control',
	'options' => QuestionnairesComponent::getDisplayNumberOptions(),
	'selected' => $questionnaireFrameSettings['display_num_per_page'],
	'autofocus' => true,
	)
	);
	?>
</div>

<div class='form-group'>
	<?php echo $this->Form->label(__d('questionnaires', 'Visible row order'));
	?>
	<?php echo $this->Form->input('QuestionnairesFrameSetting.sort_type', array(
	'label' => false,
	'type' => 'select',
	'class' => 'form-control',
	'options' => QuestionnairesComponent::getSortOrders(),
	'selected' => $questionnaireFrameSettings['sort_type'],
	)
	);
	?>
</div>
