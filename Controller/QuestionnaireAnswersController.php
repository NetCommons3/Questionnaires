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
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.Permission',
		'Questionnaires.Questionnaires',
		'AuthorizationKeys.AuthorizationKey' => array(
			'operationType' => 'none',
			'targetAction' => 'view',
			'model' => 'Questionnaire',
			'contentId' => 0),
		'VisualCaptcha.VisualCaptcha' => array(
			'operationType' => 'none',
			'targetAction' => 'view'),
	);

/**
 * use helpers
 *
 */
	public $helpers = [
		'NetCommons.BackToPage',
		'NetCommons.Date',
		'Workflow.Workflow',
		'Questionnaires.QuestionnaireAnswer'
	];

/**
 * target questionnaire data
 *
 */
	private $__questionnaire = null;

/**
 * beforeFilter
 * NetCommonsお約束：できることならControllerのbeforeFilterで実行可/不可の判定して流れを変える
 *
 * @return void
 */
	public function beforeFilter() {
		// ゲストアクセスOKのアクションを設定
		$this->Auth->allow('view', 'confirm', 'thanks');

		// 親クラスのbeforeFilterを済ませる
		parent::beforeFilter();

		// NetCommonsお約束：編集画面へのURLに編集対象のコンテンツキーが含まれている
		// まずは、そのキーを取り出す
		// アンケートキー
		if (isset($this->params['pass'][QuestionnairesComponent::QUESTIONNAIRE_KEY_PASS_INDEX])) {
			$questionnaireKey = $this->params['pass'][QuestionnairesComponent::QUESTIONNAIRE_KEY_PASS_INDEX];
		} else {
			$this->setAction('throwBadRequest');
			return;
		}

		// キーで指定されたアンケートデータを取り出しておく
		$conditions = $this->Questionnaire->getBaseCondition(
			array('Questionnaire.key' => $questionnaireKey)
		);
		$this->__questionnaire = $this->Questionnaire->find('first', array(
			'conditions' => $conditions,
		));
		if (! $this->__questionnaire) {
			$this->setAction('throwBadRequest');
			return;
		}

		// 以下のisAbleto..の内部関数にてNetCommonsお約束である編集権限、参照権限チェックを済ませています
		// 閲覧可能か
		if (!$this->isAbleTo($this->__questionnaire)) {
			//$this->setAction('throwBadRequest');
			// 不可能な時は「回答できません」画面を出すだけ
			$this->view = 'no_more_answer';
			return;
		}
		// 回答可能か
		if (!$this->isAbleToAnswer($this->__questionnaire)) {
			//$this->setAction('throwBadRequest');
			// 回答が不可能な時は「回答できません」画面を出すだけ
			$this->view = 'no_more_answer';
			return;
		}
		// 回答の初めのページであることが各種認証行う条件
		if (!$this->request->isPost() || !isset($this->request->data['QuestionnairePage']['page_sequence'])) {
			// 認証キーコンポーネントお約束：
			// 取り出したアンケートが認証キー確認を求めているなら、operationTypeをすり替える
			if ($this->__questionnaire['Questionnaire']['is_key_pass_use'] == QuestionnairesComponent::USES_USE) {
				$this->AuthorizationKey->operationType = 'redirect';
				$this->AuthorizationKey->contentId = $this->__questionnaire['Questionnaire']['id'];
			}
			// 画像認証コンポーネントお約束：
			// 取り出したアンケートが画像認証ありならば、operationTypeをすり替える
			if ($this->__questionnaire['Questionnaire']['is_image_authentication'] == QuestionnairesComponent::USES_USE) {
				$this->VisualCaptcha->operationType = 'redirect';
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
		if ($this->request->isPost() || $status == WorkflowComponent::STATUS_PUBLISHED) {
			$this->redirect(NetCommonsUrl::actionUrl(array(
				'controller' => 'questionnaire_answers',
				'action' => 'view',
				Current::read('Block.id'),
				$this->_getQuestionnaireKey($this->__questionnaire),
				'frame_id' => Current::read('Frame.id')
			)));
			return;
		}
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
		$this->set('questionnaire', $this->__questionnaire);
	}

/**
 * view method
 * Display the question of the questionnaire , to accept the answer input
 *
 * @return void
 */
	public function view() {
		$userId = Current::read('User.id');

		$questionnaire = $this->__questionnaire;
		$questionnaireKey = $this->_getQuestionnaireKey($this->__questionnaire);

		// 選択肢ランダム表示対応
		$this->__shuffleChoice($questionnaire);

		// ページの指定のない場合はFIRST_PAGE_SEQUENCEをデフォルトとする
		$nextPageSeq = QuestionnairesComponent::FIRST_PAGE_SEQUENCE;	// default

		// POSTチェック
		if ($this->request->isPost()) {
			// 回答データがある場合は回答をDBに書きこむ
			if (isset($this->data['QuestionnaireAnswer'])) {
				if (! $this->QuestionnaireAnswer->saveAnswer($this->data, $questionnaire, $userId, $this->Session->id())) {
					// 保存エラーの場合は今のページを再表示
					$nextPageSeq = $this->data['QuestionnairePage']['page_sequence'];
				} else {
					// 回答データがあり、無事保存できたら次ページを取得する
					$nextPageSeq = $this->QuestionnairePage->getNextPage(
						$questionnaire,
						$this->data['QuestionnairePage']['page_sequence'],
						$this->data['QuestionnaireAnswer']);
				}
			}
			// 次ページはもう存在しない
			if ($nextPageSeq === false) {
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
		} else {
			$summary = $this->QuestionnaireAnswerSummary->getProgressiveSummaryOfThisUser($questionnaireKey, $userId, $this->Session->id());
			$setAnswers = $this->QuestionnaireAnswer->getProgressiveAnswerOfThisSummary($summary);
			$this->set('answers', $setAnswers);
			$this->request->data['QuestionnaireAnswer'] = $setAnswers;

			// 入力される回答データですがsetで設定するデータとして扱います
			// 誠にCake流儀でなくて申し訳ないのですが、様々な種別のAnswerデータを
			// 特殊な文字列加工して統一化した形状でDBに入れている都合上、このような仕儀になっています
		}

		// 質問情報をView変数にセット
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
		$this->request->data['QuestionnairePage'] = $questionnaire['QuestionnairePage'][$nextPageSeq];
		$this->set('questionnaire', $questionnaire);
		$this->set('questionPage', $questionnaire['QuestionnairePage'][$nextPageSeq]);
		$this->NetCommons->handleValidationError($this->QuestionnaireAnswer->validationErrors);
	}
/**
 * confirm method
 *
 * @return void
 */
	public function confirm() {
		// 解答入力画面で表示していたときのシャッフルを取り出す
		$this->__shuffleChoice($this->__questionnaire);

		// 回答中サマリレコード取得
		$summary = $this->QuestionnaireAnswerSummary->getProgressiveSummaryOfThisUser(
			$this->_getQuestionnaireKey($this->__questionnaire),
			$this->Auth->user('id'),
			$this->Session->id());
		if (!$summary) {
			$this->setAction('throwBadRequest');
			return;
		}

		// POSTチェック
		if ($this->request->isPost()) {
			// サマリの状態を完了にして確定する
			$summary['QuestionnaireAnswerSummary']['answer_status'] = QuestionnairesComponent::ACTION_ACT;
			$summary['QuestionnaireAnswerSummary']['answer_time'] = $this->getNowTime();
			$this->QuestionnaireAnswerSummary->save($summary['QuestionnaireAnswerSummary']);

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
		$setAnswers = $this->QuestionnaireAnswer->getProgressiveAnswerOfThisSummary($summary);

		// 質問情報をView変数にセット
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
		$this->set('questionnaire', $this->__questionnaire);
		$this->request->data['QuestionnaireAnswer'] = $setAnswers;
		$this->set('answers', $setAnswers);
	}
/**
 * thanks method
 *
 * @return void
 */
	public function thanks() {
		// 後始末
		// 回答中にたまっていたセッションキャッシュをクリア
		$this->Session->delete('Questionnaires.' . $this->__questionnaire['Questionnaire']['key']);

		// View変数にセット
		$this->request->data['Frame'] = Current::read('Frame');
		$this->request->data['Block'] = Current::read('Block');
		$this->set('questionnaire', $this->__questionnaire);
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
					$sessionPath = 'Questionnaires.' . $questionnaire['Questionnaire']['key'] . '.QuestionnaireQuestion.' . $q['key'] . '.QuestionnaireChoice';
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