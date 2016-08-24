<?php
/**
 * questionnaire aggregate total table view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="col-xs-12">

	<?php if (!empty($question['question_value'])): ?>
		<h2>
			<?php echo $question['question_value']; ?>
		</h2>
	<?php endif; ?>
	<p>
		<?php echo __d('questionnaires', 'The total number of answers: ') . h($question['answer_total_cnt']); ?>

		<?php
			//各質問ごと集計表示の共通ヘッダー
			//質問タイプ(選択型用)
			$questionTypeStr = $questionTypeOptions[$question['question_type']];
		?>
		<small>(<?php echo $questionTypeStr; ?>)</small>

	</p>

</div>
