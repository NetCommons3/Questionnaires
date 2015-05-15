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


<?php echo $this->element('Questionnaires.Answers/answer_test_mode_header'); ?>

<article>
	<header>
		<h1>
			<?php echo $questionnaire['Questionnaire']['title']; ?>
			<?php if (isset($questionnaire['Questionnaire']['sub_title'])): ?>
			<small><?php echo $questionnaire['Questionnaire']['sub_title'];?></small>
			<?php endif ?>
		</h1>
	</header>

	<?php echo $this->Form->create('QuestionnaireAnswer', array(
	'name' => 'questionnaire_form_answer',
	'type' => 'post',
	'novalidate' => true,
	)); ?>
	<?php echo $this->Form->hidden('Frame.id', array('value' => $frameId)); ?>
	<?php echo $this->Form->hidden('Block.id', array('value' => $blockId)); ?>
	<?php echo $this->Form->hidden('Questionnaire.id', array('value' => $questionnaire['Questionnaire']['id'])); ?>

	<p>
		<?php echo $questionnaire['Questionnaire']['thanks_content']; ?>
	</p>
	<hr>
	<div class="text-center">
		<?php echo $this->BackToPage->backToPageButton(__d('questionnaires', 'Back to page'), 'menu-up', 'lg'); ?>
		<?php if ($questionnaire['Questionnaire']['is_total_show'] == QuestionnairesComponent::EXPRESSION_SHOW &&
			(!isset($questionnaire['Questionnaire']['total_show_start_period']) || time() > $questionnaire['Questionnaire']['total_show_start_period'])): ?>
		<?php echo $this->Html->link(__d('questionnaires', 'Aggregate'),
			'/questionnaires/questionnaire_answer_summaries/result/' . $frameId . '/' . $questionnaire['Questionnaire']['origin_id'] . '/',
			array('class' => 'btn btn-primary btn-lg',
				'target' => '_self')); ?>
		<?php endif ?>
	</div>
</article>