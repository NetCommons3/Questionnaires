<?php
/**
 * QuestionnairePage Model
 *
 * @property Questionnaire $Questionnaire
 * @property QuestionnaireQuestion $QuestionnaireQuestion
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');

/**
 * Summary for QuestionnairePage Model
 */
class QuestionnairePage extends QuestionnairesAppModel {

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
	public $validate = array();

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Questionnaire' => array(
			'className' => 'Questionnaires.Questionnaire',
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
		'QuestionnaireQuestion' => array(
			'className' => 'Questionnaires.QuestionnaireQuestion',
			'foreignKey' => 'questionnaire_page_id',
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
 * getDefaultPage
 * get default data of questionnaire page
 *
 * @return array
 */
	public function getDefaultPage() {
		$this->QuestionnaireQuestion = ClassRegistry::init('Questionnaires.QuestionnaireQuestion', true);

		$page = array(
			'page_title' => __d('questionnaires', 'First Page'),
			'page_sequence' => 0,
			'key' => '',
			'route_number' => 0,
		);
		$page['QuestionnaireQuestion'][0] = $this->QuestionnaireQuestion->getDefaultQuestion();

		return $page;
	}

/**
 * getNextPage
 * get next answer page number
 *
 * @param array $questionnaire questionnaire
 * @param int $nowPageSeq current page sequence number
 * @param array $nowAnswers now answer
 *
 * @return array
 */
	public function getNextPage($questionnaire, $nowPageSeq, $nowAnswers) {
		// 次ページはデフォルトならば＋１です
		$nextPageSeq = $nowPageSeq + 1;
		// 回答にスキップロジックで指定されたものがないかチェックし、行き先があるならそのページ番号を返す
		foreach ($nowAnswers as $answer) {
			$targetQuestion = Hash::extract($questionnaire['QuestionnairePage'], '{n}.QuestionnaireQuestion.{n}[key=' . $answer[0]['questionnaire_question_key'] . ']');
			if ($targetQuestion) {
				$q = $targetQuestion[0];
				// skipロジック対象の質問ならば次ページのチェックを行う
				if ($q['is_skip'] == QuestionnairesComponent::SKIP_FLAGS_SKIP) {
					$choiceIds = explode(QuestionnairesComponent::ANSWER_VALUE_DELIMITER,
						trim($answer[0]['answer_value'], QuestionnairesComponent::ANSWER_DELIMITER));
					// スキップロジックの選択肢みつけた
					$choice = Hash::extract($q['QuestionnaireChoice'], '{n}[key=' . $choiceIds[0] . ']');
					if ($choice) {
						$c = $choice[0];
						if (!empty($c['skip_page_sequence'])) {
							// スキップ先ページ
							$nextPageSeq = $c['skip_page_sequence'];
							break;
						}
					}
				}
			}
		}
		// 次ページがもしかして存在しない（つまりエンドかも）
		if ($nextPageSeq == QuestionnairesComponent::SKIP_GO_TO_END) {
			return false;
		}
		// ページ情報がない？終わりにする
		if (!isset($questionnaire['QuestionnairePage'])) {
			return false;
		}
		// ページ配列はページのシーケンス番号順に取り出されているので
		$pages = $questionnaire['QuestionnairePage'];
		$endPage = end($pages);
		if ($endPage['page_sequence'] < $nextPageSeq) {
			return false;
		}
		return $nextPageSeq;
	}
/**
 * setPageToQuestionnaire
 * setup page data to questionnaire array
 *
 * @param array &$questionnaire questionnaire data
 * @return void
 */
	public function setPageToQuestionnaire(&$questionnaire) {
		$this->QuestionnaireQuestion = ClassRegistry::init('Questionnaires.QuestionnaireQuestion', true);
		// ページデータがアンケートデータの中にない状態でここが呼ばれている場合、
		if (!isset($questionnaire['QuestionnairePage'])) {
			$pages = $this->find('all', array(
				'conditions' => array(
					'questionnaire_id' => $questionnaire['Questionnaire']['id'],
				),
				'order' => array('page_sequence ASC'),
				'recursive' => -1));

			$questionnaire['QuestionnairePage'] = Hash::combine($pages, '{n}.QuestionnairePage.page_sequence', '{n}.QuestionnairePage');
		}
		$questionnaire['Questionnaire']['page_count'] = 0;
		if (isset($questionnaire['QuestionnairePage'])) {
			foreach ($questionnaire['QuestionnairePage'] as &$page) {
				$this->QuestionnaireQuestion->setQuestionToPage($questionnaire, $page);
				$questionnaire['Questionnaire']['page_count']++;
			}
		}
	}
/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function beforeValidate($options = array()) {
		$pageIndex = $options['pageIndex'];
		// Pageモデルは繰り返し判定が行われる可能性高いのでvalidateルールは最初に初期化
		// mergeはしません
		$this->validate = array(
			'page_sequence' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
				),
				'comparison' => array(
					'rule' => array('comparison', '==', $pageIndex),
					'message' => __d('questionnaires', 'page sequence is illegal.')
				),
			),
			'route_number' => array(
				'numeric' => array(
				'rule' => array('numeric'),
					//'message' => 'Your custom message here',
					//'allowEmpty' => false,
					//'required' => false,
				),
			),
		);
		// validates時にはまだquestionnaire_idの設定ができないのでチェックしないことにする
		// questionnaire_idの設定は上位のQuestionnaireクラスで責任を持って行われるものとする

		parent::beforeValidate($options);

		// 付属の質問以下のvalidate
		if (! isset($this->data['QuestionnaireQuestion'][0])) {
			$this->validationErrors['page_sequence'][] = __d('questionnaires', 'please set at least one question.');
		} else {
			$validationErrors = array();
			$this->QuestionnaireQuestion = ClassRegistry::init('Questionnaires.QuestionnaireQuestion', true);
			foreach ($this->data['QuestionnaireQuestion'] as $qIndex => $question) {
				// 質問データバリデータ
				$this->QuestionnaireQuestion->create();
				$this->QuestionnaireQuestion->set($question);
				$options['questionIndex'] = $qIndex;
				if (! $this->QuestionnaireQuestion->validates($options)) {
					$validationErrors['QuestionnaireQuestion'][$qIndex] = $this->QuestionnaireQuestion->validationErrors;
				}
			}
			$this->validationErrors += $validationErrors;
		}
		return true;
	}
/**
 * saveQuestionnairePage
 * save QuestionnairePage data
 *
 * @param array &$questionnairePages questionnaire pages
 * @throws InternalErrorException
 * @return bool
 */
	public function saveQuestionnairePage(&$questionnairePages) {
		$this->loadModels([
			'QuestionnaireQuestion' => 'Questionnaires.QuestionnaireQuestion',
		]);

		// QuestionnairePageが単独でSaveされることはない
		// 必ず上位のQuestionnaireのSaveの折に呼び出される
		// なので、$this->setDataSource('master');といった
		// 決まり処理は上位で行われる
		// ここでは行わない

		foreach ($questionnairePages as &$page) {
			// アンケートは履歴を取っていくタイプのコンテンツデータなのでSave前にはID項目はカット
			// （そうしないと既存レコードのUPDATEになってしまうから）
			$page = Hash::remove($page, 'QuestionnairePage.id');
			$this->create();
			if (! $this->save($page, false)) {	// validateは上位のquestionnaireで済んでいるはず
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			$pageId = $this->id;

			$page = Hash::insert($page, 'QuestionnaireQuestion.{n}.questionnaire_page_id', $pageId);
			// もしもQuestionやChoiceのsaveがエラーになった場合は、
			// QuestionやChoiceのほうでInternalExceptionErrorが発行されるのでここでは何も行わない
			$this->QuestionnaireQuestion->saveQuestionnaireQuestion($page['QuestionnaireQuestion']);
		}
		return true;
	}
}
