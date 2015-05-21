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
<label><?php echo __d('questionnaires', 'Questionnaire display setting'); ?></label>
<?php echo $this->Form->input('display_type', array(
	'type' => 'radio',
	'options' => array(
	QuestionnairesComponent::DISPLAY_TYPE_SINGLE => __d('questionnaires', 'Show only one questionnaire'),
	QuestionnairesComponent::DISPLAY_TYPE_LIST => __d('questionnaires', 'Show questionnaires list')),
	'legend' => false,
	'before' => '<div class="radio-inline">',
	'separator' => '</div><div class="radio-inline">',
	'after' => '</div>',
	'value' => $questionnaireFrameSettings['display_type'],
	'ng-model' => 'questionnaireFrameSettings.display_type'
	));
