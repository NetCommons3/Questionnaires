<?php
/**
 * questionnaire create view template
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
	 ng-controller="Questionnaires.add"
	 ng-init="initialize(<?php echo (int)$frameId; ?>,
									<?php echo h(json_encode($questionnaires)); ?>)">

	<?php $this->start('title'); ?>
	<?php echo __d('questionnaires', 'plugin_name'); ?>
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
			<div class="row">

				<div class="col-lg-12">
					<p>
						<?php echo __d('questionnaires', 'You can create a new questionnaire. Please choose how to create.'); ?>
					</p>
					<p>
						<?php echo $this->element(
						'NetCommons.errors', [
						'errors' => $this->validationErrors,
						'model' => 'Questionnaire',
						'field' => 'create_option',
						]) ?>
					</p>
				</div>

				<?php echo $this->Form->create('Questionnaire', array(
				'type' => 'post',
				'novalidate' => true,
				'ng-keydown' => 'handleKeydown($event)'
				)); ?>
				<?php echo $this->Form->hidden('Frame.id', array(
				'value' => $frameId,
				)); ?>
				<?php echo $this->Form->hidden('Block.id', array(
				'value' => $blockId,
				)); ?>
				<div class="form-group col-lg-12">
					<div class="radio">
						<label>
							<input type="radio" name="create_option" value="<?php echo QUESTIONNAIRE_CREATE_OPT_NEW; ?>" ng-model="createOption">
							<?php echo __d('questionnaires', 'Create new questionnaire'); ?>
						</label>
					</div>
					<div  collapse="createOption != '<?php echo QUESTIONNAIRE_CREATE_OPT_NEW; ?>'">
						<label for="Questionnaire.title" >
							<?php echo __d('questionnaires', 'Questionnaire title'); ?>
							<?php echo $this->element('NetCommons.required'); ?>
						</label>
						<input type="text"
							   class="form-control"
							   id="Questionnaire.title"
							   name="title"
							   ng-model="newTitle"
							   placeholder="<?php echo __d('questionnaires', 'Please input questionnaire title'); ?>">
						<?php echo $this->element(
						'NetCommons.errors', [
						'errors' => $this->validationErrors,
						'model' => 'Questionnaire',
						'field' => 'title',
						]) ?>
					</div>
				</div><!-- /form-group 1-->

				<div class="form-group col-lg-12">
					<div class="radio" name="create_option" ng-model="isCollapse">
						<label>
							<input type="radio" name="create_option" value="<?php echo QUESTIONNAIRE_CREATE_OPT_REUSE; ?>" ng-model="createOption" ng-disabled="questionnaires.items.length == 0">
							<?php echo __d('questionnaires', 'Re-use past questionnaire'); ?>
						</label>
					</div>
					<div class="form-group" collapse="createOption != '<?php echo QUESTIONNAIRE_CREATE_OPT_REUSE; ?>'">
						<label for="questionnaires_past_search_filter">
							<?php echo __d('questionnaires', 'Past questionnaire'); ?>
							<?php echo $this->element('NetCommons.required'); ?>
						</label>
						<input type="search" id="questionnaires_past_search_filter" class="form-control" ng-model="q.Questionnaire.title" placeholder="<?php echo __d('questionnaires', 'Refine by entering the part of the questionnaire name'); ?>" />
						<ul class="questionnaire-select-box form-control ">
							<li class="animate-repeat btn-default" ng-repeat="item in questionnaires.items | filter:q" ng-model="$parent.pastQuestionnaireSelect" btn-radio="item.Questionnaire.id" uncheckable>
								{{item.Questionnaire.title}}

								<?php echo $this->element('Questionnaires.status_label',
								array('status' => 'item.Questionnaire.status')); ?>

								<span ng-if="!!item.Questionnaire.is_period">
								(
									{{item.Questionnaire.start_period | ncDatetime}}
									<?php echo __d('questionnaires', ' - '); ?>
									{{item.Questionnaire.end_period | ncDatetime}}
									<?php echo __d('questionnaires', 'Implementation'); ?>
								)
								</span>
							</li>
						</ul>
						<?php echo $this->element(
						'NetCommons.errors', [
						'errors' => $this->validationErrors,
						'model' => 'Questionnaire',
						'field' => 'past_questionnaire_id',
						]) ?>
						<input type="hidden" name="questionnaire_id" value="{{pastQuestionnaireSelect}}" />
					</div><!-- /input-group -->
				</div><!-- /form-group 2-->

				<div class="text-center">
					<?php echo $this->BackToPage->backToPageButton(__d('net_commons', 'Cancel'), 'remove'); ?>
					<?php echo $this->Form->button(
					__d('net_commons', 'NEXT') . ' <span class="glyphicon glyphicon-chevron-right"></span>',
					array(
					'class' => 'btn btn-primary',
					'name' => 'next_' . '',
					'ng-disabled' => "!((createOption=='" . QUESTIONNAIRE_CREATE_OPT_NEW . "' && newTitle) || (createOption=='" . QUESTIONNAIRE_CREATE_OPT_REUSE . "' && pastQuestionnaireSelect))",
					)) ?>
				</div>

				<?php echo $this->Form->end(); ?>
			</div>
	</div>
</div>