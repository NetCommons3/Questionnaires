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

App::uses('AppController', 'Controller');

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
		$this->Auth->allow('view');

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
		// 集計結果の表示はアンケート公開時期とは異なるので
		$conditions = Hash::remove($conditions, 'public_type');
		$conditions = Hash::remove($conditions, 'publish_start');
		$conditions = Hash::remove($conditions, 'publish_end');

		$this->__questionnaire = $this->Questionnaire->find('first', array(
			'conditions' => $conditions,
		));
		if (! $this->__questionnaire) {
			$this->setAction('throwBadRequest');
			return;
		}

		//集計表示していいかどうかの判断

		if (! $this->isAbleToDisplayAggregatedData($this->__questionnaire)) {
			$this->setAction('throwBadRequest');
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
		$this->set('questionnaire', $questionnaire);
		$this->set('questions', $questions);
	}

}