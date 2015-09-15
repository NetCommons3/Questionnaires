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
									<?php echo h(json_encode($jsPastQuestionnaires)); ?>,
									'<?php echo $createOption; ?>')">

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

				<?php /* ファイル送信は、FormHelperでform作成時、'type' => 'file' 必要。記述すると enctype="multipart/form-data" が追加される */ ?>
				<?php echo $this->Form->create('Questionnaire', array(
				'type' => 'post',
				'novalidate' => true,
				'type' => 'file',
				'ng-keydown' => 'handleKeydown($event)'
				)); ?>
				<?php $this->Form->unlockField('create_option'); ?>
				<?php $this->Form->unlockField('past_questionnaire_id'); ?>

				<?php echo $this->Form->hidden('Frame.id', array(
				'value' => $frameId,
				)); ?>
				<?php echo $this->Form->hidden('Block.id', array(
				'value' => $blockId,
				)); ?>
				<div class="form-group col-lg-12">
					<div class="radio">
						<label>
							<input type="radio" name="create_option" value="<?php echo QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW; ?>" ng-model="createOption">
							<?php echo __d('questionnaires', 'Create new questionnaire'); ?>
						</label>
					</div>
					<div  collapse="createOption != '<?php echo QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW; ?>'">
						<?php echo $this->Form->input('title', array(
						'class' => 'form-control',
						'label' => __d('questionnaires', 'Questionnaire title') . $this->element('NetCommons.required'),
						'ng-model' => 'newTitle',
						'placeholder' => __d('questionnaires', 'Please input questionnaire title')
						)); ?>
						<?php echo $this->element(
						'NetCommons.errors', [
						'errors' => $this->validationErrors,
						'model' => 'Questionnaire',
						'field' => 'title',
						]) ?>
					</div>
				</div><!-- /form-group 1-->

				<div class="form-group col-lg-12">
					<div class="radio">
						<label>
							<input type="radio" name="create_option" value="<?php echo QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_TEMPLATE; ?>" ng-model="createOption">
							<?php echo __d('questionnaires', 'Create from Template'); ?>
						</label>
					</div>
					<div  collapse="createOption != '<?php echo QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_TEMPLATE; ?>'">
						<label for="questionnaires_past_search_filter">
							<?php echo __d('questionnaires', 'Questionnaire template file'); ?>
							<?php echo $this->element('NetCommons.required'); ?>
						</label>
						<?php /* 本当はこの辺は共通部品になるはず とりあえず直接書いておく */ ?>
						<?php echo $this->Form->file('template_file', array(
							'accept' => "text/comma-separated-values",
						)); ?>
						<?php echo $this->Form->hidden('template_file' . '.File.status', array(
							'value' => 1
						)); ?>
						<?php echo $this->Form->hidden('template_file' . '.File.role_type', array(
							'value' => 'room_file_role'
						)); ?>
						<?php echo $this->Form->hidden('template_file' . '.File.path', array(
							'value' => '{ROOT}' . 'questionnaires' . '{DS}' . $roomId . '{DS}'
						)); ?>
						<?php echo $this->Form->hidden('template_file' . '.FilesPlugin.plugin_key', array(
							'value' => 'questionnaires'
						)); ?>
						<?php echo $this->Form->hidden('template_file' . '.FilesRoom.room_id', array(
							'value' => $roomId
						)); ?>
						<?php echo $this->Form->hidden('template_file' . '.FilesUser.user_id', array(
							'value' => (int)AuthComponent::user('id')
						)); ?>
						<?php echo $this->element(
						'NetCommons.errors', [
						'errors' => $this->validationErrors,
						'model' => 'Questionnaire',
						'field' => 'template_file',
						]) ?>
					</div>
				</div><!-- /form-group 1-->

				<div class="form-group col-lg-12">
					<div class="radio" name="create_option" ng-model="isCollapse">
						<label>
							<input type="radio" name="create_option" value="<?php echo QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_REUSE; ?>" ng-model="createOption" ng-disabled="questionnaires.items.length == 0">
							<?php echo __d('questionnaires', 'Re-use past questionnaire'); ?>
						</label>
					</div>
					<div class="form-group" collapse="createOption != '<?php echo QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_REUSE; ?>'">
						<label for="questionnaires_past_search_filter">
							<?php echo __d('questionnaires', 'Past questionnaire'); ?>
							<?php echo $this->element('NetCommons.required'); ?>
						</label>
						<input type="search" id="questionnaires_past_search_filter" class="form-control" ng-model="q.questionnaire.title" placeholder="<?php echo __d('questionnaires', 'Refine by entering the part of the questionnaire name'); ?>" />
						<ul class="questionnaire-select-box form-control ">
							<li class="animate-repeat btn-default" ng-repeat="item in questionnaires.items | filter:q" ng-model="$parent.pastQuestionnaireSelect" btn-radio="item.questionnaire.id" uncheckable>
								{{item.questionnaire.title}}

								<?php echo $this->element('Questionnaires.status_label',
								array('status' => 'item.questionnaire.status')); ?>

								<span ng-if="item.questionnaire.isPeriod != 0">
								(
									{{item.questionnaire.startPeriod | ncDatetime}}
									<?php echo __d('questionnaires', ' - '); ?>
									{{item.questionnaire.endPeriod | ncDatetime}}
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
						<input type="hidden" name="past_questionnaire_id" value="{{pastQuestionnaireSelect}}" />
					</div><!-- /input-group -->
				</div><!-- /form-group 2-->

				<div class="text-center">
					<?php echo $this->BackToPage->backToPageButton(__d('net_commons', 'Cancel'), 'remove'); ?>
					<?php echo $this->Form->button(
					__d('net_commons', 'NEXT') . ' <span class="glyphicon glyphicon-chevron-right"></span>',
					array(
					'class' => 'btn btn-primary',
					'name' => 'next_' . '',
					)) ?>
				</div>

				<?php echo $this->Form->end(); ?>
			</div>
	</div>
</div>