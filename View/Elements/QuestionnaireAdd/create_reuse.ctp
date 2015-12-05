<?php
/**
 * questionnaire add create reuse element
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->NetCommonsForm->radio('create_option',
	array(QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_REUSE => __d('questionnaires', 'Re-use past questionnaire')),
	array('ng-model' => 'createOption',
	'hiddenField' => false,
	'ng-disabled' => 'questionnaires.length == 0',
	));
?>
<div class="form-horizontal" collapse="createOption != '<?php echo QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_REUSE; ?>'">

	<div class="form-group">
		<div class="col-xs-3">
			<?php echo $this->NetCommonsForm->label('past_search',
				__d('questionnaires', 'Past questionnaire') . $this->element('NetCommons.required')); ?>
		</div>
		<?php echo $this->NetCommonsForm->input('past_search', array(
			'type' => 'search',
			'label' => false,
			'div' => 'col-xs-9',
			'required' => true,
			'id' => 'questionnaires_past_search_filter',
			'ng-model' => 'q.questionnaire.title',
			'placeholder' => __d('questionnaires', 'Refine by entering the part of the questionnaire name')
		));?>

		<ul class="col-xs-12 questionnaire-select-box form-control ">
			<li class="animate-repeat btn-default"
				ng-repeat="item in questionnaires | filter:q" ng-model="$parent.pastQuestionnaireSelect"
				btn-radio="item.questionnaire.id" uncheckable>

				{{item.questionnaire.title}}

				<?php echo $this->element('Questionnaires.ng_status_label',
				array('status' => 'item.questionnaire.status', 'periodRangeStat' => 'item.questionnaire.periodRangeStat')); ?>

				<span ng-if="item.questionnaire.publicType == <?php echo WorkflowBehavior::PUBLIC_TYPE_LIMITED; ?>">
				(
					{{item.questionnaire.publishStart | ncDatetime}}
					<?php echo __d('questionnaires', ' - '); ?>
					{{item.questionnaire.publishEnd | ncDatetime}}
					<?php echo __d('questionnaires', 'Implementation'); ?>
				)
				</span>
			</li>
		</ul>
	</div>
	<?php $this->NetCommonsForm->unlockField('past_questionnaire_id'); ?>
	<?php echo $this->NetCommonsForm->hidden('past_questionnaire_id', array('ng-value' => 'pastQuestionnaireSelect')); ?>
	<div class="has-error">
		<?php echo $this->NetCommonsForm->error('past_questionnaire_id', null, array('class' => 'help-block')); ?>
	</div>
</div>
