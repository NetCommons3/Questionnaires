
<?php echo $this->element('Questionnaires.scripts'); ?>

<?php /* TODO: 下のdivのidがnc-questionnaires-total-xx でよいか要確認. */ ?>
<div id="nc-questionnaires-total-<?php echo (int)$frameId; ?>"
	ng-controller="Questionnaires"
	ng-init="initialize(<?php echo (int)$frameId; ?>,
<?php echo h(json_encode($questionnaire)); ?>)">

<?php App::uses('Sanitize','Utility'); ?>

<section>
    <header>
		<?php 
			if (!empty($questionnaireEntity['QuestionnaireEntity']['total_comment'])) {
				echo '<h3>'.Sanitize::stripAll($questionnaireEntity['QuestionnaireEntity']['total_comment']).'</h3>';
			}
		?>
	</header>
	<?php
		foreach($questions as $questionnaireQuestionId => $question) {
            if ($question['result_display_flag'] != QuestionnairesComponent::EXPRESSION_SHOW) {
                //集計表示をしない、なので飛ばす
                continue;
            }

			//集計表示用のelement名決定
			$element_name = '';
            if ($question['result_display_type'] == QuestionnairesComponent::RESULT_DISPLAY_TYPE_BAR_CHART) {
            	if (isset($question['isMatrix'])) {
					$element_name = ($question['isMatrix']) ? 'Questionnaires.aggrigate_matrix_bar_chart' : 'Questionnaires.aggrigate_bar_chart';
				}
			}
            else if ($question['result_display_type'] == QuestionnairesComponent::RESULT_DISPLAY_TYPE_PIE_CHART) {
            	if (isset($question['isMatrix'])) {
					$element_name = ($question['isMatrix']) ? 'Questionnaires.aggrigate_matrix_pie_chart' : 'Questionnaires.aggrigate_pie_chart';
				}
			}
            else if ($question['result_display_type'] == QuestionnairesComponent::RESULT_DISPLAY_TYPE_TABLE) {
            	if (isset($question['isMatrix'])) {
					$element_name = ($question['isMatrix']) ? 'Questionnaires.aggrigate_matrix_table' : 'Questionnaires.aggrigate_table';
				}
			}

			if ($element_name==='') {
				continue; 	//element名が決まらない場合、次へ。
			}
			echo $this->element($element_name,
				array(
					'frameId' => $frameId,
					'questionnaireId' => $questionnaireId,
					'questionnaireEntity' => $questionnaireEntity,
					'question' => $question
				)
			);
			//$this->log('question['.print_r($question,true).']','debug');
		}
	?>

	<div class="text-center">
		<a class="btn btn-default btn-lg" href="/<?php echo $topUrl; ?>" ui-sref="questionnaires" target="_self"><?php echo __d('questionnaires', 'Back to top'); ?></a>
    </div>
</section>
