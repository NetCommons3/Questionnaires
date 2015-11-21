<?php
/**
 * QuestionnaireValidate Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireAnswerBehavior', 'Questionnaires.Model/Behavior');

/**
 * TextArea Behavior
 *
 * @package  Questionnaires\Questionnaires\Model\Befavior\Answer
 * @author Allcreator <info@allcreator.net>
 */
class QuestionnaireAnswerTextAreaBehavior extends QuestionnaireAnswerBehavior {

/**
 * this answer type
 *
 * @var int
 */
	protected $_myType = QuestionnairesComponent::TYPE_TEXT_AREA;

/**
 * answerMaxLength 回答がアンケートが許す最大長を超えていないかの確認
 *
 * @param object &$model use model
 * @param array $data Validation対象データ
 * @param array $question 回答データに対応する質問
 * @param int $max 最大長
 * @return bool
 */
	public function answerMaxLength(&$model, $data, $question, $max) {
		if ($question['question_type'] != $this->_myType) {
			return true;
		}
		return Validation::maxLength($data['answer_value'], $max);
	}

}