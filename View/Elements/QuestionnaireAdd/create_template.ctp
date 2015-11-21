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
<div  collapse="createOption != '<?php echo QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_TEMPLATE; ?>'">
	<?php /* 本当はこの辺は共通部品になるはず とりあえず直接書いておく */ ?>
	<?php echo $this->NetCommonsForm->input('template_file', array(
		'type' => 'file',
		'accept' => "text/comma-separated-values",
		'div' => false,
		'label' => __d('questionnaires', 'Questionnaire template file'),
		'required' => true,
		'class' => '',
	)); ?>
	<?php echo $this->NetCommonsForm->hidden('template_file' . '.File.status', array(
		'value' => 1
	)); ?>
	<?php echo $this->NetCommonsForm->hidden('template_file' . '.File.role_type', array(
		'value' => 'room_file_role'
	)); ?>
	<?php echo $this->NetCommonsForm->hidden('template_file' . '.File.path', array(
		'value' => '{ROOT}' . 'questionnaires' . '{DS}' . Current::read('Room.id') . '{DS}'
	)); ?>
	<?php echo $this->NetCommonsForm->hidden('template_file' . '.FilesPlugin.plugin_key', array(
		'value' => 'questionnaires'
	)); ?>
	<?php echo $this->NetCommonsForm->hidden('template_file' . '.FilesRoom.room_id', array(
		'value' => Current::read('Room.id')
	)); ?>
	<?php echo $this->NetCommonsForm->hidden('template_file' . '.FilesUser.user_id', array(
		'value' => (int)AuthComponent::user('id')
	)); ?>
</div>
