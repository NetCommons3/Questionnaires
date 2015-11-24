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
 * @throws Exception
 * @return void
 */
	public function index() {
		// 表示方法設定値取得
		list($displayType, $displayNum, $sort, $dir) =
			$this->QuestionnaireFrameSetting->getQuestionnaireFrameSetting(Current::read('Frame.key'));

		// 条件設定値取得
		$conditions = $this->Questionnaire->getCondition();

		// 単独表示が指定されていた場合
		if ($displayType == QuestionnairesComponent::DISPLAY_TYPE_SINGLE) {
			$displayQ = $this->QuestionnaireFrameDisplayQuestionnaire->find('first', array(
				'frame_key' => Current::read('Frame.key'),
			));
			if (!$displayQ) {
				$this->view = 'QuestionnaireAnswers/noMoreAnswer';
				return;
			}
			$questionnaires = $this->Questionnaire->getQuestionnairesList(
				array('key' => $displayQ['QuestionnaireFrameDisplayQuestionnaire']['questionnaire_key']));
			if (!$questionnaires) {
				$this->view = 'Questionnaires/noQuestionnaire';
				return;
			}
			$url = NetCommonsUrl::actionUrl(array(
				'controller' => 'questionnaire_answers',
				'action' => 'answer',
				Current::read('Block.id'),
				$questionnaires[0]['Questionnaire']['key'],
				'frame_id' => Current::read('Frame.id'),
			));
			$ret = $this->requestAction($url, 'return');
			$this->set('answer', $ret);
			$this->view = 'Questionnaires/answer';

			return;
		}

		// データ取得
		try {
			$subQuery = $this->Questionnaire->getQuestionnaireSubQuery();
			$this->Paginator->settings = array_merge(
				$this->Paginator->settings,
				array(
					'conditions' => $conditions,
					'page' => 1,
					'sort' => $sort,
					'limit' => $displayNum,
					'direction' => $dir,
					'recursive' => 0,
					'joins' => $subQuery,
				)
			);
			$questionnaire = $this->paginate('Questionnaire', $this->_getPaginateFilter());
		} catch (Exception $ex) {
			CakeLog::error($ex);
			throw $ex;
		}
		$this->set('questionnaires', $questionnaire);

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
		$answerStatus = isset($this->params['named']['answer_status']) ? $this->params['named']['answer_status'] : QuestionnairesComponent::QUESTIONNAIRE_ANSWER_VIEW_ALL;
		if ($answerStatus == QuestionnairesComponent::QUESTIONNAIRE_ANSWER_UNANSWERED) {
			$filter = array(
				'OR' => array(
					array('answer_summary_count' => null),
					array('answer_summary_count' => 0)
				)
			);
		} elseif ($answerStatus == QuestionnairesComponent::QUESTIONNAIRE_ANSWER_ANSWERED) {
			$filter = array(
				'answer_summary_count >' => 0
			);
		} elseif ($answerStatus == QuestionnairesComponent::QUESTIONNAIRE_ANSWER_TEST) {
			$filter = array(
				'Questionnaire.status !=' => WorkflowComponent::STATUS_PUBLISHED
			);
		} else {
			$filter = array();
		}
		return $filter;
	}
}