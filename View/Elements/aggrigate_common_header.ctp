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

	<?php
		//各質問ごと集計表示の共通ヘッダー

		//質問文

		if (!empty($question['question_value'])) {
			echo '<h4>'.Sanitize::stripAll($question['question_value']).'</h4>';
		}

		//質問タイプ(選択型用)
		switch ($question['question_type']) {
		case QuestionnairesComponent::TYPE_SELECTION:
			$question_type_str = __d('questionnaire', 'Select one');
			break;
		case QuestionnairesComponent::TYPE_MULTIPLE_SELECTION:
			$question_type_str = __d('questionnaire', 'Select more than one');
			break;
		case QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST:
			$question_type_str = __d('questionnaire', 'Matrix (selection list)');
			break;
		case QuestionnairesComponent::TYPE_MATRIX_MULTIPLE:
			$question_type_str = __d('questionnaire', 'Matrix (multiple)');
			break;
		case QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX:
			$question_type_str = __d('questionnaire', 'List selection');
			break;
		defaut:	
			$question_type_str = __d('questionnaire', 'Not selection');
    	}
		echo '<p>'.$question_type_str.'</p>';

		if (isset($question['answer_total_cnt'])) {
			echo __d('questionnaire','The total number of answers: ') . h($question['answer_total_cnt']) .'</p>';
		}

	?>

