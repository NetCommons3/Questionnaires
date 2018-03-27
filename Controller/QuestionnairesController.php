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

App::uses('QuestionnairesAppController', 'Questionnaires.Controller');

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
		'NetCommons.TitleIcon',
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
		$this->Paginator->settings = array_merge(
			$this->Paginator->settings,
			array(
				'conditions' => $conditions,
				'page' => 1,
				'order' => array($sort => $dir),
				'limit' => $displayNum,
				'recursive' => 0,
			)
		);
		if (!isset($this->params['named']['answer_status'])) {
			$this->request->params['named']['answer_status'] =
				QuestionnairesComponent::QUESTIONNAIRE_ANSWER_VIEW_ALL;
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

		$answerStat = $this->request->params['named']['answer_status'];

		if ($answerStat == QuestionnairesComponent::QUESTIONNAIRE_ANSWER_TEST) {
			$filter = array(
				'Questionnaire.status !=' => WorkflowComponent::STATUS_PUBLISHED
			);
			return $filter;
		}

		$filterCondition = array(
			'Questionnaire.key' => $this->QuestionnairesOwnAnswer->getOwnAnsweredKeys()
		);
		if ($answerStat == QuestionnairesComponent::QUESTIONNAIRE_ANSWER_UNANSWERED) {
			$filter = array(
				'Questionnaire.status' => WorkflowComponent::STATUS_PUBLISHED,
				'NOT' => $filterCondition
			);
		} elseif ($answerStat == QuestionnairesComponent::QUESTIONNAIRE_ANSWER_ANSWERED) {
			$filter = array(
				'Questionnaire.status' => WorkflowComponent::STATUS_PUBLISHED,
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
		$answerStat = $this->request->params['named']['answer_status'];
		if ($answerStat == QuestionnairesComponent::QUESTIONNAIRE_ANSWER_UNANSWERED) {
			$this->set('ownAnsweredKeys', array());

			return;
		}

		$this->set('ownAnsweredKeys', $this->QuestionnairesOwnAnswer->getOwnAnsweredKeys());
	}

}