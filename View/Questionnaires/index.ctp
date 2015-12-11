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

echo $this->element('Questionnaires.scripts');
?>

<div id="nc-questionnaires-<?php echo Current::read('Frame.id'); ?>" ng-controller="Questionnaires">

	<?php echo $this->element('Questionnaires.Questionnaires/add_button'); ?>

	<div class="pull-left">
		<?php echo $this->element('Questionnaires.Questionnaires/answer_status'); ?>
	</div>

	<div class="clearfix"></div>

	<table class="table nc-content-list">
		<?php foreach($questionnaires as $questionnaire): ?>
			<tr><td>
				<article class="row">
					<div class="col-md-8 col-xs-12">

						<?php echo $this->QuestionnaireStatusLabel->statusLabel($questionnaire);?>

						<?php if ($questionnaire['Questionnaire']['public_type'] == WorkflowBehavior::PUBLIC_TYPE_LIMITED): ?>
							<strong>
								<?php echo $this->Date->dateFormat($questionnaire['Questionnaire']['publish_start']); ?>
								<?php echo __d('questionnaires', ' - '); ?>
								<?php echo $this->Date->dateFormat($questionnaire['Questionnaire']['publish_end']); ?>
							</strong>
						<?php endif ?>

						<h2>
							<?php echo h($questionnaire['Questionnaire']['title']); ?>
							<br>
							<small><?php echo h($questionnaire['Questionnaire']['sub_title']); ?></small>
						</h2>

					</div>


					<div class="col-md-4 col-xs-12" >
						<div class="pull-right h3">
							<?php echo $this->QuestionnaireUtil->getAnswerButtons($questionnaire); ?>
							<?php echo $this->QuestionnaireUtil->getAggregateButtons($questionnaire, array('icon' => 'stats')); ?>
							<div class="clearfix"></div>
						</div>
					</div>
				</article>

				<?php if ($this->Workflow->canEdit('Questionnaire', $questionnaire)) : ?>
					<?php echo $this->element('Questionnaires.Questionnaires/detail_for_editor', array('questionnaire' => $questionnaire)); ?>
				<?php endif ?>
			</td></tr>
		<?php endforeach; ?>
	</table>

	<?php echo $this->element('NetCommons.paginator'); ?>

</div>