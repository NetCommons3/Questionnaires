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
<?php echo $this->NetCommonsForm->label(__d('questionnaires', 'select display questionnaires.')); ?>

<div class="questionnaire-list-wrapper">
	<table class="table table-hover questionnaire-table-vcenter">
		<tr>
			<th>
				<?php echo __d('questionnaires', 'Display'); ?>
				<div class="text-center" ng-if="questionnaireFrameSettings.displayType == <?php echo QuestionnairesComponent::DISPLAY_TYPE_LIST; ?>">
					<?php $this->NetCommonsForm->unlockField('all_check'); ?>
					<?php echo $this->NetCommonsForm->checkbox('all_check', array(
					'ng-model' => 'WinBuf.allCheck',
					'ng-change' => 'allCheckClicked()',
					'label' => false,
					'div' => false,
					'class' => '',
					)); ?>
				</div>
			</th>
			<th>
				<?php echo $this->Paginator->sort('Questionnaire.status', __d('questionnaires', 'Status')); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('Questionnaire.title', __d('questionnaires', 'Title')); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('Questionnaire.answer_start_period', __d('questionnaires', 'Implementation date')); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('Questionnaire.is_total_show', __d('questionnaires', 'Aggregates')); ?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('Questionnaire.modified', __d('net_commons', 'Updated date')); ?>
			</th>
		</tr>
		<?php foreach ((array)$questionnaires as $index => $quest): ?>
		<tr class="animate-repeat btn-default">
			<td>
				<div class="text-center" ng-show="questionnaireFrameSettings.displayType == <?php echo QuestionnairesComponent::DISPLAY_TYPE_LIST; ?>">
					<?php echo $this->NetCommonsForm->checkbox('List.QuestionnaireFrameDisplayQuestionnaire.' . $index . '.is_display', array(
					'options' => array(true => ''),
					'label' => false,
					'div' => false,
					'class' => '',
					//'value' => $quest['Questionnaire']['key'],
					//'checked' => (isset($quest['QuestionnaireFrameDisplayQuestionnaire']['questionnaire_key'])) ? true : false,
					'ng-model' => 'isDisplay[' . $index . ']'
					));
					?>
					<?php echo $this->NetCommonsForm->hidden('List.QuestionnaireFrameDisplayQuestionnaire.' . $index . '.questionnaire_key', array('value' => $quest['Questionnaire']['key'])); ?>
				</div>
				<div class="text-center"  ng-show="questionnaireFrameSettings.displayType == <?php echo QuestionnairesComponent::DISPLAY_TYPE_SINGLE; ?>">
					<?php echo $this->NetCommonsForm->radio('Single.QuestionnaireFrameDisplayQuestionnaire.questionnaire_key',
					array($quest['Questionnaire']['key'] => ''), array(
					'legend' => false,
					'label' => false,
					'div' => false,
					'class' => false,
					'hiddenField' => false,
					'checked' => (isset($quest['QuestionnaireFrameDisplayQuestionnaire']['questionnaire_key'])) ? true : false,
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
				<?php if ($quest['Questionnaire']['answer_timing'] == QuestionnairesComponent::USES_USE): ?>
					<?php echo $this->Date->dateFormat($quest['Questionnaire']['answer_start_period']); ?>
					<?php echo __d('questionnaires', ' - '); ?>
					<?php echo $this->Date->dateFormat($quest['Questionnaire']['answer_end_period']); ?>
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

<?php echo $this->element('NetCommons.paginator');

