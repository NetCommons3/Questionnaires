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
		'NetCommons.OriginalKey',
		'Wysiwyg.Wysiwyg' => array(
			'fields' => array('question_value')
		),
		//多言語
		'M17n.M17n' => array(
			'commonFields' => array(
				'question_sequence',
				'question_type',
				'is_require',
				'question_type_option',
				'is_choice_random',
				'is_choice_horizon',
				'is_skip',
				'is_jump',
				'is_range',
				'min',
				'max',
				'is_result_display',
				'result_display_type',
			),
			'associations' => array(
				'QuestionnaireChoice' => array(
					'class' => 'Questionnaires.QuestionnaireChoice',
					'foreignKey' => 'questionnaire_question_id',
				),
			),
			'afterCallback' => false,
		),
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
			'className' => 'Questionnaires.QuestionnairePage',
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
			'className' => 'Questionnaires.QuestionnaireChoice',
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
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 * @see Model::__construct()
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->loadModels([
			'QuestionnaireChoice' => 'Questionnaires.QuestionnaireChoice',
		]);
	}

/**
 * Called before each find operation. Return false if you want to halt the find
 * call, otherwise return the (modified) query data.
 *
 * @param array $query Data used to execute this query, i.e. conditions, order, etc.
 * @return mixed true if the operation should continue, false if it should abort; or, modified
 *  $query to continue with new $query
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforefind
 */
	public function beforeFind($query) {
		//hasManyで実行されたとき、多言語の条件追加
		if (! $this->id && isset($query['conditions']['questionnaire_page_id'])) {
			$questionnairePageId = $query['conditions']['questionnaire_page_id'];
			$query['conditions']['questionnaire_page_id'] =
					$this->getQuestionnairePageIdsForM17n($questionnairePageId);
			$query['conditions']['OR'] = array(
				'QuestionnaireQuestion.language_id' => Current::read('Language.id'),
				'QuestionnaireQuestion.is_translation' => false,
			);

			return $query;
		}

		return parent::beforeFind($query);
	}

/**
 * 多言語データ取得のため、当言語のquestionnaire_page_idから全言語のquestionnaire_page_idを取得する
 *
 * @param id $questionnairePageId 当言語のquestionnaire_page_id
 * @return array
 */
	public function getQuestionnairePageIdsForM17n($questionnairePageId) {
		$questionnairePage = $this->QuestionnairePage->find('first', array(
			'recursive' => -1,
			'callbacks' => false,
			'fields' => array('id', 'key', 'questionnaire_id'),
			'conditions' => array('id' => $questionnairePageId),
		));

		$questionnaireIds = $this->QuestionnairePage->getQuestionnaireIdsForM17n(
			$questionnairePage['QuestionnairePage']['questionnaire_id']
		);
		$questionnairePageIds = $this->QuestionnairePage->find('list', array(
			'recursive' => -1,
			'callbacks' => false,
			'fields' => array('id', 'id'),
			'conditions' => array(
				'questionnaire_id' => $questionnaireIds,
				'key' => $questionnairePage['QuestionnairePage']['key']
			),
		));

		return array_values($questionnairePageIds);
	}

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		$qIndex = $options['questionIndex'];
		// Questionモデルは繰り返し判定が行われる可能性高いのでvalidateルールは最初に初期化
		// mergeはしません
		$this->validate = array(
			'question_sequence' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'comparison' => array(
					'rule' => array('comparison', '==', $qIndex),
					'message' => __d('questionnaires', 'question sequence is illegal.')
				),
			),
			'question_type' => array(
				'inList' => array(
					'rule' => array('inList', QuestionnairesComponent::$typesList),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'question_value' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
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
				'inList' => array(
					'rule' => array(
						'inList',
						$this->_getResultDisplayList($this->data['QuestionnaireQuestion']['question_type'])
					),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'result_display_type' => array(
				'inList' => array(
					'rule' => array('inList', QuestionnairesComponent::$resultDispTypesList),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_range' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
		);
		// 範囲制限設定された質問の場合
		if ($this->data['QuestionnaireQuestion']['is_range'] == true) {
			$this->validate = Hash::merge($this->validate, array(
				'min' => array(
					'notBlank' => array(
						'rule' => array('notBlank'),
						'message' => __d('questionnaires', 'Please enter both the maximum and minimum values.'),
					),
					'comparison' => array(
						'rule' => array('comparison', '<', $this->data['QuestionnaireQuestion']['max']),
						'message' => __d('questionnaires', 'Please enter smaller value than max.')
					),
				),
				'max' => array(
					'notBlank' => array(
						'rule' => array('notBlank'),
						'message' => __d('questionnaires', 'Please enter both the maximum and minimum values.'),
					),
					'comparison' => array(
						'rule' => array('comparison', '>', $this->data['QuestionnaireQuestion']['min']),
						'message' => __d('questionnaires', 'Please enter bigger value than min.')
					),
				),
			));

		}
		// validates時にはまだquestionnaire_page_idの設定ができないのでチェックしないことにする
		// questionnaire_page_idの設定は上位のQuestionnairePageクラスで責任を持って行われるものとする

		parent::beforeValidate($options);

		$isSkip = $this->data['QuestionnaireQuestion']['is_skip'];
		// 付属の選択肢以下のvalidate
		if ($this->_checkChoiceExists() && isset($this->data['QuestionnaireChoice'])) {
			// この質問種別に必要な選択肢データがちゃんとあるなら選択肢をバリデート
			$validationErrors = array();
			foreach ($this->data['QuestionnaireChoice'] as $cIndex => $choice) {
				// 質問データバリデータ
				$this->QuestionnaireChoice->create();
				$this->QuestionnaireChoice->set($choice);
				$options['choiceIndex'] = $cIndex;
				$options['isSkip'] = $isSkip;
				if (!$this->QuestionnaireChoice->validates($options)) {
					$validationErrors['QuestionnaireChoice'][$cIndex] =
						$this->QuestionnaireChoice->validationErrors;
				}
			}
			$this->validationErrors += $validationErrors;
		}

		return true;
	}

/**
 * getDefaultQuestion
 * get default data of questionnaire question
 *
 * @return array
 */
	public function getDefaultQuestion() {
		$question = array(
			'question_sequence' => 0,
			'question_value' => __d('questionnaires', 'New Question') . '1',
			'question_type' => QuestionnairesComponent::TYPE_SELECTION,
			'is_require' => QuestionnairesComponent::USES_NOT_USE,
			'is_skip' => QuestionnairesComponent::SKIP_FLAGS_NO_SKIP,
			'is_choice_random' => QuestionnairesComponent::USES_NOT_USE,
			'is_range' => QuestionnairesComponent::USES_NOT_USE,
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
 * @param array &$questions questionnaire questions
 * @throws InternalErrorException
 * @return bool
 */
	public function saveQuestionnaireQuestion(&$questions) {
		$this->loadModels([
			'QuestionnaireChoice' => 'Questionnaires.QuestionnaireChoice',
		]);
		// QuestionnaireQuestionが単独でSaveされることはない
		// 必ず上位のQuestionnaireのSaveの折に呼び出される
		// なので、$this->setDataSource('master');といった
		// 決まり処理は上位で行われる
		// ここでは行わない

		foreach ($questions as &$question) {
			// アンケートは履歴を取っていくタイプのコンテンツデータなのでSave前にはID項目はカット
			// （そうしないと既存レコードのUPDATEになってしまうから）
			$question = Hash::remove($question, 'QuestionnaireQuestion.id');

			$this->create();
			if (! $this->save($question, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			$questionId = $this->id;

			if (isset($question['QuestionnaireChoice'])) {
				$question = Hash::insert(
					$question,
					'QuestionnaireChoice.{n}.questionnaire_question_id', $questionId);
				// もしもChoiceのsaveがエラーになった場合は、
				// ChoiceのほうでInternalExceptionErrorが発行されるのでここでは何も行わない
				$this->QuestionnaireChoice->saveQuestionnaireChoice($question['QuestionnaireChoice']);
			}
		}
		return true;
	}

/**
 * _checkChoiceExists
 *
 * 適正な選択肢を持っているか
 *
 * @return bool
 */
	protected function _checkChoiceExists() {
		$questionType = $this->data['QuestionnaireQuestion']['question_type'];
		// テキストタイプ、テキストエリアタイプの時は選択肢不要
		if (QuestionnairesComponent::isOnlyInputType($questionType)) {
			return true;
		}

		// 上記以外の場合は最低１つは必要
		if (! Hash::check($this->data, 'QuestionnaireChoice.{n}')) {
			$this->validationErrors['question_type'][] =
				__d('questionnaires', 'please set at least one choice.');
			return false;
		}

		// マトリクスタイプの時は行に１つ列に一つ必要
		// マトリクスタイプのときは、行、カラムの両方ともに最低一つは必要
		if (QuestionnairesComponent::isMatrixInputType($questionType)) {
			$rows = Hash::extract(
				$this->data['QuestionnaireChoice'],
				'{n}[matrix_type=' . QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX . ']');
			$cols = Hash::extract(
				$this->data['QuestionnaireChoice'],
				'{n}[matrix_type=' . QuestionnairesComponent::MATRIX_TYPE_COLUMN . ']');

			if (empty($rows) || empty($cols)) {
				$this->validationErrors['question_type'][] =
					__d('questionnaires', 'please set at least one choice at row and column.');
				return false;
			}
		}
		return true;
	}

/**
 * _getResultDisplayList
 * 質問種別に応じて許されるisResultDisplayの設定値
 *
 * @param int $questionType 質問種別
 * @return array
 */
	protected function _getResultDisplayList($questionType) {
		if (QuestionnairesComponent::isOnlyInputType($questionType)) {
			return array(QuestionnairesComponent::USES_NOT_USE);
		}
		return array(QuestionnairesComponent::USES_USE, QuestionnairesComponent::USES_NOT_USE);
	}
}