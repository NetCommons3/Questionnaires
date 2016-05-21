<?php
/**
 * questionnaire add create template element
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php echo $this->NetCommonsForm->radio('create_option',
	array(QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_TEMPLATE => __d('questionnaires', 'Create from Template')),
	array('ng-model' => 'createOption',
	'hiddenField' => false,
	));
?>
<div  uib-collapse="createOption != '<?php echo QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_TEMPLATE; ?>'">
	<div class="col-xs-11 col-xs-offset-1">
		<?php echo $this->NetCommonsForm->input('template_file', array(
			'type' => 'file',
			'accept' => "text/comma-separated-values",
			'div' => false,
			'label' => __d('questionnaires', 'Questionnaire template file'),
			'required' => true,
			'class' => '',
		)); ?>
	</div>
</div>
