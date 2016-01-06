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
 * default graph colors
 * 基本的にはＪＳで新しい選択肢を作成するので、これと同じ色配列がＪＳファイルにも書いてあります
 *
 * @var string
 */
	static public $defaultGraphColors = array('#f38631', '#e0e4cd', '#69d2e7', '#68e2a7', '#f64649',
		'#4d5361', '#47bfbd', '#7c4f6c', '#23313c', '#9c9b7f', '#be5945', '#cccccc');

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
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
			'className' => 'Questionnaires.QuestionnaireQuestion',
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
		$choiceIndex = $options['choiceIndex'];
		// Choiceモデルは繰り返し判定が行われる可能性高いのでvalidateルールは最初に初期化
		// mergeはしません
		$this->validate = array(
			'choice_label' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => __d('questionnaires', 'Please input choice text.'),
				),
				'choiceLabel' => array(
					'rule' => array('custom', '/^(?!.*[\|\:]).*$/'),
					'message' => __d('questionnaires', 'You can not use the character of |, : for choice text '),
				),
			),
			'other_choice_type' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					//'allowEmpty' => false,
					//'required' => false,
				),
			),
			'choice_sequence' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
				'comparison' => array(
					'rule' => array('comparison', '==', $choiceIndex),
					'message' => __d('questionnaires', 'choice sequence is illegal.')
				),
			),
			'graph_color' => array(
				'rule' => '/^#[a-f0-9]{6}$/i',
				'message' => __d('questionnaires', 'First character is "#". And input the hexadecimal numbers by six digits.'),
				//'allowEmpty' => false,
				//'required' => false,
			),
		);
		// ウィザード画面でのセットアップ中の場合はまだ親ページIDの正当性についてのチェックは行わない
		if (! (isset($options['validate']) && $options['validate'] == QuestionnairesComponent::QUESTIONNAIRE_VALIDATE_TYPE)) {
			$this->validate = Hash::merge($this->validate, array(
				'questionnaire_question_id' => array(
					'numeric' => array(
						'rule' => array('numeric'),
						'message' => __d('net_commons', 'Invalid request.'),
					),
				),
			));
		}
		$this->_checkSkip($options['isSkip'], $options['pageIndex'], $options['maxPageIndex']);

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
			'graph_color' => self::$defaultGraphColors[0],
			'skip_page_sequence' => QuestionnairesComponent::SKIP_GO_TO_END
		);
	}

/**
 * saveQuestionnaireChoice
 * save QuestionnaireChoice data
 *
 * @param array &$choices questionnaire choices
 * @throws InternalErrorException
 * @return bool
 */
	public function saveQuestionnaireChoice(&$choices) {
		// QuestionnaireChoiceが単独でSaveされることはない
		// 必ず上位のQuestionnaireのSaveの折に呼び出される
		// なので、$this->setDataSource('master');といった
		// 決まり処理は上位で行われる
		// ここでは行わない
		foreach ($choices as &$choice) {
			// アンケートは履歴を取っていくタイプのコンテンツデータなのでSave前にはID項目はカット
			// （そうしないと既存レコードのUPDATEになってしまうから）
			$choice = Hash::remove($choice, 'QuestionnaireChoice.id');
			$this->create();
			if (!$this->save($choice, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}
		return true;
	}

/**
 * _checkSkip
 *
 * @param bool $isSkip スキップ有無
 * @param int $pageIndex ページ番号
 * @param int $maxPageIndex このアンケートでの最後のページ番号
 * @return void
 */
	protected function _checkSkip($isSkip, $pageIndex, $maxPageIndex) {
		if ($isSkip != QuestionnairesComponent::SKIP_FLAGS_SKIP) {
			return;
		}
		// 質問がスキップ質問である場合
		// 未設定時はデフォルトの次ページ移動となります
		if (! isset($this->data['QuestionnaireChoice']['skip_page_sequence'])) {
			$this->data['QuestionnaireChoice']['skip_page_sequence'] = $pageIndex + 1;
		}
		// 最後ページへの指定ではない場合
		if ($this->data['QuestionnaireChoice']['skip_page_sequence'] != QuestionnairesComponent::SKIP_GO_TO_END) {
			// そのジャンプ先は現在ページから戻っていないか
			if ($this->data['QuestionnaireChoice']['skip_page_sequence'] <= $pageIndex) {
				$this->validationErrors['skip_page_sequence'][] = __d('questionnaires', 'Invalid skip page. Please set forward page.');
			}
			// そのジャンプ先は存在するページシーケンスか
			if ($this->data['QuestionnaireChoice']['skip_page_sequence'] > $maxPageIndex) {
				$this->validationErrors['skip_page_sequence'][] = __d('questionnaires', 'Invalid skip page. page does not exist.');
			}
		}
	}

}