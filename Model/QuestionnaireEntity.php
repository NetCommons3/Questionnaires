<?php
/**
 * QuestionnaireEntity Model
 *
 * @property Questionnaire $Questionnaire
 * @property QuestionnairePage $QuestionnairePage
 *
* @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
* @link     http://www.netcommons.org NetCommons Project
* @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');

/**
 * Summary for QuestionnaireEntity Model
 */
class QuestionnaireEntity extends QuestionnairesAppModel {

/**
 * Use database config
 *
 * @var string
 */
	public $useDbConfig = 'master';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'questionnaire_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Questionnaire' => array(
			'className' => 'Questionnaire',
			'foreignKey' => 'questionnaire_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'QuestionnairePage' => array(
			'className' => 'QuestionnairePage',
			'foreignKey' => 'questionnaire_entity_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
	/**
	 * getQuestionnaireById 指定されたIDにマッチするアンケートデータを取得する
	 * @param int $questionnaireId アンケートID
	 * @param boolean $contentEditable 編集権限
	 * @return array
	 */
	public function getQuestionnaireEntityById($questionnaireId, $contentEditable) {

		$lastQuestionnaireEntity = $this->find('first', array(
			'conditions' => array(
				'QuestionnaireEntity.questionnaire_id' => $questionnaireId,
				($contentEditable) ? null : array('status'=>NetCommonsBlockComponent::STATUS_PUBLISHED, 'questionnaire_status'=>0),
			),
			'order' => array('QuestionnaireEntity.modified DESC'),
		));
		if($lastQuestionnaireEntity) {
			$entityId = $lastQuestionnaireEntity['QuestionnaireEntity']['id'];
		}
		else {
			return array();
		}
		$questionnaireEntity = $this->find('first', array(
			'conditions' => array('QuestionnaireEntity.id' => $entityId),
		));

		// ページ配下の質問データも取り出す
		$this->_setQuestions($questionnaireEntity, $contentEditable);

		return $questionnaireEntity;
	}
	/**
	 * getQuestionnaireById 指定されたIDにのアンケートデータのクローンを取得する
	 * @param int $questionnaireId アンケートID
	 * @return array
	 */
	public function getQuestionnaireEntityCloneById($questionnaireId) {
		$lastQuestionnaireEntity = $this->find('first', array(
			'conditions' => array(
				'QuestionnaireEntity.questionnaire_id' => $questionnaireId,
			),
			'order' => array('QuestionnaireEntity.created DESC'),
		));
		if($lastQuestionnaireEntity) {
			$entityId = $lastQuestionnaireEntity['QuestionnaireEntity']['id'];
		}
		else {
			return array();
		}
		$questionnaireEntity = $this->find('first', array(
			'conditions' => array('QuestionnaireEntity.id' => $entityId),
		));

		// ページ配下の質問データも取り出す
		$this->_setQuestions($questionnaireEntity);

		// ID値のみクリア
		$this->_clearQuestionnaireId($questionnaireEntity);

		return $questionnaireEntity;
	}

	/**
	 * _getDefaultQuestionnaireEntity アンケートエンティティのデフォルト構造を取得
	 * @return array
	 */
	public function getDefaultQuestionnaireEntity() {
		$ret = array(
			'Questionnaire' => array(),
			'QuestionnaireEntity' => array('title' => '',
											'total_show_flag' => QuestionnairesComponent::EXPRESSION_SHOW),
		);
		$this->_insDefaultPage($ret);
		return $ret;
	}

	/**
	 * saveQuestionnaire アンケート情報を保存
	 * @param array $questionnaire アンケートデータ
	 * @return boolean
	 */
	public function saveQuestionnaire($questionnaire) {
		$this->save($questionnaire);
	}
	/**
	 * supplementQuestionnaire 不完全アンケート情報の各種項目を補完する
	 * @param array $questionnaire アンケートデータ
	 * @return boolean
	 */
	public function supplementQuestionnaire(&$questionnaire)	{
		//
		// アンケート稼働状態に指示がない場合は公開中を設定する
		if(!array_key_exists('questionnaire_status', $questionnaire['Questionnaire'])) {
			$questionnaire['Questionnaire']['questionnaire_status'] = QuestionnairesComponent::STATUS_STARTED;
		}
		if(!array_key_exists('status', $questionnaire['QuestionnaireEntity'])) {
			$questionnaire['QuestionnaireEntity']['status'] = NetCommonsBlockComponent::STATUS_IN_DRAFT;
		}

		// アンケートエンティティ情報に日付項目がないとJSでエラーになるので補完する
		if(!array_key_exists('start_period', $questionnaire['QuestionnaireEntity'])) {
			$questionnaire['QuestionnaireEntity']['start_period'] = null;
		}
		if(!array_key_exists('end_period', $questionnaire['QuestionnaireEntity'])) {
			$questionnaire['QuestionnaireEntity']['end_period'] = null;
		}
		if(!array_key_exists('total_show_start_peirod', $questionnaire['QuestionnaireEntity'])) {
			$questionnaire['QuestionnaireEntity']['total_show_start_peirod'] = null;
		}

		// 現時点では質問データの各項目に不全が発生するのでそこを補完
		if(isset($questionnaire['QuestionnairePage'])){
			foreach($questionnaire['QuestionnairePage'] as $pageIndex => $page) {
				if(isset($page['QuestionnaireQuestion'])) {
					foreach($page['QuestionnaireQuestion'] as $qIndex => $question) {
						if(!array_key_exists('result_display_flag', $question)) {
							if(isset($question['question_type']) &&
								($question['question_type'] == QuestionnairesComponent::TYPE_TEXT
								|| $question['question_type'] == QuestionnairesComponent::TYPE_TEXT_AREA
								|| $question['question_type'] == QuestionnairesComponent::TYPE_DATE_AND_TIME)) {
								$questionnaire['QuestionnairePage'][$pageIndex]['QuestionnaireQuestion'][$qIndex]['result_display_flag'] = QuestionnairesComponent::EXPRESSION_NOT_SHOW;
								$questionnaire['QuestionnairePage'][$pageIndex]['QuestionnaireQuestion'][$qIndex]['result_display_type'] = QuestionnairesComponent::RESULT_DISPLAY_TYPE_TABLE;
							}
							else {
								$questionnaire['QuestionnairePage'][$pageIndex]['QuestionnaireQuestion'][$qIndex]['result_display_flag'] = QuestionnairesComponent::EXPRESSION_SHOW;
								$questionnaire['QuestionnairePage'][$pageIndex]['QuestionnaireQuestion'][$qIndex]['result_display_type'] = QuestionnairesComponent::RESULT_DISPLAY_TYPE_BAR_CHART;
							}
						}
					}
				}
			}
		}
	}
	/**
	 * _setQuestions ページの質問を補てんする
	 * @param array $questionnaire アンケートデータ
	 * @return void
	 */
	private function _setQuestions(&$questionnaire, $contentEditable = false) {
		$QuestionnaireQuestion = Classregistry::init('Questionnaires.QuestionnaireQuestion');
		foreach($questionnaire['QuestionnairePage'] as $pageIndex => &$page) {
			if(isset($page['id'])) {
				$questions = $QuestionnaireQuestion->getQuestionsByPageId($page['id']);
				if(!empty($questions)) {
					//$questionnaire['QuestionnairePage'][$pageIndex]['QuestionnaireQuestion'] = array();
//					$page['QuestionnaireQuestion'] = $questions['QuestionnaireQuestion'];
					foreach ($questions as $qIndex => $question) {
						$choices = $question['QuestionnaireChoice'];
						$question['QuestionnaireQuestion']['QuestionnaireChoice']  =  $choices;
						$page['QuestionnaireQuestion'][] = $question['QuestionnaireQuestion'];
					//	$questionnaire['QuestionnairePage'][$pageIndex]['QuestionnaireQuestion'][$qIndex] = $question['QuestionnaireQuestion'];
					//	$questionnaire['QuestionnairePage'][$pageIndex]['QuestionnaireQuestion'][$qIndex]['QuestionnaireChoice'] = $question['QuestionnaireChoice'];
					}
				}
			}
		}
	}
	/**
	 * _insDefaultPage アンケートエンティティに少なくとも１つのページデータを設定する
	 * @param array $questionnaire アンケートデータ
	 * @return void
	 */
	private function _insDefaultPage(&$questionnaire) {
		if(!isset($questionnaire['QuestionnairePage'][0])) {
			$questionnaire['QuestionnairePage'][0]['page_title'] = __d('questionnaires', 'First Page');
			$questionnaire['QuestionnairePage'][0]['page_sequence'] = 0;
			$questionnaire['QuestionnairePage'][0]['QuestionnaireQuestion'] = array();
		}
	}
	/**
	 * _clearQuestionnaireId アンケートデータからＩＤのみをクリアする
	 * @param array $questionnaire アンケートデータ
	 * @return void
	 */
	private function _clearQuestionnaireId(&$questionnaire) {
		foreach($questionnaire as $q_key=>$q) {
			if(is_array($q)) {
				$this->_clearQuestionnaireId($questionnaire[$q_key]);
			}
			else if(preg_match('/(.*?)id$/', $q_key) ||
					preg_match('/^created(.*?)/', $q_key) ||
					preg_match('/^modified(.*?)/', $q_key)) {
				$this->log($q_key."\n", 'debug');
				unset($questionnaire[$q_key]);
			}
		}
	}
}
