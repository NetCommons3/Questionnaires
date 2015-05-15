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
		'NetCommons.Publishable',
		'NetCommons.OriginalKey',
	);

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
		'origin_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'is_active' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'is_latest' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
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
		'page_sequence' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
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
		'QuestionnaireQuestion' => array(
			'className' => 'QuestionnaireQuestion',
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
		$this->loadModels([
			'QuestionnaireQuestion' => 'Questionnaires.QuestionnaireQuestion',
		]);

		$page['page_title'] = __d('questionnaires', 'First Page');
		$page['page_sequence'] = 0;
		$page['origin_id'] = 0;
		$page['QuestionnaireQuestion'][0] = $this->QuestionnaireQuestion->getDefaultQuestion();
		return $page;
	}

/**
 * setPageToQuestionnaire
 * setup page data to questionnaire array
 *
 * @param array &$questionnaire questionnaire data
 * @return void
 */
	public function setPageToQuestionnaire(&$questionnaire) {
		$this->loadModels([
			'QuestionnaireQuestion' => 'Questionnaires.QuestionnaireQuestion',
		]);

		// ページデータがアンケートデータの中にない状態でここが呼ばれている場合、
		if (!isset($questionnaire['QuestionnairePage'])) {
			/*
			$questionnaire['Questionnaire']['page_count'] = $this->find('count', array(
				'conditions' => array(
					'questionnaire_id' => $questionnaire['Questionnaire']['id'],
				),
				'recursive' => -1
			));
			$questionnaire['Questionnaire']['question_count'] = $this->QuestionnaireQuestion->find('count', array(
				'conditions' => array(
					'questionnaire_page_id' => $questionnaire['Questionnaire']['id']
				),
			));
			return;
			*/
			$pages = $this->find('all', array(
				'conditions' => array(
					'questionnaire_id' => $questionnaire['Questionnaire']['id'],
				),
				'recursive' => -1));
				$questionnaire['QuestionnairePage'] = $pages[0]['QuestionnairePage'];
		}

		// ページシーケンスによって並べ替え
		$pages = Hash::sort($questionnaire['QuestionnairePage'], '{n}.page_sequence', 'asc');
		$questionnaire['QuestionnairePage'] = $pages;

		foreach ($questionnaire['QuestionnairePage'] as &$page) {
			if (isset($page['id'])) {
				$this->QuestionnaireQuestion->setQuestionToPage($questionnaire, $page);
			}
			$questionnaire['Questionnaire']['page_count']++;
		}
	}

/**
 * saveQuestionnairePage
 * save QuestionnairePage data
 *
 * @param int $questionnaireId questionnaire id
 * @param int $status status
 * @param array &$questionnairePages questionnaire pages
 * @throws InternalErrorException
 * @return bool
 */
	public function saveQuestionnairePage($questionnaireId, $status, &$questionnairePages) {
		$this->loadModels([
			'QuestionnaireQuestion' => 'Questionnaires.QuestionnaireQuestion',
			'QuestionnaireChoice' => 'Questionnaires.QuestionnaireChoice',
		]);

		foreach ($questionnairePages as &$p) {
			$savePage = $this->_setupSaveData($p, $status);
			$savePage['questionnaire_id'] = $questionnaireId;
			$this->create();
			if (! $this->save($savePage)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			$pageId = $this->id;

			if (isset($p['QuestionnaireQuestion'])) {
				$this->QuestionnaireQuestion->saveQuestionnaireQuestion($pageId, $status, $p['QuestionnaireQuestion']);
			}
		}
	}
}
