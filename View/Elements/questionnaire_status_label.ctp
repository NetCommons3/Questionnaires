<?php
/**
 * questionnaire page setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php
	//初期値セット
	$lblColor = 'danger';
	$lblMsg = __d('questionnaires', 'Undefined');
	if ($status == NetCommonsBlockComponent::STATUS_IN_DRAFT) {
		//一時保存中
		$lblColor = 'info';
		$lblMsg = __d('net_commons', 'Temporary');
	} elseif ($status == NetCommonsBlockComponent::STATUS_APPROVED) {
		//承認待ち
		$lblColor = 'warning';
		$lblMsg = __d('net_commons', 'Approving');
	} elseif ($status == NetCommonsBlockComponent::STATUS_DISAPPROVED) {
		//差し戻し
		$lblColor = 'danger';
		$lblMsg = __d('net_commons', 'Disapproving');
	} elseif ($questionnaireStatus != QuestionnairesComponent::STATUS_STARTED) {
		//未実施
		$lblColor = 'default';
		$lblMsg = __d('questionnaires', 'Before public');
	} elseif ($questionnairePeriodFlag == false) {
		//終了
		$lblColor = 'default';
		$lblMsg = __d('questionnaires', 'End');
	} else {
		$lblMsg = '';
	}
?>
<?php if ($lblMsg != ''): ?>
		<span  class="label label-<?php echo $lblColor; ?>">
			<?php echo $lblMsg; ?>
		</span>
<?php endif;