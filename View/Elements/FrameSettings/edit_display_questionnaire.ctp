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
<label><?php echo __d('questionnaires', 'select display questionnaires.'); ?></label>

<div class="questionnaire-list-wrapper">
	<table class="table table-hover questionnaire-table-vcenter">
		<tr>
			<th>
				<?php echo __d('questionnaires', 'Display'); ?>
				<div class="text-center" ng-if="questionnaireFrameSettings.display_type == <?php echo QuestionnairesComponent::DISPLAY_TYPE_LIST; ?>">
					<input type="checkbox" ng-model="WinBuf.allCheck" ng-change="allCheckClicked()"  />
				</div>
			</th>
			<th>
				<?php echo $this->Paginator->sort('Questionnaire.status', __d('questionnaires', 'Status')); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('Questionnaire.title', __d('questionnaires', 'Title')); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('Questionnaire.start_period', __d('questionnaires', 'Implementation date')); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('Questionnaire.is_total_show', __d('questionnaires', 'Aggregates')); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('Questionnaire.modified', __d('net_commons', 'Updated date')); ?>
			</th>
		</tr>
		<?php foreach ((array)$questionnaires as $quest): ?>
		<tr class="animate-repeat btn-default">
			<td>
				<div class="text-center" ng-show="questionnaireFrameSettings.display_type == <?php echo QuestionnairesComponent::DISPLAY_TYPE_LIST; ?>">
					<?php echo $this->Form->checkbox('QuestionnaireFrameDisplayQuestionnaires.List.questionnaire_origin_id.' . $quest['Questionnaire']['origin_id'], array(
					'options' => array($quest['Questionnaire']['origin_id'] => ''),
					'label' => false,
					'div' => false,
					'value' => $quest['Questionnaire']['origin_id'],
					'checked' => (isset($displayQuestionnaire[$quest['Questionnaire']['origin_id']])) ? true : false,
					'ng-true-value' => $quest['Questionnaire']['origin_id'],
					'ng-model' => 'displayQuestionnaire.' . $quest['Questionnaire']['origin_id'],
					));
					?>
				</div>
				<div class="text-center"  ng-show="questionnaireFrameSettings.display_type == <?php echo QuestionnairesComponent::DISPLAY_TYPE_SINGLE; ?>">
					<?php echo $this->Form->radio('QuestionnaireFrameDisplayQuestionnaires.Single.questionnaire_origin_id',
					array($quest['Questionnaire']['origin_id'] => ''), array(
					'legend' => false,
					'label' => false,
					'div' => false,
					'hiddenField' => false,
					'checked' => (isset($displayQuestionnaire[$quest['Questionnaire']['origin_id']])) ? true : false,
					));
					?>
				</div>
			</td>
			<td>
				<?php echo $this->QuestionnaireStatusLabel->statusLabelManagementWidget($quest);?>
			</td>
			<td>
				<?php echo $quest['Questionnaire']['title']; ?>
			</td>
			<td>
				<?php if ($quest['Questionnaire']['is_period'] == QuestionnairesComponent::USES_USE): ?>
					<?php echo $this->Date->dateFormat($quest['Questionnaire']['start_period']); ?>
					<?php echo __d('questionnaires', ' - '); ?>
					<?php echo $this->Date->dateFormat($quest['Questionnaire']['end_period']); ?>
				<?php endif ?>
			</td>
			<td>
				<?php if ($quest['Questionnaire']['is_total_show'] == QuestionnairesComponent::EXPRESSION_SHOW): ?>
					<?php echo __d('questionnaires', 'On'); ?>
				<?php endif ?>
			</td>
			<td>
				<?php echo $this->Date->dateFormat($quest['Questionnaire']['modified']); ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>
<div class="text-center">
	<?php echo $this->element('NetCommons.paginator', array(
	'url' => Hash::merge(
	array('controller' => 'questionnaire_frame_settings', 'action' => 'edit', $frameId, $blockId),
	$this->Paginator->params['named']
	)
	)); ?>
</div>

