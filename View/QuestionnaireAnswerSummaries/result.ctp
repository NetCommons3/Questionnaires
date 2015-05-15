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

<?php /* FUJI: 下のdivのidがnc-questionnaires-total-xx でよいか要確認. */ ?>
<div id="nc-questionnaires-total-<?php echo (int)$frameId; ?>"
	ng-controller="Questionnaires"
	ng-init="initialize(<?php echo (int)$frameId; ?>,
<?php echo h(json_encode($questionnaire)); ?>)">

<?php App::uses('Sanitize', 'Utility'); ?>

<article>
	<header>
		<h1>
			<?php echo $questionnaire['Questionnaire']['title']; ?>
			<?php if (isset($questionnaire['Questionnaire']['sub_title'])): ?>
			<small><?php echo $questionnaire['Questionnaire']['sub_title'];?></small>
			<?php endif ?>
		</h1>
	</header>

	<?php if (!empty($questionnaire['Questionnaire']['total_comment'])): ?>
		<div class="row">
			<div class="col-xs-12">
					<p>
						<?php echo Sanitize::stripAll($questionnaire['Questionnaire']['total_comment']); ?>
					</p>
			</div>
		</div>
	<?php endif; ?>

	<?php
		foreach ($questions as $questionnaireQuestionId => $question) {
			if ($question['is_result_display'] != QuestionnairesComponent::EXPRESSION_SHOW) {
				//集計表示をしない、なので飛ばす
				continue;
			}

			//集計表示用のelement名決定
			$elementName = '';
			$matrix = '';
			if ($question['question_type'] == QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST
				|| $question['question_type'] == QuestionnairesComponent::TYPE_MATRIX_MULTIPLE) {
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
			echo $this->element($elementName,
				array(
					'frameId' => $frameId,
					'questionnaireId' => $questionnaireId,
					'questionnaire' => $questionnaire,
					'question' => $question
				)
			);
		}
	?>

	<div class="text-center">
		<?php echo $this->BackToPage->backToPageButton(__d('questionnaires', 'Back to Top'), '', 'lg'); ?>
	</div>

</article>
</div>