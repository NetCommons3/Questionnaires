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

	<?php echo $this->NetCommonsForm->create('QuestionnaireAnswer', array(
	)); ?>
		<?php echo $this->NetCommonsForm->hidden('Frame.id'); ?>
		<?php echo $this->NetCommonsForm->hidden('Block.id'); ?>
		<?php echo $this->NetCommonsForm->hidden('Questionnaire.id', array('value' => $questionnaire['Questionnaire']['id'])); ?>

		<p>
			<?php echo $questionnaire['Questionnaire']['thanks_content']; ?>
		</p>
		<hr>

		<div class="text-center">
			<?php echo $this->BackTo->pageLinkButton(__d('questionnaires', 'Back to page'), array(
				'icon' => 'remove',
				'iconSize' => 'lg')); ?>
			<?php
				/* この画面へ来るときはAnswerCountは取得してないSQLで来ている */
				/* 集計ボタン表示Helperは回答数が１つはないと表示されない */
				/* 今回答したのだから必ず回答は１つはある */
				/* 強制的に一つ増やしておく */
				$questionnaire['CountAnswerSummary'] = array('answer_summary_count' => 1);
				echo $this->QuestionnaireUtil->getAggregateButtons(Current::read('Frame.id'), $questionnaire,
					array('title' => __d('questionnaires', 'Aggregate'),
							'class' => 'btn-primary btn-lg'));
			?>
		</div>
	<?php echo $this->NetCommonsForm->end(); ?>
</article>