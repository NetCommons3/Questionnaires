<?php
/**
 * Questionnaires Controller
 *
 * @property PaginatorComponent $Paginator
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * QuestionnairesController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuestionnairesController extends QuestionnairesAppController {

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
		'Comments.Comment',
		'Questionnaires.QuestionnaireFrameDisplayQuestionnaire',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.NetCommonsWorkflow',
		'NetCommons.NetCommonsBlock', //Use Questionnaire model
		'NetCommons.NetCommonsFrame',
		'NetCommons.NetCommonsRoomRole' => array(
			//コンテンツの権限設定
			'allowedActions' => array(
				'contentEditable' => array('add', 'edit', 'delete')
			),
		),
		'Questionnaires.Questionnaires',
		'Paginator'
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.Token',
		'NetCommons.Date',
		'NetCommons.BackToPage',
		'Questionnaires.QuestionnaireStatusLabel',
		'Questionnaires.QuestionnaireUtil'
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('thanks');
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		// 表示方法設定値取得
		list($displayType, $displayNum, $sort, $dir) =
			$this->QuestionnaireFrameSetting->getQuestionnaireFrameSetting($this->viewVars['frameKey']);

		// 条件設定値取得
		$conditions = $this->getCondition();

		// 単独表示が指定されていた場合
		if ($displayType == QuestionnairesComponent::DISPLAY_TYPE_SINGLE) {
			$displayQ = $this->QuestionnaireFrameDisplayQuestionnaire->find('first', array(
				'frame_key' => $this->viewVars['frameKey'],
			));
			if (!$displayQ) {
				$this->view = 'QuestionnaireAnswers/noMoreAnswer';
				return;
			}
			$questionnaires = $this->Questionnaire->getQuestionnairesList(
				$conditions,
				$this->Session->id(),
				$this->Auth->user('id'),
				array('origin_id' => $displayQ['QuestionnaireFrameDisplayQuestionnaire']['questionnaire_origin_id']));
			if (!$questionnaires) {
				$this->view = 'Questionnaires/noQuestionnaire';
				return;
			}
			//$this->redirect('questionnaire_answers/answer/' . $this->viewVars['frameId'] . '/' . $questionnaires[0]['Questionnaire']['origin_id']);
			$ret = $this->requestAction('/questionnaires/questionnaire_answers/answer/' . $this->viewVars['frameId'] . '/' . $questionnaires[0]['Questionnaire']['origin_id'], array('return'));
			$this->set('answer', $ret);
			$this->view = 'Questionnaires/answer';
			return;
		}

		// データ取得
		// Modelの方ではカスタムfindメソッドを装備している
		// ここではtype属性を指定することでカスタムFindを呼び出すように指示している
		try {
			$subQuery = $this->Questionnaire->getQuestionnairesCommonForAnswer($this->Session->id(), $this->Auth->user('id'));
			$this->paginate = array(
				'conditions' => $conditions,
				'page' => 1,
				'sort' => $sort,
				'limit' => $displayNum,
				'direction' => $dir,
				'recursive' => 0,
				'joins' => $subQuery,
				'fields' => array(
					'Block.*',
					'Questionnaire.*',
					'CreatedUser.*',
					'ModifiedUser.*',
					'CountAnswerSummary.*'
				)
			);
			$questionnaire = $this->paginate('Questionnaire');
		} catch (NotFoundException $e) {
			// NotFoundの例外
			// アンケートデータが存在しないこととする
			$questionnaire = array();
		}
		$this->set('questionnaires', $questionnaire);

		if (count($questionnaire) == 0) {
			$this->view = 'Questionnaires/noQuestionnaire';
		}
	}

/**
 * thanks method
 *
 * @param int $frameId フレームID
 * @param int $questionnaireId アンケートID
 * @throws NotFoundException
 * @throws ForbiddenException
 * @return void
 */
	public function thanks($frameId = 0, $questionnaireId = 0) {
		// 指定されたアンケート情報を取り出す
		$conditions = $this->getConditionForAnswer(array('origin_id' => $questionnaireId));
		$questionnaire = $this->Questionnaire->find('first', array(
			'conditions' => $conditions
		));
		if (!$questionnaire) {
			throw new NotFoundException(__d('questionnaires', 'Invalid questionnaire'));
		}

		// Guard Force URL hack
		if (!$this->isAbleTo($questionnaire)) {
			throw new ForbiddenException(__d('net_commons', 'Permission denied'));
		}

		// 後始末
		$this->Session->delete('Questionnaires.' . $questionnaireId);

		// View変数にセット
		$this->set('questionnaire', $questionnaire);
		$this->set('isDuringTest', $this->_isDuringTest($questionnaire));
	}

/**
 * add questionnaire display method
 *
 * @return void
 */
	public function add() {
		// 作成中データ
		$pastQuestionnaires = array();
		$createOption = QUESTIONNAIRE_CREATE_OPT_NEW;

		// POSTされたデータを読み取り
		if ($this->request->isPost()) {

			$questionnaire = null;

			// 選択生成方法設定
			if (isset($this->data['create_option'])) {

				$createOption = $this->data['create_option'];

				// 空の新規作成
				if ($createOption == QUESTIONNAIRE_CREATE_OPT_NEW) {
					//
					$this->Questionnaire->set($this->request->data);
					// 新規作成時のvalidation実行
					if ($this->Questionnaire->validates(array(
						'fieldList' => array(	'title')))) {
						// デフォルトデータをもとに新規作成
						$questionnaire = $this->Questionnaire->getDefaultQuestionnaire(array(
							'title' => $this->data['Questionnaire']['title']));
					}
				} elseif ($createOption == QUESTIONNAIRE_CREATE_OPT_REUSE) {

					if (isset($this->data['past_questionnaire_id'])) {
						// 過去のアンケートのコピー・クローンで作成
						$questionnaire = $this->__getQuestionnaireCloneById($this->data['past_questionnaire_id']);
					} else {
						// Modelにはない属性のエラーを入れる
						$this->validationErrors['Questionnaire']['past_questionnaire_id'] = __d('questionnaires', 'Please select past questionnaire.');
					}
				}
				if ($questionnaire) {
					$questionnaire['Questionnaire']['block_id'] = $this->viewVars['blockId'];
				}
			} else {
				// 作成方法の指示がないのにPOSTされている場合は、画面の再表示とする
				$this->validationErrors['Questionnaire']['create_option'] = __d('questionnaires', 'Please choose create option.');
			}

			if ($questionnaire) {
				// 作成中アンケートデータをキャッシュに書く
				$this->Session->write('Questionnaires.questionnaire', $questionnaire);
				// 次の画面へリダイレクト
				$this->redirect('questionnaire_questions/edit/' . $this->viewVars['frameId']);
			}
		}

		// 過去データ 取り出し
		// 表示方法設定値取得
		$settings = $this->QuestionnaireFrameSetting->getQuestionnaireFrameSetting($this->viewVars['frameId']);
		$conditions = $this->getConditionForAnswer();
		$pastQuestionnaires['items'] = $this->Questionnaire->getQuestionnairesList(
			$conditions,
			$this->Session->id(),
			$this->Auth->user('id'),
			array(),
			$settings[1] . ' ' . $settings[2],	// 1:sort 2:direction
			0,
			1000
		);

		$this->set('jsPastQuestionnaires', $this->camelizeKeyRecursive($this->_changeBooleansToNumbers($pastQuestionnaires)));
		$this->set('createOption', $createOption);
	}

/**
 * edit method
 *
 * @throws BadRequestException
 * @return void
 */
	public function edit() {
		if ($this->request->isPost()) {

			$postQuestionnaire = $this->request->data;

			$questionnaire = $this->Session->read('Questionnaires.questionnaire');
			if (!$questionnaire) {
				// セッションタイムアウト
				throw new BadRequestException(__d('net_commons', 'Bad Request'));
			}
			$beforeStatus = $questionnaire['Questionnaire']['status'];
			// 設定画面ではアンケート本体に纏わる情報のみがPOSTされる
			$questionnaire = Hash::merge($questionnaire, $postQuestionnaire);

			// 指示された編集状態ステータス
			if (!$status = $this->NetCommonsWorkflow->parseStatus()) {
				$this->__setupViewParameters($questionnaire);
				return;
			}
			$questionnaire['Questionnaire']['status'] = $status;

			if (! $this->__validateQuestionnaireSetting($questionnaire)) {
				$questionnaire['Questionnaire']['status'] = $beforeStatus;
				$this->__setupViewParameters($questionnaire);
				return;
			}

			// それをDBに書く
			$saveQuestionnaire = $this->Questionnaire->saveQuestionnaire($questionnaire);
			if ($saveQuestionnaire == false) {
				$questionnaire['Questionnaire']['status'] = $beforeStatus;
				$this->__setupViewParameters($questionnaire);
				return;
			}

			/////// セッションはまだ消しちゃいけない。表画面で消すこと　$this->Session->delete('Questionnaires');

			// ページトップへリダイレクト
			$this->redirectByFrameId();

		} else {
			// redirectで来るか、もしくは本当に直接のURL指定で来るかのどちらか
			// クエリでアンケートIDの指定がある場合はそちらを優先
			if (!empty($this->request->query['questionnaire_id'])) {
				// 指定されたアンケートデータを取得
				$questionnaire = $this->Questionnaire->find('first', array(
					'conditions' => array(
						'Questionnaire.id' => $this->request->query['questionnaire_id']
					)
				));
			} elseif ($this->Session->check('Questionnaires.questionnaire')) {
				// クエリがない場合はセッションを確認
				$questionnaire = $this->Session->read('Questionnaires.questionnaire');
			} else {
				// それもない場合はエラー
				throw new BadRequestException(__d('net_commons', 'Bad Request'));
			}

			// それをキャッシュに書く
			$this->Session->write('Questionnaires.questionnaire', $questionnaire);

		}
		$this->__setupViewParameters($questionnaire);
	}

/**
 * delete method
 *
 * @return void
 */
	public function delete() {
		if ($this->request->isPost()) {
			// 削除処理
			if (! $this->Questionnaire->deleteQuestionnaire($this->request->data)) {
				return;
			}
			// メッセージ表示
			$this->Session->setFlash(__d('questionnaires', 'This Questionnaire has been deleted.'));
			// ページトップの画面へリダイレクト
			$this->redirectByFrameId();
		}
	}

/**
 * validate questionnaire  setting
 *
 * @param array $data received post data
 * @return bool True on success, false on error
 */
	private function __validateQuestionnaireSetting($data) {
		$errors = array();

		$this->Questionnaire->set($data);
		$this->Questionnaire->validates(array(
			'fieldList' => array(
				'is_period',
				'start_period',
				'end_period',
				'total_show_timing',
				'total_show_start_period',
				'is_no_member_allow',
				'is_anonymity',
				'is_key_pass_use',
				'key_phrase',
				'is_repeat_allow',
				'is_image_authentication',
				'thanks_content',
				//'is_open_mail_send',
				//'open_mail_subject',
				//'open_mail_body',
				'is_answer_mail_send',
			)
		));
		if ($this->Questionnaire->validationErrors) {
			$errors['Questionnaire'] = $this->Questionnaire->validationErrors;
		}

		if (!$this->Comment->validateByStatus($data, array('caller' => 'Questionnaire'))) {
			$errors = Hash::merge($errors, $this->Comment->validationErrors);
		}

		if ($errors) {
			$this->qValidationErrors = $errors;
			return false;
		} else {
			return true;
		}
	}

/**
 * __setupViewParameters method
 *
 * @param array $questionnaire アンケートデータ
 * @return void
 */
	private function __setupViewParameters($questionnaire) {
		$isPublished = $this->Questionnaire->find('count', array(
			'conditions' => array(
				'is_active' => true,
				'origin_id' => $questionnaire['Questionnaire']['origin_id']
			)
		));
		$this->set('questionnaire', $questionnaire);
		$this->set('jsQuestionnaire', $this->camelizeKeyRecursive($this->_changeBooleansToNumbers($questionnaire)));
		$this->set('contentStatus', $questionnaire['Questionnaire']['status']);
		$this->set('comments', $this->_getComments($questionnaire['Questionnaire']));
		$this->set('backUrl', '/questionnaires/questionnaire_questions/edit_result/' . $this->viewVars['frameId']);
		$this->set('isPublished', $isPublished);
	}

/**
 * __getQuestionnaireCloneById 指定されたIDにのアンケートデータのクローンを取得する
 *
 * @param int $questionnaireId アンケートID(編集なのでoriginではなくRAWなIDのほう
 * @return array
 */
	private function __getQuestionnaireCloneById($questionnaireId) {
		$questionnaire = $this->Questionnaire->find('first', array(
			'conditions' => array('Questionnaire.id' => $questionnaireId),
		));

		// ID値のみクリア
		$this->__clearQuestionnaireId($questionnaire);

		return $questionnaire;
	}

/**
 * __clearQuestionnaireId アンケートデータからＩＤのみをクリアする
 *
 * @param array &$questionnaire アンケートデータ
 * @return void
 */
	private function __clearQuestionnaireId(&$questionnaire) {
		foreach ($questionnaire as $qKey => $q) {
			if (is_array($q)) {
				$this->__clearQuestionnaireId($questionnaire[$qKey]);
			} elseif (preg_match('/(.*?)id$/', $qKey) ||
				preg_match('/^created(.*?)/', $qKey) ||
				preg_match('/^modified(.*?)/', $qKey)) {
				unset($questionnaire[$qKey]);
			}
		}
	}
}