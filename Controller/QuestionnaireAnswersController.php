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
		'Questionnaires.QuestionnairePage',
		'Questionnaires.QuestionnaireAnswerSummary',
		'Questionnaires.QuestionnaireAnswer',
		'Questionnaires.QuestionnaireFrameSetting',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.Permission',
		'Questionnaires.Questionnaires',
		'Questionnaires.QuestionnairesOwnAnswer',
		'AuthorizationKeys.AuthorizationKey' => array(
			'operationType' => 'embed',
			'targetAction' => 'view',
			'model' => 'Questionnaire',
			'contentId' => 0),
		'VisualCaptcha.VisualCaptcha' => array(
			'operationType' => 'embed',
			'targetAction' => 'view'),
	);

/**
 * use helpers
 *
 */
	public $helpers = [
		'NetCommons.Date',
		'NetCommons.TitleIcon',
		'Workflow.Workflow',
		'Questionnaires.QuestionnaireAnswer',
	];

/**
 * target questionnaire data
 *
 */
	private $__questionnaire = null;

/**
 * target isAbleToAnswer Action
 *
 */
	private $__ableToAnswerAction = ['view', 'confirm'];

/**
 * frame setting display type
 */
	private $__displayType = null;

/**
 * beforeFilter
 * NetCommonsお約束：できることならControllerのbeforeFilterで実行可/不可の判定して流れを変える
 *
 * @return void
 */
	public function beforeFilter() {
		// ゲストアクセスOKのアクションを設定
		$this->Auth->allow(
			'view', 'confirm', 'thanks', 'not_found_answer', 'no_more_answer', 'key_auth', 'img_auth'
		);

		// 親クラスのbeforeFilterを済ませる
		parent::beforeFilter();

		// 現在の表示形態を調べておく
		list($this->__displayType) = $this->QuestionnaireFrameSetting->getQuestionnaireFrameSetting(
			Current::read('Frame.key')
		);

		// NetCommonsお約束：編集画面へのURLに編集対象のコンテンツキーが含まれている
		// まずは、そのキーを取り出す
		// アンケートキー
		$questionnaireKey = $this->_getQuestionnaireKeyFromPass();

		// キーで指定されたアンケートデータを取り出しておく
		$conditions = $this->Questionnaire->getWorkflowConditions(
			array('Questionnaire.key' => $questionnaireKey)
		);

		$this->__questionnaire = $this->Questionnaire->find('first', array(
			'conditions' => $conditions,
			'recursive' => 1
		));
		if (! $this->__questionnaire) {
			$this->setAction('not_found_answer');
			return;
		}

		// 以下のisAbleto..の内部関数にてNetCommonsお約束である編集権限、参照権限チェックを済ませています
		// 閲覧可能か
		if (!$this->isAbleTo($this->__questionnaire)) {
			// 不可能な時は「回答できません」画面を出すだけ
			$this->setAction('no_more_answer');
			return;
		}
		if (in_array($this->action, $this->__ableToAnswerAction)) {
			// 回答可能か
			if (!$this->isAbleToAnswer($this->__questionnaire)) {
				// 回答が不可能な時は「回答できません」画面を出すだけ
				$this->setAction('no_more_answer');
				return;
			}
		}
	}

/**
 * test_mode
 *
 * テストモード回答のとき、一番最初に表示するページ
 * 一覧表示画面で「テスト」ボタンがここへ誘導するようになっている。
 * どのようなアンケートであるのかの各種属性設定をわかりやすくまとめて表示する表紙的な役割を果たす。
 *
 * あくまで作成者の便宜のために表示しているものであるので、最初のページだったら必ずここを表示といったような
 * 強制的redirectなどは設定しない。なので強制URL-Hackしたらこの画面をスキップすることだって可能。
 * 作成者への「便宜」のための親切心ページなのでスキップしたい人にはそうさせてあげるのでよいと考える。
 *
 * @return void
 */
	public function test_mode() {
		$status = $this->__questionnaire['Questionnaire']['status'];
		// テストモード確認画面からのPOSTや、現在のアンケートデータのステータスが公開状態の時
		// 次へリダイレクト
		if ($this->request->is('post') || $status == WorkflowComponent::STATUS_PUBLISHED) {
			$this->_redirectAnswerPage();
			return;
		}
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
		$this->set('questionnaire', $this->__questionnaire);
	}

/**
 * _viewGuard
 *
 * アンケートが認証キーや画像認証でガードされているかどうかを調べ、
 * ガードがある場合は適宜、相当のアクションへ転送する
 *
 * @return void
 */
	protected function _viewGuard() {
		$questionnaireKey = $this->_getQuestionnaireKey($this->__questionnaire);

		$quest = $this->__questionnaire['Questionnaire'];

		if (!$this->Session->check('Questionnaire.auth_ok.' . $questionnaireKey)) {
			if ($this->request->is('get') ||
				!isset($this->request->data['QuestionnairePage']['page_sequence'])) {
				// 認証キーコンポーネントお約束：
				if ($quest['is_key_pass_use'] == QuestionnairesComponent::USES_USE) {
					$this->AuthorizationKey->contentId = $quest['id'];
					$this->AuthorizationKey->guard(
						AuthorizationKeyComponent::OPERATION_EMBEDDING,
						'Questionnaire',
						$this->__questionnaire);
					$this->setAction('key_auth');
					return;
				}
				if ($quest['is_image_authentication'] == QuestionnairesComponent::USES_USE) {
					// 画像認証コンポーネントお約束：
					$this->setAction('img_auth');
					return;
				}
			}
		} else {
			$this->Session->delete('Questionnaire.auth_ok.' . $questionnaireKey);
		}
	}

/**
 * key_auth
 *
 * 認証キーガード
 *
 * @return void
 */
	public function key_auth() {
		$isKeyPassUse = $this->__questionnaire['Questionnaire']['is_key_pass_use'];
		if ($isKeyPassUse != QuestionnairesComponent::USES_USE) {
			$this->_redirectAnswerPage();
			return;
		}
		$qKey = $this->_getQuestionnaireKey($this->__questionnaire);
		if ($this->request->is('post')) {
			if ($this->AuthorizationKey->check()) {
				$this->Session->write('Questionnaire.auth_ok.' . $qKey, 'OK');
				// 画面へ行く
				$url = NetCommonsUrl::actionUrl(array(
					'controller' => 'questionnaire_answers',
					'action' => 'view',
					Current::read('Block.id'),
					$qKey,
					'frame_id' => Current::read('Frame.id'),
				));
				$this->redirect($url);
				return;
			}
		}
		$url = NetCommonsUrl::actionUrl(array(
			'controller' => 'questionnaire_answers',
			'action' => 'key_auth',
			Current::read('Block.id'),
			$qKey,
			'frame_id' => Current::read('Frame.id'),
		));
		$this->set('questionnaire', $this->__questionnaire);
		$this->set('displayType', $this->__displayType);
		$this->set('postUrl', $url);
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
	}

/**
 * img_auth
 *
 * 画像認証ガード
 *
 * @return void
 */
	public function img_auth() {
		$isImgUse = $this->__questionnaire['Questionnaire']['is_image_authentication'];
		if ($isImgUse != QuestionnairesComponent::USES_USE) {
			$this->_redirectAnswerPage();
			return;
		}
		$qKey = $this->_getQuestionnaireKey($this->__questionnaire);
		if ($this->request->is('post')) {
			if ($this->VisualCaptcha->check()) {
				$this->Session->write('Questionnaire.auth_ok.' . $qKey, 'OK');
				// 画面へ行く
				$this->_redirectAnswerPage();
				return;
			}
		}
		$url = NetCommonsUrl::actionUrl(array(
			'controller' => 'questionnaire_answers',
			'action' => 'img_auth',
			Current::read('Block.id'),
			$qKey,
			'frame_id' => Current::read('Frame.id'),
		));
		$this->set('questionnaire', $this->__questionnaire);
		$this->set('displayType', $this->__displayType);
		$this->set('postUrl', $url);
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
	}

/**
 * view method
 * Display the question of the questionnaire , to accept the answer input
 *
 * @return void
 */
	public function view() {
		$questionnaire = $this->__questionnaire;
		$questionnaireKey = $this->_getQuestionnaireKey($this->__questionnaire);

		//
		$this->_viewGuard();

		// 選択肢ランダム表示対応
		$this->__shuffleChoice($questionnaire);

		// ページの指定のない場合はFIRST_PAGE_SEQUENCEをデフォルトとする
		$nextPageSeq = QuestionnairesComponent::FIRST_PAGE_SEQUENCE;	// default

		$postPageSeq = null;
		if (isset($this->data['QuestionnairePage']['page_sequence'])) {
			$postPageSeq = $this->data['QuestionnairePage']['page_sequence'];
		}

		// POSTチェック
		if ($this->request->is('post')) {
			// サマリ情報準備
			$summary = $this->QuestionnairesOwnAnswer->forceGetProgressiveAnswerSummary(
				$this->__questionnaire
			);
			$nextPageSeq = $postPageSeq;

			// 回答データがある場合は回答をDBに書きこむ
			if (isset($this->data['QuestionnaireAnswer'])) {
				if (! $this->QuestionnaireAnswer->saveAnswer($this->data, $questionnaire, $summary)) {
					// 保存エラーの場合は今のページを再表示
					$nextPageSeq = $postPageSeq;
				} else {
					// 回答データがあり、無事保存できたら次ページを取得する
					$nextPageSeq = $this->QuestionnairePage->getNextPage(
						$questionnaire,
						$postPageSeq,
						$this->data['QuestionnaireAnswer']);
				}
			}
			// 次ページはもう存在しない
			if ($nextPageSeq === false) {
				// 確認画面へいってもよい状態ですと書きこむ
				$this->QuestionnaireAnswerSummary->saveAnswerStatus(
					$questionnaire,
					$summary,
					QuestionnairesComponent::ACTION_BEFORE_ACT
				);
				// 確認画面へ
				$url = NetCommonsUrl::actionUrl(array(
					'controller' => 'questionnaire_answers',
					'action' => 'confirm',
					Current::read('Block.id'),
					$questionnaireKey,
					'frame_id' => Current::read('Frame.id'),
				));
				$this->redirect($url);
				return;
			}
		}
		if (! ($this->request->is('post') && $nextPageSeq == $postPageSeq)) {
			$summary = $this->QuestionnairesOwnAnswer->getProgressiveSummaryOfThisUser(
				$questionnaireKey);
			$setAnswers = $this->QuestionnaireAnswer->getProgressiveAnswerOfThisSummary(
				$questionnaire,
				$summary);
			$this->set('answers', $setAnswers);
			$this->request->data['QuestionnaireAnswer'] = $setAnswers;

			// 入力される回答データですがsetで設定するデータとして扱います
			// 誠にCake流儀でなくて申し訳ないのですが、様々な種別のAnswerデータを
			// 特殊な文字列加工して統一化した形状でDBに入れている都合上、このような仕儀になっています
		} else {
			$this->set('answers', $this->request->data['QuestionnaireAnswer']);
		}

		// 質問情報をView変数にセット
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
		$this->request->data['QuestionnairePage'] = $questionnaire['QuestionnairePage'][$nextPageSeq];
		$this->set('questionnaire', $questionnaire);
		$this->set('questionPage', $questionnaire['QuestionnairePage'][$nextPageSeq]);
		$this->set('displayType', $this->__displayType);
		$this->NetCommons->handleValidationError($this->QuestionnaireAnswer->validationErrors);

		//新着データを既読にする
		$this->Questionnaire->saveTopicUserStatus($questionnaire);
	}

/**
 * confirm method
 *
 * @return void
 */
	public function confirm() {
		// 確認してもいいサマリレコード取得
		$summary = $this->QuestionnairesOwnAnswer->getConfirmSummaryOfThisUser(
			$this->_getQuestionnaireKey($this->__questionnaire));
		if (!$summary) {
			$this->setAction('throwBadRequest');
			return;
		}

		// 解答入力画面で表示していたときのシャッフルを取り出す
		$this->__shuffleChoice($this->__questionnaire);

		// POSTチェック
		if ($this->request->is('post')) {
			// サマリの状態を完了にして確定する
			$this->QuestionnaireAnswerSummary->saveAnswerStatus(
				$this->__questionnaire,
				$summary,
				QuestionnairesComponent::ACTION_ACT);
			$this->QuestionnairesOwnAnswer->saveOwnAnsweredKeys(
				$this->_getQuestionnaireKey($this->__questionnaire));

			// ありがとう画面へ行く
			$url = NetCommonsUrl::actionUrl(array(
				'controller' => 'questionnaire_answers',
				'action' => 'thanks',
				Current::read('Block.id'),
				$this->_getQuestionnaireKey($this->__questionnaire),
				'frame_id' => Current::read('Frame.id'),
			));
			$this->redirect($url);
		}

		// 回答情報取得
		// 回答情報並べ替え
		$setAnswers = $this->QuestionnaireAnswer->getProgressiveAnswerOfThisSummary(
			$this->__questionnaire,
			$summary);

		// 質問情報をView変数にセット
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
		$this->set('questionnaire', $this->__questionnaire);
		$this->request->data['QuestionnaireAnswer'] = $setAnswers;
		$this->set('answers', $setAnswers);
		$this->set('displayType', $this->__displayType);
	}

/**
 * thanks method
 *
 * @return void
 */
	public function thanks() {
		$qKey = $this->__questionnaire['Questionnaire']['key'];
		// 回答済みか確認
		if (! $this->QuestionnairesOwnAnswer->checkOwnAnsweredKeys($qKey)) {
			$this->setAction('throwBadRequest');
			return;
		}
		// 後始末
		// 回答中にたまっていたセッションキャッシュをクリア
		$this->Session->delete('Questionnaires.' . $qKey);

		// View変数にセット
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
		$this->set('questionnaire', $this->__questionnaire);
		$this->set('ownAnsweredKeys', $this->QuestionnairesOwnAnswer->getOwnAnsweredKeys());
		$this->set('displayType', $this->__displayType);

		//新着データを回答済みにする
		$this->Questionnaire->saveTopicUserStatus($this->__questionnaire, true);
	}

/**
 * no_more_answer method
 * 条件によって回答できないアンケートにアクセスしたときに表示
 *
 * @return void
 */
	public function no_more_answer() {
		$this->set('questionnaire', $this->__questionnaire);
		$this->set('ownAnsweredKeys', $this->QuestionnairesOwnAnswer->getOwnAnsweredKeys());
		$this->set('displayType', $this->__displayType);
	}
/**
 * not_found_answer method
 * アンケートがみつからないときに表示
 *
 * @return void
 */
	public function not_found_answer() {
		$this->set('displayType', $this->__displayType);
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
					$sessionPath = sprintf(
						'Questionnaires.%s.QuestionnaireQuestion.%s.QuestionnaireChoice',
						$questionnaire['Questionnaire']['key'],
						$q['key']
					);
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
/**
 * _redirectAnswerPage
 *
 * @return void
 */
	protected function _redirectAnswerPage() {
		$this->redirect(NetCommonsUrl::actionUrl(array(
			'controller' => 'questionnaire_answers',
			'action' => 'view',
			Current::read('Block.id'),
			$this->_getQuestionnaireKey($this->__questionnaire),
			'frame_id' => Current::read('Frame.id')
		)));
	}

}
