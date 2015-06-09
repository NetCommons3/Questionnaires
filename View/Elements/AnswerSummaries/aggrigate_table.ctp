<?php
/**
 * questionnaire aggrigate total table view template
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
		<table class="table table-striped table-bordered questionnaire-table-vcenter table-responsive">
			<thead>
				<tr>
					<th><?php echo __d('questionnaires', 'Item name'); ?></th>
					<th><?php echo __d('questionnaires', 'Aggrigate value'); ?></th>
					<th><?php echo __d('questionnaires', 'The percentage'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($question['QuestionnaireChoice'] as $choice): ?>
				<tr>
					<td>
						<?php echo h($choice['choice_label']); ?>
					</td>
					<td>
						<?php
							$cnt = (isset($choice['aggrigate_total']['aggrigate_not_matrix'])) ? $choice['aggrigate_total']['aggrigate_not_matrix'] : '0';
							echo h($cnt);
						?>
					</td>
					<td>
						<?php
							$thePercentage = QuestionnairesComponent::NOT_OPERATION_MARK;
							if (isset($question['answer_total_cnt'])) {
								$percent = round( (intval($cnt) / intval($question['answer_total_cnt'])) * 100, 1, PHP_ROUND_HALF_UP );
								$thePercentage = $percent . ' ' . QuestionnairesComponent::PERCENTAGE_UNIT;
							}
							echo $thePercentage;
						?>
					</td>
				</tr>

			<?php endforeach; ?>
			</tbody>

		</table>
	</div>
</div>