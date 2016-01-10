<?php
/**
 * QuestionnairesOwnAnswer Component
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Component', 'Controller');

/**
 * QuestionnairesOwnAnswerComponent
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuestionnairesOwnAnswerComponent extends Component {

/**
 * Answered questionnaire keys
 *
 * 回答済みアンケートキー配列
 *
 * @var array
 */
	private $__ownAnsweredKeys = null;

/**
 * 指定されたアンケートに該当する回答中アンケートのサマリを取得する
 *
 * @param string $questionnaireKey 回答済に追加するアンケートキー
 * @return progressive Answer Summary id list
 */
	public function getProgressiveSummaryOfThisUser($questionnaireKey) {
		// 戻り値初期化
		$summary = false;
		$answerSummary = ClassRegistry::init('Questionnaires.QuestionnaireAnswerSummary');
		// 未ログインの人の場合はセッションにある回答中データを参照する
		if (! Current::read('User.id')) {
			$session = $this->_Collection->load('Session');
			$summaryId = $session->read('Questionnaires.progressiveSummary.' . $questionnaireKey);
			if ($summaryId) {
				$summary = $answerSummary->findById($summaryId);
			}
			return $summary;
		}
		// ログインユーザーはDBから探す
		$conditions = array(
			'answer_status' => QuestionnairesComponent::ACTION_NOT_ACT,
			'questionnaire_key' => $questionnaireKey,
			'user_id' => Current::read('User.id'),
		);
		$summary = $answerSummary->find('first', array(
			'conditions' => $conditions,
			'order' => 'QuestionnaireAnswerSummary.created DESC'	// 最も新しいものを一つ選ぶ
		));
		return $summary;
	}
/**
 * 指定されたアンケートに対応する回答中サマリを作成
 *
 * @param array $questionnaire アンケート
 * @return progressive Answer Summary data
 */
	public function forceGetProgressiveAnswerSummary($questionnaire) {
		$summary = $this->getProgressiveSummaryOfThisUser($questionnaire['Questionnaire']['key']);
		if (! $summary) {
			$answerSummary = ClassRegistry::init('Questionnaires.QuestionnaireAnswerSummary');
			$session = $this->_Collection->load('Session');
			$summary = $answerSummary->forceGetProgressiveAnswerSummary($questionnaire, Current::read('User.id'), $session->id());
			if ($summary) {
				$this->saveProgressiveSummaryOfThisUser($questionnaire['Questionnaire']['key'], $summary['QuestionnaireAnswerSummary']['id']);
			}
		}

		return $summary;
	}

/**
 * 指定されたアンケートのサマリIDを回答中サマリIDとしてセッションに記録
 *
 * @param string $questionnaireKey 回答中のアンケートキー
 * @param int $summaryId 回答中のサマリのID
 * @return void
 */
	public function saveProgressiveSummaryOfThisUser($questionnaireKey, $summaryId) {
		$session = $this->_Collection->load('Session');
		$session->write('Questionnaires.progressiveSummary.' . $questionnaireKey, $summaryId);
	}
/**
 * セッションから指定されたアンケートの回答中サマリIDを削除
 *
 * @param string $questionnaireKey アンケートキー
 * @return void
 */
	public function deleteProgressiveSummaryOfThisUser($questionnaireKey) {
		$session = $this->_Collection->load('Session');
		$session->delete('Questionnaires.progressiveSummary.' . $questionnaireKey);
	}

/**
 * 回答済みアンケートリストを取得する
 *
 * @return Answered Questionnaire keys list
 */
	public function getOwnAnsweredKeys() {
		if (isset($this->__ownAnsweredKeys)) {
			return $this->__ownAnsweredKeys;
		}

		$this->__ownAnsweredKeys = array();

		if (! Current::read('User.id')) {
			$session = $this->_Collection->load('Session');
			$blockId = Current::read('Block.id');
			$ownAnsweredKeys = $session->read('Questionnaires.ownAnsweredKeys.' . $blockId);
			if (isset($ownAnsweredKeys)) {
				$this->__ownAnsweredKeys = explode(',', $ownAnsweredKeys);
			}

			return $this->__ownAnsweredKeys;
		}

		$answerSummary = ClassRegistry::init('Questionnaires.QuestionnaireAnswerSummary');
		$conditions = array(
			'user_id' => Current::read('User.id'),
			'answer_status' => QuestionnairesComponent::ACTION_ACT,
			'test_status' => QuestionnairesComponent::TEST_ANSWER_STATUS_PEFORM,
			'answer_number' => 1
		);
		$ownAnsweredKeys = $answerSummary->find(
			'list',
			array(
				'conditions' => $conditions,
				'fields' => array('QuestionnaireAnswerSummary.questionnaire_key'),
				'recursive' => -1
			)
		);
		$this->__ownAnsweredKeys = array_values($ownAnsweredKeys);	// idの使用を防ぐ（いらない？）

		return $this->__ownAnsweredKeys;
	}
/**
 * アンケート回答済みかどうかを返す
 *
 * @param string $questionnaireKey 回答済に追加するアンケートキー
 * @return bool
 */
	public function checkOwnAnsweredKeys($questionnaireKey) {
		// まだ回答済データが初期状態のときはまずは確保
		if ($this->__ownAnsweredKeys === null) {
			$this->getOwnAnsweredKeys();
		}
		if (in_array($questionnaireKey, $this->__ownAnsweredKeys)) {
			return true;
		}
		return false;
	}
/**
 * セッションの回答済みアンケートリストに新しいアンケートを追加する
 *
 * @param string $questionnaireKey 回答済に追加するアンケートキー
 * @return void
 */
	public function saveOwnAnsweredKeys($questionnaireKey) {
		// まだ回答済データが初期状態のときはまずは確保
		if ($this->__ownAnsweredKeys === null) {
			$this->getOwnAnsweredKeys();
		}
		// 回答済みアンケート配列に追加
		$this->__ownAnsweredKeys[] = $questionnaireKey;
		// ログイン状態の人の場合はこれ以上の処理は不要
		if (Current::read('User.id')) {
			return;
		}
		// 未ログインの人の場合はセッションに書いておく
		$session = $this->_Collection->load('Session');
		$blockId = Current::read('Block.id');
		$session->write('Questionnaires.ownAnsweredKeys.' . $blockId, implode(',', $this->__ownAnsweredKeys));

		// 回答中アンケートからは削除しておく
		$this->deleteProgressiveSummaryOfThisUser($questionnaireKey);
	}

}
