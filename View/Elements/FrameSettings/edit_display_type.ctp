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
<?php echo $this->NetCommonsForm->input('display_type', array(
	'type' => 'radio',
	'class' => '',
	'options' => array(
		QuestionnairesComponent::DISPLAY_TYPE_SINGLE => __d('questionnaires', 'Show only one questionnaire'),
		QuestionnairesComponent::DISPLAY_TYPE_LIST => __d('questionnaires', 'Show questionnaires list')),
	'legend' => false,
	'label' => false,
	'before' => '<div class="radio-inline"><label>',
	'separator' => '</label></div><div class="radio-inline"><label>',
	'after' => '</label></div>',
	'hiddenField' => false,
	'ng-model' => 'questionnaireFrameSettings.displayType',
	));
