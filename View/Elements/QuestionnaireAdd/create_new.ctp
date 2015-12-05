<?php
/**
 * questionnaire add create new element
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->NetCommonsForm->radio('create_option',
	array(QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW => __d('questionnaires', 'Create new questionnaire')),
	array('ng-model' => 'createOption',
	'hiddenField' => false,
	));
?>
<div  collapse="createOption != '<?php echo QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW; ?>'">
	<?php echo $this->NetCommonsForm->input('title', array(
	'label' => __d('questionnaires', 'Questionnaire title'),
	'required' => true,
	'placeholder' => __d('questionnaires', 'Please input questionnaire title')
	)); ?>
</div>
