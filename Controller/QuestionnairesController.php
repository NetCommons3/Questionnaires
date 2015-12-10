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
		'Questionnaires.QuestionnaireFrameSetting',
		'Questionnaires.QuestionnaireFrameDisplayQuestionnaire',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'edit,delete' => 'content_creatable',
			),
		),
		'Questionnaires.Questionnaires',
		'Questionnaires.QuestionnairesOwnAnswer',
		'Paginator',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Workflow.Workflow',
		'NetCommons.Date',
		'NetCommons.DisplayNumber',
		'NetCommons.Button',
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
		// 編集用セッションデータ削除
		$this->Session->delete('Questionnaires.questionnaire');
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		// 表示方法設定値取得
		list(, $displayNum, $sort, $dir) =
			$this->QuestionnaireFrameSetting->getQuestionnaireFrameSetting(Current::read('Frame.key'));

		// 条件設定値取得
		$conditions = $this->Questionnaire->getCondition();

		// データ取得
		//$subQuery = $this->Questionnaire->getQuestionnaireSubQuery();
		$this->Paginator->settings = array_merge(
			$this->Paginator->settings,
			array(
				'conditions' => $conditions,
				'page' => 1,
				'sort' => $sort,
				'limit' => $displayNum,
				'direction' => $dir,
				'recursive' => 0,
				//'joins' => $subQuery,
			)
		);
		if (!isset($this->params['named']['answer_status'])) {
			$this->request->params['named']['answer_status'] = QuestionnairesComponent::QUESTIONNAIRE_ANSWER_VIEW_ALL;
		}
		$questionnaire = $this->paginate('Questionnaire', $this->_getPaginateFilter());
		$this->set('questionnaires', $questionnaire);

		$this->__setOwnAnsweredKeys();

		if (count($questionnaire) == 0) {
			$this->view = 'Questionnaires/noQuestionnaire';
		}
	}

/**
 * _getPaginateFilter method
 *
 * @return array
 */
	protected function _getPaginateFilter() {
		$filter = array();

		if ($this->request->params['named']['answer_status'] == QuestionnairesComponent::QUESTIONNAIRE_ANSWER_TEST) {
			$filter = array(
				'Questionnaire.status !=' => WorkflowComponent::STATUS_PUBLISHED
			);

			return $filter;
		}

		$filterCondition = array('Questionnaire.key' => $this->QuestionnairesOwnAnswer->getOwnAnsweredKeys());
		if ($this->request->params['named']['answer_status'] == QuestionnairesComponent::QUESTIONNAIRE_ANSWER_UNANSWERED) {
			$filter = array(
				'NOT' => $filterCondition
			);
		} elseif ($this->request->params['named']['answer_status'] == QuestionnairesComponent::QUESTIONNAIRE_ANSWER_ANSWERED) {
			$filter = array(
				$filterCondition
			);
		}

		return $filter;
	}

/**
 * Set view value of answered questionnaire keys
 *
 * @return void
 */
	private function __setOwnAnsweredKeys() {
		if ($this->request->params['named']['answer_status'] == QuestionnairesComponent::QUESTIONNAIRE_ANSWER_UNANSWERED) {
			$this->set('ownAnsweredKeys', array());

			return;
		}

		$this->set('ownAnsweredKeys', $this->QuestionnairesOwnAnswer->getOwnAnsweredKeys());
	}

}