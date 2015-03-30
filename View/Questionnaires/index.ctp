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

<?php echo $this->element('Questionnaires.scripts'); ?>

<div id="nc-questionnaires-<?php echo (int)$frameId; ?>"
ng-controller="Questionnaires"
ng-init="initialize(<?php echo (int)$frameId; ?>,
<?php echo h(json_encode($questionnaire)); ?>)">

	<?php if($this->viewVars['contentEditable'] == true): ?>
	<p class="text-right">
		<span class="nc-tooltip" tooltip="<?php echo __d('net_commons', 'Edit'); ?>">
			<a href="<?php echo $this->Html->url('/questionnaires/questionnaires/setting_list/' . $frameId) ?>" class="btn btn-primary">
				<span class="glyphicon glyphicon-edit"> </span>
			</a>
		</span>
	</p>
	<?php endif ?>

	<?php echo $this->element('Questionnaires.answer_status',
		array('answer_status' => $answer_status
		)
	); ?>


	<table class="table table-striped table-bordered questionnaire-table-vcenter questionnaire-borderless">
		<thead>
			<tr>
				<th><?php echo __d('questionnaires', 'Answers period'); ?></th>
				<th><?php echo __d('questionnaires', 'Questionnaire title'); ?></th>
				<th><?php echo __d('questionnaires', 'Answers and results'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($answer_questionnaires as $answer_questionnaire): ?>
			<tr>
				<td>
					<?php if ($answer_questionnaire['LatestEntity']['status'] != NetCommonsBlockComponent::STATUS_PUBLISHED ||
								$answer_questionnaire['Questionnaire']['questionnaire_status'] != QuestionnairesComponent::STATUS_STARTED ||
								$answer_questionnaire['LatestEntity']['questionPeriodFlag'] == false): ?>
					<h4>
						<?php
						//初期値セット
						$lbl_color = 'danger';
						$lbl_msg = __d('questionnaires','Undefined');
						if($answer_questionnaire['LatestEntity']['status'] == NetCommonsBlockComponent::STATUS_IN_DRAFT) {
							//一時保存中
							$lbl_color = 'info';
							$lbl_msg = __d('net_commons', 'Temporary');
						}
						else if($answer_questionnaire['LatestEntity']['status'] == NetCommonsBlockComponent::STATUS_APPROVED ) {
							//承認待ち
							$lbl_color = 'warning';
							$lbl_msg = __d('net_commons', 'Approving');
						 }
						 else if($answer_questionnaire['LatestEntity']['status'] == NetCommonsBlockComponent::STATUS_DISAPPROVED ) {
							//差し戻し
							$lbl_color = 'danger';
							$lbl_msg = __d('net_commons', 'Disapproving');
						 }
						 else if($answer_questionnaire['Questionnaire']['questionnaire_status'] != QuestionnairesComponent::STATUS_STARTED) {
							//未実施
							$lbl_color = 'default';
							$lbl_msg = __d('questionnaires','Before public');
						 }
						 else if($answer_questionnaire['LatestEntity']['questionPeriodFlag'] == false) {
							//終了
							$lbl_color = 'default';
							$lbl_msg = __d('questionnaires','End');
						 }
						?>
						<span  class="label label-<?php echo $lbl_color; ?>">
							<?php echo $lbl_msg; ?>
						</span>
					</h4>
					<?php endif ?>

					<?php
						//開始・終了日付の整形
						if (isset($answer_questionnaire['LatestEntity']['start_period']) ||
					 		isset($answer_questionnaire['LatestEntity']['end_period'])): ?>
					<span class="small">
						<?php
							$start_period_ymd = $this->QuestionnaireUtil->transformPeriodYmd($answer_questionnaire['LatestEntity']['start_period']);
							$end_period_ymd = $this->QuestionnaireUtil->transformPeriodYmd($answer_questionnaire['LatestEntity']['end_period']);
						?>
						<?php echo $start_period_ymd; ?>～<?php echo $end_period_ymd; ?>
					</span>
					<?php endif ?>
				</td>
				<td>
					<?php
						// タイトル・サブタイトル
						$sub_title_msg = $this->QuestionnaireUtil->getSubTitle($answer_questionnaire['LatestEntity']['sub_title']);
					?>
					<p class="h4">
						<?php echo h($answer_questionnaire['LatestEntity']['title']).$sub_title_msg; ?>
					</p>
				</td>
				<td class="q-btn">
					<?php echo
						$this->QuestionnaireUtil->getAnswerButtons($frameId, $answer_questionnaire, $contentEditable);
					?>
					<?php echo
						$this->QuestionnaireUtil->getAggregateButtons($frameId, $answer_questionnaire, $contentEditable);
					?>
				</td>
			</tr>

		<?php endforeach; ?>
		</tbody>
	</table>

	<div class="text-center">
		<?php echo $this->element('Questionnaires.answer_list_paginate',
			array( 'params' =>
				array(
					'answer_status' => $answer_status,
					'page' => $page
				)
			));
		?>
	</div>

</div>

