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

	<?php if (!empty($question['question_value'])): ?>
	<h2>
		<?php echo Sanitize::stripAll($question['question_value']); ?>
	</h2>
	<?php endif; ?>
	<p>
		<?php echo __d('questionnaires', 'The total number of answers: ') . h($question['answer_total_cnt']); ?>

	<?php
		//各質問ごと集計表示の共通ヘッダー
		$questionTypeStr = '';
		//質問タイプ(選択型用)
		switch ($question['question_type']) {
		case QuestionnairesComponent::TYPE_SELECTION:
			$questionTypeStr = __d('questionnaires', 'Select one');
			break;
		case QuestionnairesComponent::TYPE_MULTIPLE_SELECTION:
			$questionTypeStr = __d('questionnaires', 'Select more than one');
			break;
		case QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST:
			$questionTypeStr = __d('questionnaires', 'Matrix (selection list)');
			break;
		case QuestionnairesComponent::TYPE_MATRIX_MULTIPLE:
			$questionTypeStr = __d('questionnaires', 'Matrix (multiple)');
			break;
		case QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX:
			$questionTypeStr = __d('questionnaires', 'List selection');
			break;
		default:
			$questionTypeStr = __d('questionnaires', 'Not selection');
		}
	?>
	<small>(<?php echo $questionTypeStr; ?>)</small>

	</p>

</div>
