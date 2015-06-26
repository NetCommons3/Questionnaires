<?php
/**
 * QuestionnaireChoice Model
 *
 * @property QuestionnaireQuestion $QuestionnaireQuestion
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');

/**
 * Summary for QuestionnaireChoice Model
 */
class QuestionnaireChoice extends QuestionnairesAppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.Publishable',
		'NetCommons.OriginalKey',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'QuestionnaireQuestion' => array(
			'className' => 'QuestionnaireQuestion',
			'foreignKey' => 'questionnaire_question_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
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
			'choice_label' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => __d('questionnaires', 'Please input choice text.'),
				),
			),
			'questionnaire_question_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'other_choice_type' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'choice_sequence' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'graph_color' => array(
					'rule' => '/^#[a-f0-9]{6}$/i',
					'message' => __d('questionnaires', 'First character is "#". And input the hexadecimal numbers by six digits.'),
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'is_auto_translated' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
		));
		return parent::beforeValidate($options);
	}

/**
 * getDefaultChoice
 * get default data of questionnaire choice
 *
 * @return array
 */
	public function getDefaultChoice() {
		return	array(
				'choice_sequence' => 0,
				'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
				'choice_label' => __d('questionnaires', 'new choice') . '1',
				'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
				'skip_page_sequence' => QuestionnairesComponent::SKIP_GO_TO_END
			);
	}

/**
 * saveQuestionnaireChoice
 * save QuestionnaireChoice data
 *
 * @param int $questionId questionnaire question id
 * @param int $status status
 * @param array &$choices questionnaire choices
 * @throws InternalErrorException
 * @return bool
 */
	public function saveQuestionnaireChoice($questionId, $status, &$choices) {
		// QuestionnaireChoiceが単独でSaveされることはない
		// 必ず上位のQuestionnaireのSaveの折に呼び出される
		// なので、$this->setDataSource('master');といった
		// 決まり処理は上位で行われる
		// ここでは行わない
		foreach ($choices as &$c) {
			$c = $this->_setupSaveData($c, $status);
			$c['questionnaire_question_id'] = $questionId;
			$this->create();
			if (!$this->save($c)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}
	}
}
