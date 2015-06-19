<?php
/**
 * QuestionnaireAnswers Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * QuestionnaireAnswersController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuestionnaireAnswersController extends QuestionnairesAppController {

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Questionnaires.Questionnaire',
		'Questionnaires.QuestionnairePage',
		'Questionnaires.QuestionnaireQuestion',
		'Questionnaires.QuestionnaireChoice',
		'Questionnaires.QuestionnaireAnswerSummary',
		'Questionnaires.QuestionnaireAnswer',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.NetCommonsBlock', //Use Questionnaire model
		'NetCommons.NetCommonsFrame',
		'NetCommons.NetCommonsRoomRole',
		'Questionnaires.Questionnaires',
		'Questionnaires.QuestionnairesPreAnswer',
		'NetCommons.NetCommonsVisualCaptcha',
	);

/**
 * use helpers
 *
 */
	public $helpers = array(
		'NetCommons.BackToPage',
		'NetCommons.Token'
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('pre_answer', 'answer', 'confirm', 'captcha', 'captcha_image', 'captcha_audio');
	}

/**
 * preAnswer
 * to confirm the key phrase input and/or CAPTCHA before entering the questionnaire
 *
 * @param int $frameId frame Id
 * @param int $questionnaireId questionnaire Id
 * @throws NotFoundException
 * @throws ForbiddenException
 * @return void
 */
	public function pre_answer($frameId = 0, $questionnaireId = 0) {
		$errors = array();

		$questionnaire = $this->QuestionnairesPreAnswer->guardAnswer($this, $frameId, $questionnaireId);

		// check POST request
		if ($this->request->isPost()) {
			// Check the post data , if correct answer , will be session registration
			if (!$this->QuestionnairesPreAnswer->checkKeyPhrase($this, $questionnaire, $this->data)) {
				$errors['PreAnswer']['key_phrase'][] = __d('questionnaires', 'Invalid key phrase');
			}
			if (!$this->QuestionnairesPreAnswer->checkImageAuth($this, $questionnaire, $this->data)) {
				$errors['PreAnswer']['image_auth'][] = __d('questionnaires', 'Invalid key Image Auth');
			}
		}

		// check that whether the current state is in the state to be a pre-answer
		if (!$this->QuestionnairesPreAnswer->isPreAnswer($this, $questionnaire)) {
			$this->redirect('answer/' . $frameId . '/' . $questionnaireId);
		}

		// もしも表示数を変えたいときは下記１行を有効にして、captcha関数でgenerateに引数を与える
		//$this->set('imageDisplayCount', 10);

		$this->set('questionnaire', $this->camelizeKeyRecursive($questionnaire));
		$this->set('isDuringTest', $this->_isDuringTest($questionnaire));
		$this->set('errors', $this->camelizeKeyRecursive($errors));
	}

/**
 * captcha
 * return to Client captcha data
 *
 * @param int $frameId frame Id
 * @return string
 */
	public function captcha($frameId) {
		$this->autoRender = false;
		echo $this->NetCommonsVisualCaptcha->generate();	// もしも表示数を変えたいときは引数に数値を設定
	}

/**
 * captcha_image
 * return to Client captcha data
 *
 * @param int $frameId frame Id
 * @param int $index captcha image number
 * @return string
 */
	public function captcha_image($frameId, $index) {
		$this->autoRender = false;
		return $this->NetCommonsVisualCaptcha->image($index);
	}

/**
 * captcha_audio
 * return to Client captcha audio data
 *
 * @param int $frameId frame Id
 * @return string
 */
	public function captcha_audio($frameId) {
		$this->autoRender = false;
		echo $this->NetCommonsVisualCaptcha->audio();
	}

/**
 * answer method
 * Display the question of the questionnaire , to accept the answer input
 *
 * @param int $frameId frame Id
 * @param int $questionnaireId questionnaire Id
 * @throws NotFoundException
 * @throws ForbiddenException
 * @return void
 */
	public function answer($frameId = 0, $questionnaireId = 0) {
		$errors = array();
		$userId = $this->Auth->user('id');

		// アンケート回答可否チェックとアンケート情報の取り出し
		$questionnaire = $this->QuestionnairesPreAnswer->guardAnswer($this, $frameId, $questionnaireId);
		if (!$questionnaire) {
			$this->view = 'QuestionnaireAnswers/noMoreAnswer';
			return;
		}

		// プレ回答をするべき状態にあるか
		if ($this->QuestionnairesPreAnswer->isPreAnswer($this, $questionnaire)) {
			//$this->redirect('pre_answer/' . $frameId . '/' . $questionnaireId);
			$this->setAction('pre_answer', $frameId, $questionnaireId);
			return;
		}

		// 選択肢ランダム表示対応
		$this->__shuffleChoice($questionnaire);

		// ページの指定のない場合はFIRST_PAGE_SEQUENCEをデフォルトとする
		$nextPageSeq = QuestionnairesComponent::FIRST_PAGE_SEQUENCE;	// default

		// POSTチェック
		if ($this->request->isPost()) {
			// 回答データがある場合は回答をDBに書きこむ
			if (isset($this->data['QuestionnaireAnswer'])) {
				// 次に表示するべきページのシーケンス番号を取得する
				$nextPageSeq = $this->data['QuestionnairePage']['page_sequence'] + 1;

				$ret = $this->QuestionnaireAnswer->saveAnswer($questionnaire, $userId, $this->Session->id(), $this->data['QuestionnaireAnswer'], $errors);
				if ($ret == false) {
					// 保存エラーの場合は今のページを再表示
					$nextPageSeq = $this->data['QuestionnairePage']['page_sequence'];
				} else {
					// 回答データがあり、無事保存し、かつ、スキップロジックにHITしていたらページを変更する
					$nextPageSeq = $this->__checkSkipPage($this->data['QuestionnaireAnswer'], $questionnaire, $nextPageSeq);
				}
			}
		}

		// 指定ページはすでに存在するページを超える場合ー＞確認画面へ
		// スキップで「最後へ」と指示されている場合ー＞確認画面へ
		if ($this->__checkEndPage($questionnaire, $nextPageSeq)) {
			$this->redirect('confirm/' . $this->viewVars['frameId'] . '/' . $questionnaireId);
		}

		// 次のページが普通に存在する場合
		// （すでに回答している場合もあるので、回答も合わせて取り出すこと）
		if (count($errors) == 0) {
			$summary = $this->QuestionnaireAnswerSummary->getProgressiveSummaryOfThisUser($questionnaireId, $userId, $this->Session->id());
			$setAnswers = $this->QuestionnaireAnswerSummary->getProgressiveAnswerOfThisSummary($summary);
			$this->set('answers', $setAnswers);
		}
		//$this->log(print_r($setAnswers, true), 'debug');
		// 質問情報をView変数にセット
		$this->set('questionnaire', $questionnaire);
		$this->set('isDuringTest', $this->_isDuringTest($questionnaire));
		$this->set('questionPage', $questionnaire['QuestionnairePage'][$nextPageSeq]);
		$this->set('errors', $errors);
	}

/**
 * confirm method
 *
 * @param int $frameId フレームID
 * @param int $questionnaireId アンケートID
 * @return void
 * @throws NotFoundException
 */
	public function confirm($frameId = 0, $questionnaireId = 0) {
		$questionnaire = $this->QuestionnairesPreAnswer->guardAnswer($this, $frameId, $questionnaireId);

		$this->__shuffleChoice($questionnaire);

		// 回答中サマリレコード取得
		$summary = $this->QuestionnaireAnswerSummary->getProgressiveSummaryOfThisUser(
			$questionnaireId,
			$this->Auth->user('id'),
			$this->Session->id());
		if (!$summary) {
			throw new NotFoundException(__d('questionnaires', 'Invalid answers'));
		}

		// POSTチェック
		if ($this->request->isPost()) {
			// サマリの状態を完了にして確定する
			$summary['QuestionnaireAnswerSummary']['answer_status'] = QuestionnairesComponent::ACTION_ACT;
			$summary['QuestionnaireAnswerSummary']['answer_time'] = $this->_getNowTime();
			$this->QuestionnaireAnswerSummary->save($summary['QuestionnaireAnswerSummary']);

			// ありがとう画面へ行く
			$this->redirect('../questionnaires/thanks/' . $this->viewVars['frameId'] . '/' . $questionnaireId);
		}

		// 回答情報取得
		// 回答情報並べ替え
		$setAnswers = array();
		$setAnswers = $this->QuestionnaireAnswerSummary->getProgressiveAnswerOfThisSummary($summary);

		// 質問情報をView変数にセット
		$this->set('questionnaireId', $questionnaireId);
		$this->set('questionnaire', $questionnaire);
		$this->set('isDuringTest', $this->_isDuringTest($questionnaire));
		$this->set('answers', $setAnswers);
	}

/**
 * __checkSkipPage
 * check skip page method
 * Check whether there is the one specified by the skip logic answers , and returns the page number if there is destination
 * The false when there is nothing
 *
 * @param array $answers Answer
 * @param array $questionnaire Questionnaire
 * @param int $nextPageSeq default next page sequence
 * @return int next page sequence
 */
	private function __checkSkipPage($answers, $questionnaire, $nextPageSeq) {
		// 回答にスキップロジックで指定されたものがないかチェックし、行き先があるならそのページ番号を返す
		foreach ($answers as $answer) {
			$targetQuestion = Hash::extract($questionnaire['QuestionnairePage'], '{n}.QuestionnaireQuestion.{n}[origin_id=' . $answer['questionnaire_question_origin_id'] . ']');
			if ($targetQuestion) {
				$q = $targetQuestion[0];
				// skipロジック対象の質問ならば次ページのチェックを行う
				if ($q['is_skip'] == QuestionnairesComponent::SKIP_FLAGS_SKIP) {
					$choiceIds = explode(QuestionnairesComponent::ANSWER_VALUE_DELIMITER,
									trim($answer['answer_value'], QuestionnairesComponent::ANSWER_DELIMITER));
					$choice = Hash::extract($q['QuestionnaireChoice'], '{n}[origin_id=' . $choiceIds[0] . ']');
					if ($choice) {
						$c = $choice[0];
						return empty($c['skip_page_sequence']) ? $nextPageSeq : $c['skip_page_sequence'];
					} else {
						return $nextPageSeq; // 指定された次ページを返す
					}
				}
			}
		}
		// スキップロジック質問がない場合は元関数から言われた次ページをそのまま返す
		return $nextPageSeq;
	}

/**
 * _checkEndPage
 * 指定された次ページはすでにアンケートの最後になるか
 *
 * @param array $questionnaire アンケート情報
 * @param int $nextPageSeq 指定次ページ
 * @return bool
 */
	private function __checkEndPage($questionnaire, $nextPageSeq) {
		$this->log($nextPageSeq, 'debug');
		if ($nextPageSeq == QuestionnairesComponent::SKIP_GO_TO_END) {
			return true;
		}

		// ページ情報がない？終わりにする
		if (!isset($questionnaire['QuestionnairePage'])) {
			return true;
		}

		// ページ配列はページのシーケンス番号順に取り出されているので
		$pages = $questionnaire['QuestionnairePage'];
		$endPage = end($pages);
		if ($endPage['page_sequence'] < $nextPageSeq) {
			return true;
		}
		return false;
	}

/**
 * _shuffleChoice
 * shuffled choices and write into session
 *
 * @param array &$questionnaire アンケート
 * @return void
 */
	private function __shuffleChoice(&$questionnaire) {
		foreach ($questionnaire['QuestionnairePage'] as &$page) {
			foreach ($page['QuestionnaireQuestion'] as &$q) {
				$choices = $q['QuestionnaireChoice'];
				if ($q['is_choice_random'] == QuestionnairesComponent::USES_USE) {
					$sessionPath = 'Questionnaires.' . $questionnaire['Questionnaire']['origin_id'] . '.QuestionnaireQuestion.' . $q['origin_id'] . '.QuestionnaireChoice';
					if ($this->Session->check($sessionPath)) {
						$choices = $this->Session->read($sessionPath);
					} else {
						shuffle($choices);
						$this->Session->write($sessionPath, $choices);
					}
				}
				$q['QuestionnaireChoice'] = $choices;
			}
		}
	}
}