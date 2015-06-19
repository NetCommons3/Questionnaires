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
	<p>
			<?php echo __d('questionnaires', 'Note: Which may not be 100% because of rounding of numeric total.'); ?>
	<?php
		//各質問ごと集計表示の共通フッター

		if (QuestionnairesComponent::isMatrixInputType($question['question_type'])) {
			echo '<br />' . __d('questionnaires', 'Note: Matrix if the number in parentheses is a percentage of the total number of responses.');
		}

		if ($question['question_type'] == QuestionnairesComponent::TYPE_MULTIPLE_SELECTION ||
			$question['question_type'] == QuestionnairesComponent::TYPE_MATRIX_MULTIPLE) {

			echo '<br />' . __d('questionnaires', 'Note: If multiple selection is possible, total more than 100% to be.');
		}
		?>
	</p>
</div>
