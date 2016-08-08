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

echo $this->element('Questionnaires.scripts');
$jsQuestionnaireFrameSettings = NetCommonsAppController::camelizeKeyRecursive(QuestionnairesAppController::changeBooleansToNumbers($this->request->data['QuestionnaireFrameSetting']));
$jsQuestionnaires = NetCommonsAppController::camelizeKeyRecursive(QuestionnairesAppController::changeBooleansToNumbers($questionnaires));
?>

<article class="nc-questionnaire-frame-settings-content-list-"
	 ng-controller="QuestionnairesFrame"
	 ng-init="initialize(<?php echo h(json_encode($jsQuestionnaires)); ?>,
	 	<?php echo h(json_encode($jsQuestionnaireFrameSettings)); ?>)">

	<?php echo $this->BlockTabs->main(BlockTabsHelper::MAIN_TAB_FRAME_SETTING); ?>

	<div class="tab-content">

		<?php echo $this->element('Blocks.edit_form', array(
				'model' => 'QuestionnaireFrameSetting',
				'callback' => 'Questionnaires.FrameSettings/edit_form',
				'cancelUrl' => NetCommonsUrl::backToPageUrl(true),
			)); ?>

	</div>

</article>