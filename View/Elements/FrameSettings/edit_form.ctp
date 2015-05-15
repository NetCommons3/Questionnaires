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
<?php echo $this->Form->create('QuestionnaireFrameSetting', array(
	'type' => 'post',
	'novalidate' => true,
	)); ?>

	<?php echo $this->Form->hidden('id'); ?>
	<?php echo $this->Form->hidden('Frame.id', array(
		'value' => $frameId,
		)); ?>
	<?php echo $this->Form->hidden('Block.id', array(
		'value' => $blockId,
		)); ?>

<div class="panel panel-default">
	<div class="panel-body">

		<div class="row form-group questionnaire-group">
			<label><?php echo __d('questionnaires', 'Questionnaire display setting'); ?></label>
				<?php echo $this->Form->input('display_type', array(
					'type' => 'radio',
					'options' => array(
							QuestionnairesComponent::DISPLAY_TYPE_SINGLE => __d('questionnaires', 'Show only one questionnaire'),
							QuestionnairesComponent::DISPLAY_TYPE_LIST => __d('questionnaires', 'Show questionnaires list')),
					'legend' => false,
					'label' => false,
					'before' => '<label class="radio-inline">',
					'separator' => '</label><label class="radio-inline">',
					'after' => '</label>',
					'ng-model' => 'questionnaires.QuestionnaireFrameSettings.display_type'
					)); ?>
		</div>

<div class="form-group questionnaire-group" ng-if="questionnaires.QuestionnaireFrameSettings.display_type == <?php echo QuestionnairesComponent::DISPLAY_TYPE_SINGLE; ?>">
	<label><?php echo __d('questionnaires', 'select display questionnaires.'); ?></label>

	<div class="form-inline">
		<label>
			<?php echo __d('questionnaires', 'Narrow down'); ?>
			<input type="search" class="form-control" ng-model="q.Entity.title" placeholder="<?php echo __d('questionnaires', 'Refine by entering the part of the questionnaire name'); ?>" />
		</label>
	</div>
	<div style="height:200px;overflow-y: scroll;">
		<table class="table table-hover">
			<tr>
				<th><?php echo __d('questionnaires', 'Display'); ?></th>
				<th><?php echo __d('questionnaires', 'Status'); ?></th>
				<th><?php echo __d('questionnaires', 'Title'); ?></th>
				<th><?php echo __d('questionnaires', 'Implementation date'); ?></th>
				<th><?php echo __d('questionnaires', 'Aggregates'); ?></th>
			</tr>
			<tr class="animate-repeat btn-default" ng-repeat="item in questionnaires | filter:q" >
				<td>
					<input type="radio" name="display" style="margin-left:10px;">
				</td>
				<td>
					<?php echo $this->element('Questionnaires.status_label',
					array('status' => 'item.Questionnaire.status')); ?>
				</td>
				<td>{{item.Questionnaire.title}}</td>
				<td><span ng-if="item.Questionnaire.is_period">
													<?php echo __d('questionnaires', 'Not limited'); ?>
												</span>
												<span ng-if="item.Questionnaire.is_period">
													{{item.Questionnaire.start_period | ncDatetime}}
													<?php echo __d('questionnaires', ' - '); ?>
													{{item.Questionnaire.end_period | ncDatetime}}
												</span>
				</td>
				<td>
							<span ng-if="item.Questionnaire.is_total_show == <?php echo (QuestionnairesComponent::EXPRESSION_SHOW); ?>">
								<?php echo __d('questionnaires', 'Yes'); ?>
							</span>
				</td>
			</tr>
		</table>

	</div>
</div>

<div class="form-group questionnaire-group" ng-if="questionnaires.QuestionnaireFrameSettings.display_type == <?php echo QuestionnairesComponent::DISPLAY_TYPE_LIST; ?>">
	<div class='form-group'>
		<?php
						echo $this->Form->label(__d('questionnaires', 'Visible questionnaire row'));
		?>
		&nbsp;
		<?php
						echo $this->Form->input('QuestionnairesFrameSetting.display_num_per_page', array(
		'label' => false,
		'type' => 'select',
		'class' => 'form-control',
		'options' => QuestionnairesComponent::getDisplayNumberOptions(),
		'selected' => $questionnaireFrameSettings['display_num_per_page'],
		'autofocus' => true,
		)
		);
		?>
	</div>

	<div class='form-group'>
		<?php
					echo $this->Form->label(__d('questionnaires', 'Visible row order'));
		?>
		&nbsp;
		<?php
					echo $this->Form->input('QuestionnairesFrameSetting.sort_type', array(
		'label' => false,
		'type' => 'select',
		'class' => 'form-control',
		'options' => QuestionnairesComponent::getSortOrders(),
		'selected' => $questionnaireFrameSettings['sort_type'],
		)
		);
		?>
	</div>
</div>

		</div>

		<div class="panel-footer text-center">
				<a class="btn btn-default" href="/<?php echo $topUrl; ?>">
					<span class="glyphicon glyphicon-remove"></span>
					<?php echo __d('net_commons', 'Cancel'); ?>
				</a>
				<?php echo $this->Form->button(
				__d('net_commons', 'OK'),
				array(
				'class' => 'btn btn-primary',
				'name' => 'save_' . NetCommonsBlockComponent::STATUS_PUBLISHED,
				)) ?>
		</div>

	</div>
<?php echo $this->Form->end();