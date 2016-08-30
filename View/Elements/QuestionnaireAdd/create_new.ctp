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
<div  uib-collapse="createOption != '<?php echo QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW; ?>'">
	<div class="col-xs-11 col-xs-offset-1">
		<?php echo $this->NetCommonsForm->input('title', array(
		'label' => __d('questionnaires', 'Questionnaire title'),
		'required' => true,
		'placeholder' => __d('questionnaires', 'Please input questionnaire title'),
		'nc-focus' => '{{createOption == \'' . QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW . '\'}}'
		)); ?>
	</div>
</div>
