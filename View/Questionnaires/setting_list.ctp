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
<?php echo $this->Html->script('Questionnaires.questionnaires_edit.js');?>


<div id="nc-questionnaires-setting-list-<?php echo (int)$frameId; ?>"
	 ng-controller="Questionnaires.edit"
	 ng-init="initialize(<?php echo (int)$frameId; ?>,
									<?php echo h(json_encode($questionnaires)); ?>)">

	<?php $this->start('title'); ?>
	<?php echo __d('questionnaires', 'plugin_name'); ?>
	<?php $this->end(); ?>

	<?php $this->startIfEmpty('tabs'); ?>
	<li ng-class="{active:tab.isSet(0)}">
		<a href="" role="tab" data-toggle="tab">
			<?php echo __d('questionnaires', 'Questionnaire edit'); ?>
		</a>
	</li>
	<?php $this->end(); ?>

	<div class="modal-header">
		<?php $title = $this->fetch('title'); ?>
		<?php if ($title) : ?>
			<?php echo $title; ?>
		<?php else : ?>
			<br />
		<?php endif; ?>
	</div>


	<div class="modal-body">

		<ul class="nav nav-tabs">
			<?php foreach ($tabLists as $tab): ?>
			<li role="presentation" class="<?php echo $tab['class'] ?>">
				<a href="<?php echo $tab['href']; ?>">
					<?php echo $tab['tabTitle']; ?>
				</a>
			</li>
			<?php endforeach ?>
		</ul>

				<div class="tab-body has-feedback" >
					<?php echo $this->Form->create('Questionnaire', array(
					'id' => 'questionnaire_edit_list_pagenation_' . $frameId,
					'name' => 'questionnaire_edit_list_pagenation',
					'type' => 'get',
					'novalidate' => true,
					'class' => 'form-inline',
					)); ?>
						<div class="pagination-bar-top">
							<div class="pagination-label pull-left">
								{{(currentPageNumber - 1)*displayNumPerPage + 1}}
								<?php echo __d('questionnaires', ' - '); ?>
								{{questionnaires.itemCount}}
								<?php echo __d('questionnaires', '(All'); ?>
								{{totalCount}}
								<?php echo __d('questionnaires', ' items)'); ?>
							</div>
							<pagination on-select-page="pageChanged(page)" direction-links="false" boundary-links="true" total-items="totalCount" max-size="<?php echo QUESTIONNAIRE_DEFAULT_DISPLAY_PAGE_SIZE; ?>" page="currentPageNumber" ng-model="currentPageNumber" class="pagination-sm" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></pagination>
						</div>
					<?php echo $this->Form->input('page', array(
					'type' => 'hidden',
					'ng-value' => 'currentPageNumber'
					)); ?>
					<?php echo $this->Form->end(); ?>

					<?php echo $this->Form->create('Questionnaire', array(
					'id' => 'questionnaire_status_selector',
					'name' => 'questionnaire_form_status_filter',
					'type' => 'get',
					'novalidate' => true,
					'class' => 'form-inline',
					)); ?>
						<div class="form-group">
							<?php echo $this->element('Questionnaires.edit_status_selector'); ?>
						</div>
					<?php echo $this->Form->end(); ?>

					<accordion close-others="true">
						<accordion-group ng-repeat="item in questionnaires.items" heading="{{item.Entity.title}}" >
							<accordion-heading>
								<div class="row">
									<div class="col-sm-9">
										<span class="questionnaire-title">
											{{item.Entity.title}}
											<?php echo $this->element('Questionnaires.status_label',
											array('status' => 'item.Entity.status')); ?>
											<small ng-show="!!item.Entity.sub_title">
												<br />
												{{item.Entity.sub_title}}
											</small>
											<small ng-class="{'text-primary': item.Entity.questionPeriodFlag, 'text-muted': (!item.Entity.questionPeriodFlag)}">
												<br />
												<span class="glyphicon glyphicon-time"></span>
												<span class="text-primary" ng-show="!item.Entity.start_period && !item.Entity.end_period">
													<?php echo __d('questionnaires', 'Not limited'); ?>
												</span>
												<span ng-show="!!item.Entity.start_period">
													{{item.Entity.start_period | ncDatetime}}
												</span>
												<span ng-hide="!!item.Entity.start_period">
													<?php echo str_repeat('&nbsp;', 20); ?>
												</span>
												<span ng-show="!!item.Entity.start_period || !!item.Entity.end_period">
													<?php echo __d('questionnaires', ' - '); ?>
												</span>
												<span ng-show="!!item.Entity.end_period">
													{{item.Entity.end_period | ncDatetime}}
												</span>
											</small>
										</span>
									</div>
									<div class="col-sm-2">
										<span class="label label-danger" ng-if="item.Questionnaire.questionnaire_status == <?php echo(QuestionnairesComponent::STATUS_STOPPED); ?>">
											<?php echo __d('questionnaire', 'Stopped'); ?>
										</span>
									</div>
									<div class="col-sm-1">
										<span class="badge pull-right">{{item.AnswerSummary.answer_count}}</span>
									</div>
								</div>
							</accordion-heading>
							<p class="pull-right">
								<a class="btn btn-default"
								   href="<?php echo $this->Html->url(
									'questionnaire_questions/setting_list/' . $frameId . '/?questionnaire_id={{item.Entity.questionnaire_id}}' .
									'/?back_url=' . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/')) ?>">
									<span class="glyphicon glyphicon-pencil" ></span>
									<?php echo __d('questionnaires', 'Edit'); ?>
								</a>

								<script type="text/ng-template" id="templateId.html">
									<?php echo $this->element('Questionnaires.comments'); ?>
								</script>
								<button type="button" ng-disabled="item.Comments.length==0" comments="item.Comments" templateNumber="{{item.Questionnaire.id}}" class="btn btn-default" comment-popover popover-placement="bottom">
									<span class="glyphicon glyphicon-comment" ></span>
									<?php echo __d('questionnaires', 'Comment'); ?>
								</button>
<!--
								<button type="button" class="btn btn-default"
										ng-click="delete(<?php echo $frameId; ?>, item.Questionnaire.id, '<?php echo __d('questionnaires', 'Do you want to delete this questionnaire?'); ?>')">
									<span class="glyphicon glyphicon-trash" ></span>
									<?php echo __d('questionnaires', 'Delete'); ?>
								</button>
-->
							</p>
							<p>
								<small>
								<?php echo __d('questionnaires', 'Author'); ?>:{{item.CreatedUser.value}}
								<br />
								<?php echo __d('questionnaires', 'Last updated'); ?>:{{item.Entity.modified | ncDatetime}}
								<?php echo __d('questionnaires', 'Modified by'); ?>：{{item.ModifiedUser.value}}
								</small>
							</p>
						</accordion-group>
					</accordion>
					<div class="text-center">
						<button type="button" class="btn btn-default" ng-click="cancel()" ng-disabled="sending">
							<span class="glyphicon glyphicon-remove"></span>
							<?php echo __d('net_commons', 'Cancel'); ?>
						</button>
					</div>
				</div>

	</div>

</div>
