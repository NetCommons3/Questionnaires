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

	<?php echo $this->Form->hidden('id', array(
		'value' => $questionnaireFrameSettings['id'],
		)); ?>
	<?php echo $this->Form->hidden('Frame.id', array(
		'value' => $frameId,
		)); ?>
	<?php echo $this->Form->hidden('Block.id', array(
		'value' => $blockId,
		)); ?>

	<div class="panel panel-default">
		<div class="panel-body row">

			<div class="col-sm-12 form-group">
				<?php echo $this->element('Questionnaires.FrameSettings/edit_display_type'); ?>
			</div>

			<div class="col-sm-12 form-group" ng-show="questionnaireFrameSettings.display_type == <?php echo QuestionnairesComponent::DISPLAY_TYPE_LIST; ?>">
				<?php echo $this->element('Questionnaires.FrameSettings/edit_list_display_option'); ?>
			</div>

			<div class="col-sm-12 form-group">
				<?php echo $this->element('Questionnaires.FrameSettings/edit_display_questionnaire'); ?>
			</div>

		</div>

		<div class="panel-footer text-center">
			<button type="button" class="btn btn-default btn-workflow" onclick="location.href = '<?php echo $cancelUrl; ?>'">
				<span class="glyphicon glyphicon-remove"></span>
				<?php echo __d('net_commons', 'Cancel'); ?>
			</button>

			<?php echo $this->Form->button(__d('net_commons', 'OK'), array(
			'class' => 'btn btn-primary btn-workflow',
			'name' => 'save',
			)); ?>
		</div>
	</div>
<?php echo $this->Form->end();