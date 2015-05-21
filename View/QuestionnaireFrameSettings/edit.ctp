<?php
/**
 * questionnaire setting list view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php echo $this->element('Questionnaires.scripts'); ?>

<div id="nc-questionnaire-frame-settings-content-list-<?php echo (int)$frameId; ?>"
	 ng-controller="QuestionnairesFrame"
	 ng-init="initialize(<?php echo (int)$frameId; ?>,
	 	<?php echo h(json_encode($questionnaires)); ?>,
	 	<?php echo h(json_encode($questionnaireFrameSettings)); ?>,
	 	<?php echo h(json_encode($displayQuestionnaire)); ?>)">

	<div class="modal-body">
		<?php echo $this->element('NetCommons.setting_tabs', $settingTabs); ?>
		<div class="tab-content">
			<?php echo $this->element('Questionnaires.FrameSettings/edit_form', array(
				'controller' => 'QuestionnaireFrameSettings',
				'action' => h($this->request->params['action']) . '/' . $frameId . '/' . $blockId,
				'callback' => 'Questionnaires.FrameSettings/edit_form',
				'cancelUrl' => '/questionnaires/blocks/index/' . $frameId
				)); ?>
		</div>
	</div>
</div>