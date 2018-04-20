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
	'/components/nvd3/build/nv.d3.min.js',
	'/components/angular-nvd3/dist/angular-nvd3.min.js',
	'/questionnaires/js/questionnaires_graph.js'
	));
echo $this->NetCommonsHtml->css('/components/nvd3/build/nv.d3.css');

$jsQuestions = NetCommonsAppController::camelizeKeyRecursive(QuestionnairesAppController::changeBooleansToNumbers($questions));
?>

<?php /* FUJI: 下のdivのidがnc-questionnaires-total-xx でよいか要確認. */ ?>
<div id="nc-questionnaires-total-<?php echo Current::read('Frame.id'); ?>"
	ng-controller="QuestionnairesAnswerSummary"
	ng-init="initialize(<?php echo h(json_encode($jsQuestions)); ?>)">

<article>
	<?php echo $this->element('Questionnaires.Answers/answer_header'); ?>

	<?php echo $this->element('Questionnaires.Answers/answer_test_mode_header'); ?>

	<?php if (!empty($questionnaire['Questionnaire']['total_comment'])): ?>
		<div class="row">
			<div class="col-xs-12">
					<p>
						<?php echo $questionnaire['Questionnaire']['total_comment']; ?>
					</p>
			</div>
		</div>
	<?php endif; ?>

	<?php foreach ($questions as $questionnaireQuestionId => $question): ?>
		<?php
			if (QuestionnairesComponent::isOnlyInputType($question['question_type'])) {
				continue;	//集計表示をしない、なので飛ばす
			}
			if ($question['is_result_display'] != QuestionnairesComponent::EXPRESSION_SHOW) {
				continue;	//集計表示をしない、なので飛ばす
			}

			//集計表示用のelement名決定
			$elementName = '';
			$matrix = '';
			if (QuestionnairesComponent::isMatrixInputType($question['question_type'])) {
				$matrix = '_matrix';
			}
			if ($question['result_display_type'] == QuestionnairesComponent::RESULT_DISPLAY_TYPE_BAR_CHART) {
				$elementName = 'Questionnaires.AnswerSummaries/aggregate' . $matrix . '_bar_chart';
			} elseif ($question['result_display_type'] == QuestionnairesComponent::RESULT_DISPLAY_TYPE_PIE_CHART) {
				$elementName = 'Questionnaires.AnswerSummaries/aggregate' . $matrix . '_pie_chart';
			} elseif ($question['result_display_type'] == QuestionnairesComponent::RESULT_DISPLAY_TYPE_TABLE) {
				$elementName = 'Questionnaires.AnswerSummaries/aggregate' . $matrix . '_table';
			} else {
				continue; // 不明な表示タイプ
			}
		?>
		<div class="row">
			<?php
			//各質問ごと集計表示の共通ヘッダー
			echo $this->element('Questionnaires.AnswerSummaries/aggregate_common_header',
				array('question' => $question));

			//グラフ・表の本体部分
			echo $this->element($elementName,
					array(
						'question' => $question,
						'questionId' => $questionnaireQuestionId));

			//各質問ごと集計表示の共通フッター
			echo $this->element('Questionnaires.AnswerSummaries/aggregate_common_footer',
				array('question' => $question));
			?>
		</div>
	<?php endforeach; ?>

	<div class="text-center">
	<?php if ($displayType == QuestionnairesComponent::DISPLAY_TYPE_LIST): ?>
		<?php echo $this->LinkButton->toList(); ?>
	<?php else:?>
		<?php echo $this->LinkButton->toList(__d('questionnaires', 'End'),
            null,
            array('icon' => 'remove', 'hiddenTitle' => false)); ?>
	<?php endif;?>
	</div>

</article>
</div>
