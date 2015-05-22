<?php
/**
 * QuestionnaireQuestion Model
 *
 * @property QuestionnairePage $QuestionnairePage
 * @property QuestionnaireChoice $QuestionnaireChoice
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');

/**
 * Summary for QuestionnaireQuestion Model
 */
class QuestionnaireQuestion extends QuestionnairesAppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.Publishable',
		'NetCommons.OriginalKey',
		'Questionnaires.QuestionnaireValidate',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'QuestionnairePage' => array(
			'className' => 'QuestionnairePage',
			'foreignKey' => 'questionnaire_page_id',
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
		'QuestionnaireChoice' => array(
			'className' => 'QuestionnaireChoice',
			'foreignKey' => 'questionnaire_question_id',
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
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		$this->validate = Hash::merge($this->validate, array(
			'questionnaire_page_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'question_sequence' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'question_type' => array(
				'inList' => array(
					'rule' => array('inList', QuestionnairesComponent::$typesList),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'question_value' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => __d('questionnaires', 'Please input question text.'),
				),
			),
			'is_require' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_choice_random' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_skip' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_result_display' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'checkRelationshipQuestionType' => array(
					'rule' => array('checkRelationshipQuestionType'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'result_display_type' => array(
				'inList' => array(
					'rule' => array('inList', QuestionnairesComponent::$resultDispTypesList),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'min' => array(
				'checkMinMax' => array(
					'rule' => array('checkMinMax'),
					'message' => __d('questionnaires', 'Please enter both the maximum and minimum values.'),
				),
			),
			'max' => array(
				'checkMinMax' => array(
					'rule' => array('checkMinMax'),
					'message' => __d('questionnaires', 'Please enter both the maximum and minimum values.'),
				),
			),
		));
		return parent::beforeValidate($options);
	}

/**
 * getDefaultQuestion
 * get default data of questionnaire question
 *
 * @return array
 */
	public function getDefaultQuestion() {
		$this->loadModels([
			'QuestionnaireChoice' => 'Questionnaires.QuestionnaireChoice',
		]);
		$question = array(
			'question_sequence' => 0,
			'question_value' => __d('questionnaires', 'New Question') . '1',
			'question_type' => QuestionnairesComponent::TYPE_SELECTION,
			'is_result_display' => QuestionnairesComponent::EXPRESSION_SHOW,
			'result_display_type' => QuestionnairesComponent::RESULT_DISPLAY_TYPE_BAR_CHART
		);
		$question['QuestionnaireChoice'][0] = $this->QuestionnaireChoice->getDefaultChoice();
		return $question;
	}

/**
 * setQuestionToPage
 * setup page data to questionnaire array
 *
 * @param array &$questionnaire questionnaire data
 * @param array &$page questionnaire page data
 * @return void
 */
	public function setQuestionToPage(&$questionnaire, &$page) {
		$questions = $this->find('all', array(
			'conditions' => array(
				'questionnaire_page_id' => $page['id'],
			),
			'order' => array(
				'question_sequence' => 'asc',
			)
		));

		if (!empty($questions)) {
			foreach ($questions as $question) {
				if (isset($question['QuestionnaireChoice'])) {
					$choices = $question['QuestionnaireChoice'];
					$question['QuestionnaireQuestion']['QuestionnaireChoice'] = $choices;
					$page['QuestionnaireQuestion'][] = $question['QuestionnaireQuestion'];
				}
				$questionnaire['Questionnaire']['question_count']++;
			}
		}
	}

/**
 * saveQuestionnaireQuestion
 * save QuestionnaireQuestion data
 *
 * @param int $pageId questionnaire page id
 * @param int $status status
 * @param array &$questions questionnaire questions
 * @throws InternalErrorException
 * @return bool
 */
	public function saveQuestionnaireQuestion($pageId, $status, &$questions) {
		$this->loadModels([
			'QuestionnaireChoice' => 'Questionnaires.QuestionnaireChoice',
		]);
		// QuestionnaireQuestionが単独でSaveされることはない
		// 必ず上位のQuestionnaireのSaveの折に呼び出される
		// なので、$this->setDataSource('master');といった
		// 決まり処理は上位で行われる
		// ここでは行わない

		foreach ($questions as &$qq) {
			$saveQuestion = $this->_setupSaveData($qq, $status);
			$saveQuestion['questionnaire_page_id'] = $pageId;

			$this->create();
			if (!$this->save($saveQuestion)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			};

			$questionId = $this->id;

			if (isset($qq['QuestionnaireChoice'])) {
				$this->QuestionnaireChoice->saveQuestionnaireChoice($questionId, $status, $qq['QuestionnaireChoice']);
			}
		}
	}
}