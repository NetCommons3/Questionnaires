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
<?php echo $this->element('Questionnaires.scripts'); ?>

<article id="nc-questionnaires-confirm"
		 ng-controller="QuestionnairesAnswer">

	<?php echo $this->element('Questionnaires.Answers/answer_header'); ?>

	<?php echo $this->element('Questionnaires.Answers/answer_test_mode_header'); ?>

	<p>
		<?php echo __d('questionnaires', 'Please confirm your answers.'); ?>
	</p>

	<?php echo $this->NetCommonsForm->create('QuestionnaireAnswer'); ?>
	<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
	<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>
	<?php echo $this->NetCommonsForm->hidden('Questionnaire.id', array('value' => $questionnaire['Questionnaire']['id'])); ?>

	<?php foreach($questionnaire['QuestionnairePage'] as $pIndex => $page): ?>
		<?php foreach($page['QuestionnaireQuestion'] as $qIndex => $question): ?>

			<?php if (isset($answers[$question['key']])): ?>

				<?php if ($question['is_require'] == QuestionnairesComponent::REQUIRES_REQUIRE): ?>
					<div class="pull-left">
						<?php echo $this->element('NetCommons.required'); ?>
					</div>
				<?php endif ?>

				<label>
					<?php echo h($question['question_value']); ?>
				</label>

				<div class="well form-control-static">
					<div class="form-group">
						 <?php echo $this->QuestionnaireAnswer->answer($question, true); ?>
					</div>
				</div>
			<?php endif ?>
		<?php endforeach; ?>
	<?php endforeach; ?>


	<div class="text-center">

		<a class="btn btn-default" href="<?php echo $this->NetCommonsHtml->url(array(
																	'controller' => 'questionnaire_answers',
																	'action' => 'view',
																	'block_id' => Current::read('Block.id'),
																	'key' => $questionnaire['Questionnaire']['key'],
																	'frame_id' => Current::read('Frame.id'))); ?>">
			<span class="glyphicon glyphicon-chevron-left"></span>
			<?php echo __d('questionnaires', 'Start over'); ?>
		</a>

		<?php echo $this->NetCommonsForm->button(
		__d('net_commons', 'Confirm'),
		array(
		'class' => 'btn btn-primary',
		'name' => 'confirm_' . 'questionnaire',
		)) ?>
	</div>
	<?php echo $this->NetCommonsForm->end(); ?>

</article>