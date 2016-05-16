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

<article id="nc-questionnaires-answer-confirm-<?php Current::read('Frame.id'); ?>">

	<?php echo $this->element('Questionnaires.Answers/answer_test_mode_header'); ?>

	<?php echo $this->element('Questionnaires.Answers/answer_header'); ?>

		<p>
			<?php echo $questionnaire['Questionnaire']['thanks_content']; ?>
		</p>
		<hr>

		<div class="text-center">
			<?php if ($displayType == QuestionnairesComponent::DISPLAY_TYPE_LIST): ?>
				<?php echo $this->BackTo->indexLinkButton(__d('questionnaires', 'Back to page')); ?>
			<?php endif; ?>
			<?php
				echo $this->QuestionnaireUtil->getAggregateButtons($questionnaire,
					array('title' => __d('questionnaires', 'Aggregate'),
							'class' => 'primary',
			));
			?>
		</div>
</article>