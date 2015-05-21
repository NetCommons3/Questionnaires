<?php
/**
 * Questionnaires PreAnswer Component
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Component', 'Controller');

class QuestionnairesPreAnswerComponent extends Component {

/**
 * guardAnswer
 *
 * @param Controller $controller controller(QuestionnaireAnswerController)
 * @param int $frameId frame id
 * @param int $questionnaireId questionnaire id
 * @return mixed
 * @throws ForbiddenException
 * @throws NotFoundException
 */
	public function guardAnswer($controller, $frameId, $questionnaireId) {
		$Questionnaire = ClassRegistry::init('Questionnaire');

		// get conditions for finding specified Questionnaire
		$conditions = $controller->getConditionForAnswer(array('origin_id' => $questionnaireId));

		// get the specified questionnaire
		$questionnaire = $Questionnaire->find('first', array(
			'conditions' => $conditions,
		));
		if (!$questionnaire) {
			throw new NotFoundException(__d('questionnaires', 'Invalid questionnaire'));
		}

		// Guard Force URL hack
		if (!$this->__isAbleToAnswer($controller, $questionnaire)) {
			throw new ForbiddenException(__d('net_commons', 'Permission denied'));
		}
		/*
		if ($controller->request->params['action'] != 'pre_answer') {
			if (!$this->isPreAnswer($controller, $questionnaire)) {
				//$controller->redirect('pre_answer/' . $frameId . '/' . $questionnaireId);
				return false;
			}
		}
		*/
		return $questionnaire;
	}

/**
 * isAbleToAnswer 指定されたIDに回答できるかどうか
 * 強制URLハックのガード
 * 指定のアンケートの状態と回答者の権限を照らし合わせてガードをかける
 * 公開状態にない
 * 期間外
 * 停止中
 * 繰り返し回答
 * 会員以外には許してないのに未ログインである
 *
 * @param Controller $controller controller(QuestionnaireAnswerController)
 * @param array $questionnaire 対象となるアンケートデータ
 * @return bool
 */
	private function __isAbleToAnswer($controller, $questionnaire) {
		$answerSummary = ClassRegistry::init('QuestionnaireAnswerSummary');

		if (!$controller->isAbleTo($questionnaire)) {
			return false;
		}
		// 繰り返し回答を許していないのにすでに回答済みか
		if ($questionnaire['Questionnaire']['is_repeat_allow'] == QuestionnairesComponent::PERMISSION_NOT_PERMIT) {
			$summary = $answerSummary->getNowSummaryOfThisUser(
				$questionnaire['Questionnaire']['origin_id'],
				$controller->Auth->user('id'),
				$controller->Session->id());
			if ($summary) {
				return false;
			}
		}

		return true;
	}

/**
 * isPreAnswer
 * Whether the state should be a pre- answer
 *
 * @param Controller $controller controller(QuestionnaireAnswerController)
 * @param array $questionnaire Questionnaire
 * @return bool
 */
	public function isPreAnswer($controller, $questionnaire) {
		// プレ回答をすべき状態かチェックする
		// 表示すべきページ番号がFIRST_PAGE_SEQUENCEで
		// かつパスフレーズや画像認証が要求されており、
		// かつ、それらがまだ認証されていない場合
		// ０ページ目としてそれらを表示する
		if (($questionnaire['Questionnaire']['is_key_pass_use'] == QuestionnairesComponent::USES_USE
			|| $questionnaire['Questionnaire']['is_image_authentication'] == QuestionnairesComponent::USES_USE)) {
			$checkKeyPhrase = true;
			if ($questionnaire['Questionnaire']['is_key_pass_use'] == QuestionnairesComponent::USES_USE) {
				$checkKeyPhrase = $this->__checkPreAnswer($controller, $questionnaire['Questionnaire']['origin_id'], 'key_phrase');
			}

			$checkImageAuth = true;
			if ($questionnaire['Questionnaire']['is_image_authentication'] == QuestionnairesComponent::USES_USE) {
				$checkImageAuth = $this->__checkPreAnswer($controller, $questionnaire['Questionnaire']['origin_id'], 'image_auth');
			}

			if (!$checkKeyPhrase || !$checkImageAuth) {
				return true;
			}
		}
		return false;
	}

/**
 * checkKeyPhrase
 * Enter passphrase confirmation of when the questionnaire before the start of the input of the key phrase is sought
 * Operator it is recorded in the session you answered the correct answer
 *
 * @param object $controller controller
 * @param array $questionnaire アンケート情報
 * @param array $data 入力データ
 * @return bool
 */
	public function checkKeyPhrase($controller, $questionnaire, $data) {
		// アンケート開始前にキーフレーズの入力が求められている場合の入力パスフレーズ確認
		// あっていたらセッションに書きこむ
		if ($questionnaire['Questionnaire']['is_key_pass_use'] != QuestionnairesComponent::USES_USE) {
			return true;
		}
		if ($questionnaire['Questionnaire']['key_phrase'] == $data['PreAnswer']['key_phrase']) {
			$controller->Session->write('Questionnaires.' . $questionnaire['Questionnaire']['origin_id'] . '.key_phrase', true);
			return true;
		}
		return false;
	}

/**
 * checkImageAuth
 * Input confirmation of the case that input of image authentication is required before the start of the questionnaire
 * Operator it is recorded in the session you answered the correct answer
 *
 * @param object $controller controller
 * @param array $questionnaire Questionnaire
 * @param array $data post data
 * @return bool
 */
	public function checkImageAuth($controller, $questionnaire, $data) {
		// アンケート開始前に画像認証の入力が求められている場合の入力確認
		// あっていたらセッションに書きこむ
		if ($questionnaire['Questionnaire']['is_image_authentication'] != QuestionnairesComponent::USES_USE) {
			return true;
		}
		if ($controller->VisualCaptcha->check()) {
			$controller->Session->write('Questionnaires.' . $questionnaire['Questionnaire']['origin_id'] . '.image_auth', true);
			return true;
		}
		return false;
	}

/**
 * _checkPreAnswer
 * アンケート開始前にキーフレーズの入力や画像認証が求められている場合
 * それらの回答がすんでいるかどうかをチェックする
 *
 * @param Controller $controller controller(QuestionnaireAnswerController)
 * @param int $questionnaireId アンケートID
 * @param string $checkType キーフレーズ　もしくは　画像認証
 * @return bool
 */
	private function __checkPreAnswer($controller, $questionnaireId, $checkType) {
		$check = $controller->Session->check('Questionnaires.' . $questionnaireId . '.' . $checkType);
		if ($check) {
			return true;
		}
		return false;
	}

}