<?php
/**
 * questionnaire aggregate total matrix table view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="col-xs-12">

	<div class="table-responsive">
		<table class="table table-striped table-bordered questionnaire-table-vcenter">
			<thead>
			<tr>
				<th><?php echo __d('questionnaires', 'Item name'); ?></th>
				<?php
				$colIds = array();
				foreach ($question['QuestionnaireChoice'] as $choice) {
					if ($choice['matrix_type'] == QuestionnairesComponent::MATRIX_TYPE_COLUMN) {
						$colIds[] = $choice['key'];		//順番に列のid配列を作る。
						//選択肢の「列」
						echo '<th>' . h($choice['choice_label']) . '</th>';
					}
				}
				?>
				<?php
				//小計が必要かどうか、要確認
				//echo '<th>'.__d('questionnaires', 'Subtotal').'</th>';
				?>
			</tr>
			</thead>
			<tbody>
			<?php foreach($question['QuestionnaireChoice'] as $choice): ?>
			<?php
			if ($choice['matrix_type'] == QuestionnairesComponent::MATRIX_TYPE_COLUMN) {
				continue;	//列の選択肢なら次へ
			}
			//以降、行の選択肢
		?>
			<tr>
				<td>
					<?php echo h($choice['choice_label']); ?>
				</td>
				<?php
				$subtotal = 0;
				foreach ($colIds as $colId) {
					echo '<td>';
				//集計値
				$cnt = (isset($choice['aggregate_total'][$colId])) ? $choice['aggregate_total'][$colId] : '0';
				echo h($cnt);

				echo '&nbsp;&nbsp;&nbsp;&nbsp;';

				//合計回答数に対する割合
				$thePercentage = QuestionnairesComponent::NOT_OPERATION_MARK;
				if (isset($question['answer_total_cnt'])) {
				$percent = round( (intval($cnt) / intval($question['answer_total_cnt'])) * 100, 1, PHP_ROUND_HALF_UP );
				$thePercentage = $percent . ' ' . QuestionnairesComponent::PERCENTAGE_UNIT;
				}
				echo ' (' . $thePercentage . ') ';

				//小計加算...必要になれば、すぐに使えるように。
				$subtotal += intval($cnt);

				echo '</td>';
				}
				?>
				<?php
				//小計が必要かどうか、要確認
				//echo '<td'>.$subtotal.'</td>';
				?>
			</tr>

			<?php endforeach; ?>
			</tbody>

		</table>

	</div>

</div>