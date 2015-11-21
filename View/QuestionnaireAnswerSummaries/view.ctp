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
echo $this->NetCommonsHtml->script(array(
	'/components/d3/d3.min.js',
	'/components/nvd3/nv.d3.min.js',
	'/components/angular-nvd3/dist/angular-nvd3.min.js',
	'/questionnaires/js/questionnaires_graph.js'
	));
echo $this->NetCommonsHtml->css('/components/nvd3/nv.d3.css');
?>

<?php echo $this->Html->scriptStart(array('inline' => false)); ?>
	NetCommonsApp.requires.push('nvd3');
<?php echo $this->Html->scriptEnd(); ?>

<?php /* FUJI: 下のdivのidがnc-questionnaires-total-xx でよいか要確認. */ ?>
<div id="nc-questionnaires-total-<?php echo Current::read('Frame.id'); ?>"
	ng-controller="QuestionnairesAnswerSummary"
	ng-init="initialize(<?php echo Current::read('Frame.id'); ?>,
	<?php echo h(json_encode($jsQuestionnaire)); ?>,
	<?php echo h(json_encode($jsQuestions)); ?>)">

<?php App::uses('Sanitize', 'Utility'); ?>

<article>
	<?php echo $this->element('Questionnaires.Answers/answer_test_mode_header'); ?>

	<?php echo $this->element('Questionnaires.Answers/answer_header'); ?>

	<?php if (!empty($questionnaire['Questionnaire']['total_comment'])): ?>
		<div class="row">
			<div class="col-xs-12">
					<p>
						<?php echo Sanitize::stripAll($questionnaire['Questionnaire']['total_comment']); ?>
					</p>
			</div>
		</div>
	<?php endif; ?>

	<?php foreach ($questions as $questionnaireQuestionId => $question): ?>
		<?php
			if ($question['is_result_display'] != QuestionnairesComponent::EXPRESSION_SHOW) {
				//集計表示をしない、なので飛ばす
				continue;
			}

			//集計表示用のelement名決定
			$elementName = '';
			$matrix = '';
			if (QuestionnairesComponent::isMatrixInputType($question['question_type'])) {
				$matrix = '_matrix';
			}
			if ($question['result_display_type'] == QuestionnairesComponent::RESULT_DISPLAY_TYPE_BAR_CHART) {
				$elementName = 'Questionnaires.AnswerSummaries/aggrigate' . $matrix . '_bar_chart';
			} elseif ($question['result_display_type'] == QuestionnairesComponent::RESULT_DISPLAY_TYPE_PIE_CHART) {
				$elementName = 'Questionnaires.AnswerSummaries/aggrigate' . $matrix . '_pie_chart';
			} elseif ($question['result_display_type'] == QuestionnairesComponent::RESULT_DISPLAY_TYPE_TABLE) {
				$elementName = 'Questionnaires.AnswerSummaries/aggrigate' . $matrix . '_table';
			}

			if ($elementName === '') {
				continue; //element名が決まらない場合、次へ。
			}
		?>
		<div class="row">
			<?php
			//各質問ごと集計表示の共通ヘッダー
			echo $this->element('Questionnaires.AnswerSummaries/aggrigate_common_header',
			array(
			'frameId' => Current::read('Frame.id'),
			'questionnaireId' => $questionnaireId,
			'questionnaire' => $questionnaire,
			'question' => $question
			)
			);
			?>

			<?php echo $this->element($elementName,
					array(
						'frameId' => Current::read('Frame.id'),
						'questionnaireId' => $questionnaireId,
						'questionnaire' => $questionnaire,
						'question' => $question,
						'questionId' => $questionnaireQuestionId
					)
				);
			?>

			<?php
			//各質問ごと集計表示の共通フッター
			echo $this->element('Questionnaires.AnswerSummaries/aggrigate_common_footer',
			array(
			'frameId' => Current::read('Frame.id'),
			'questionnaireId' => $questionnaireId,
			'questionnaire' => $questionnaire,
			'question' => $question
			)
			);
			?>

		</div>
	<?php endforeach; ?>

	<div class="text-center">
		<?php echo $this->BackTo->pageLinkButton(__d('questionnaires', 'Back to Top'), array('icon' => 'menu-up', 'iconSize' => 'lg')); ?>
	</div>

</article>
</div>
