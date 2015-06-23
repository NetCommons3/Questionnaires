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
<articel>

	<?php
		echo $this->element('Questionnaires.Answers/answer_test_mode_header');
	?>

	<?php echo $this->Form->create('PreAnswer', array(
	'name' => 'questionnaire_form_answer',
	'type' => 'post',
	'novalidate' => true,
	)); ?>

	<?php echo $this->Form->hidden('id'); ?>
	<?php echo $this->Form->hidden('Frame.id', array('value' => $frameId)); ?>
	<?php echo $this->Form->hidden('Block.id', array('value' => $blockId)); ?>
	<?php echo $this->Form->hidden('Questionnaire.id', array('value' => $questionnaire['Questionnaire']['id'])); ?>
	<?php echo $this->Form->hidden('PreAnswer.test_mode', array('value' => true)); ?>

	<div class="modal-body">
		<?php
		echo $this->element('Questionnaires.Answers/answer_header');
		?>
		<div class="row">
			<div class="col-sm-12">
				<h3><?php echo __d('questionnaires', 'Questionnaire answer period'); ?></h3>
				<?php if ($questionnaire['Questionnaire']['is_period'] == QuestionnairesComponent::USES_USE): ?>
					<?php echo date('Y/m/d H:i', strtotime($questionnaire['Questionnaire']['start_period'])); ?>
					<?php echo __d('questionnaires', ' - '); ?>
					<?php echo date('Y/m/d H:i', strtotime($questionnaire['Questionnaire']['end_period'])); ?>
				<?php else: ?>
					<?php echo __d('questionnaires', 'do not set the answer period'); ?>
				<?php endif; ?>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<h3><?php echo __d('questionnaires', 'Counting result display start date'); ?></h3>
				<?php if ($questionnaire['Questionnaire']['total_show_timing'] == QuestionnairesComponent::USES_USE): ?>
					<?php echo date('Y/m/d H:i', strtotime($questionnaire['Questionnaire']['total_show_start_period'])); ?>
					<?php echo __d('questionnaires', ' - '); ?>
				<?php else: ?>
					<?php echo __d('questionnaires', 'do not set the aggregate display period'); ?>
				<?php endif; ?>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<h3><?php echo __d('questionnaires', 'Questionnaire method'); ?></h3>
				<ul>
					<li>
						<?php if ($questionnaire['Questionnaire']['is_no_member_allow'] == QuestionnairesComponent::USES_USE): ?>
							<?php echo __d('questionnaires', 'accept the non-members answer'); ?>
						<?php else: ?>
							<?php echo __d('questionnaires', 'do not accept the non-members answer'); ?>
						<?php endif; ?>
					</li>

					<li>
						<?php if ($questionnaire['Questionnaire']['is_key_pass_use'] == QuestionnairesComponent::USES_USE): ?>
							<?php echo __d('questionnaires', 'use key phrase'); ?>
								<dl class="dl-horizontal">
									<dt><?php echo __d('questionnaires', 'key phrase'); ?>:</dt>
									<dd><?php echo h($questionnaire['Questionnaire']['key_phrase']); ?></dd>
								</dl>
						<?php else: ?>
							<?php echo __d('questionnaires', 'do not use key phrase'); ?>
						<?php endif; ?>
					</li>

					<li>
						<?php if ($questionnaire['Questionnaire']['is_anonymity'] == QuestionnairesComponent::USES_USE): ?>
							<?php echo __d('questionnaires', 'anonymous answer'); ?>
						<?php else: ?>
							<?php echo __d('questionnaires', 'register answer'); ?>
						<?php endif; ?>
					</li>

					<li>
						<?php if ($questionnaire['Questionnaire']['is_repeat_allow'] == QuestionnairesComponent::USES_USE): ?>
							<?php echo __d('questionnaires', 'forgive the repetition of the answer'); ?>
						<?php else: ?>
							<?php echo __d('questionnaires', 'do not forgive the repetition of the answer'); ?>
						<?php endif; ?>
					</li>

					<li>
						<?php if ($questionnaire['Questionnaire']['is_image_authentication'] == QuestionnairesComponent::USES_USE): ?>
							<?php echo __d('questionnaires', 'do image authentication'); ?>
						<?php else: ?>
							<?php echo __d('questionnaires', 'do not image authentication'); ?>
						<?php endif; ?>
					</li>

					<li>
						<?php if ($questionnaire['Questionnaire']['is_answer_mail_send'] == QuestionnairesComponent::USES_USE): ?>
						<?php echo __d('questionnaires', 'Deliver e-mail when submitted'); ?>
						<?php else: ?>
						<?php echo __d('questionnaires', 'do not deliver e-mail when submitted'); ?>
						<?php endif; ?>
					</li>
				</ul>
			</div>
		</div>

	</div>
	<div class="modal-footer">
		<div class="text-center">
			<?php echo $this->BackToPage->backToPageButton(__d('net_commons', 'Cancel'), 'remove'); ?>
			<?php echo $this->Form->button(
			__d('questionnaires', 'Start the test answers of this questionnaire') . ' <span class="glyphicon glyphicon-chevron-right"></span>',
			array(
			'class' => 'btn btn-primary',
			'name' => 'next_' . '',
			)) ?>
		</div>
	</div>
	<?php echo $this->Form->end(); ?>

</articel>