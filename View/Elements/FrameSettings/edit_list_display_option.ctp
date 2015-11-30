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
	<?php echo $this->NetCommonsForm->input('display_num_per_page', array(
		'label' => __d('questionnaires', 'Visible questionnaire row'),
		'type' => 'select',
		'options' => QuestionnairesComponent::getDisplayNumberOptions(),
		'selected' => $questionnaireFrameSettings['display_num_per_page'],
		'autofocus' => true,
		));
	?>
</div>

<div class='form-group'>
	<?php echo $this->NetCommonsForm->input('sort_type', array(
		'label' => __d('questionnaires', 'Visible row order'),
		'type' => 'select',
		'options' => QuestionnairesComponent::getSortOrders(),
		'selected' => $questionnaireFrameSettings['sort_type'],
		));
	?>
</div>
