<?php
/**
 * QuestionnaireAnswerSummaries Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnairesAppController', 'Questionnaires.Controller');

/**
 * QuestionnaireAnswerSummariesController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuestionnaireAnswerSummariesController extends QuestionnairesAppController {

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
	);

/**
 * use helpers
 *
 */
	public $helpers = array(
		'NetCommons.TitleIcon',
		'Workflow.Workflow',
	);

/**
 * target questionnaire data
 *
 */
	private $__questionnaire = null;

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		// ゲストアクセスOKのアクションを設定
		$this->Auth->allow('view', 'no_summaries');

		// 現在の表示形態を調べておく
		list($this->__displayType) =
			$this->QuestionnaireFrameSetting->getQuestionnaireFrameSetting(Current::read('Frame.key'));
		$this->set('displayType', $this->__displayType);

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
			$this->setAction('no_summaries');
			return;
		}

		//集計表示していいかどうかの判断

		if (! $this->isAbleToDisplayAggregatedData($this->__questionnaire)) {
			$this->setAction('no_summaries');
			return;
		}
	}

/**
 * result method
 *
 * @return void
 */
	public function view() {
		$questionnaire = $this->__questionnaire;

		//集計処理を行います。
		$questions = $this->QuestionnaireAnswerSummary->getAggregate($questionnaire);

		//画面用データをセットする。
		$this->set('questionTypeOptions', $this->Questionnaires->getQuestionTypeOptionsWithLabel());
		$this->set('questionnaire', $questionnaire);
		$this->set('questions', $questions);
	}

/**
 * no_summaries method
 * 条件によって集計結果が見れないアンケートにアクセスしたときに表示
 *
 * @return void
 */
	public function no_summaries() {
	}

}
