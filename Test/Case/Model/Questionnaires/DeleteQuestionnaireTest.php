<?php
/**
 * Questionnaires::deleteQuestionnaire()のテスト
 *
 * @property Questionnaires $Questionnaires
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowDeleteTest', 'Workflow.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * Questionnaires::deleteQuestionnaire()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\Questionnaire
 */
class QuestionnaireDeleteQuestionnaireTest extends WorkflowDeleteTest {

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'questionnaires';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.questionnaires.questionnaire',
		'plugin.questionnaires.questionnaire_page',
		'plugin.questionnaires.questionnaire_question',
		'plugin.questionnaires.questionnaire_choice',
		'plugin.questionnaires.questionnaire_answer_summary',
		'plugin.questionnaires.questionnaire_answer',
		'plugin.questionnaires.questionnaire_setting',
		'plugin.questionnaires.questionnaire_frame_setting',
		'plugin.questionnaires.questionnaire_frame_display_questionnaire',
		'plugin.questionnaires.questionnaire_setting',
		'plugin.authorization_keys.authorization_keys',
		'plugin.workflow.workflow_comment',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'Questionnaire';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'deleteQuestionnaire';

/**
 * テストDataの取得
 *
 * @param int $id questionnaire id
 * @param string $questionnaireKey questionnaire key
 * @return array
 */
	private function __getData($id, $questionnaireKey) {
		$data = array(
			'Block' => array(
				'id' => '2',
				'key' => 'block_1',
			),
			'Questionnaire' => array(
				'id' => $id,
				'key' => $questionnaireKey,
			),
		);
		return $data;
	}
/**
 * テストAssociationDataの取得
 *
 * @param string $questionnaireKey questionnaire key
 * @return array
 */
	private function __getAssociation($questionnaireKey) {
		$association = array(
			'QuestionnaireFrameDisplayQuestionnaire' => array(
				'questionnaire_key' => $questionnaireKey,
			),
			'QuestionnaireAnswerSummary' => array(
				'questionnaire_key' => $questionnaireKey,
			),
		);
		return $association;
	}

/**
 * DeleteのDataProvider
 *
 * #### 戻り値
 *  - data: 削除データ
 *  - associationModels: 削除確認の関連モデル array(model => conditions)
 *
 * @return array
 */
	public function dataProviderDelete() {
		$data = $this->__getData(2, 'questionnaire_2');
		$association = $this->__getAssociation('questionnaire_2');
		return array(
			array(
				$data,
				$association),
		);
	}
/**
 * NoExistDeleteのテスト
 *
 * @param array|string $data 削除データ
 * @param array $associationModels 削除確認の関連モデル array(model => conditions)
 * @dataProvider dataProviderNoExistDataDelete
 * @return void
 */
	public function testNoExistDataDelete($data, $associationModels = null) {
		$model = $this->_modelName;
		$method = $this->_methodName;
		if (! $associationModels) {
			$associationModels = array();
		}

		//テスト実行
		$result = $this->$model->$method($data);
		$this->assertTrue($result);

		if (isset($data[$this->$model->alias]['key'])) {
			$keyConditions = array('key' => $data[$this->$model->alias]['key']);
		} elseif (! is_array($data)) {
			$keyConditions = array('key' => $data);
		} else {
			$keyConditions = Hash::flatten($data);
		}

		//チェック
		$count = $this->$model->find('count', array(
			'recursive' => -1,
			'conditions' => $keyConditions,
		));
		$this->assertEquals(0, $count);

		foreach ($associationModels as $assocModel => $conditions) {
			$count = $this->$model->$assocModel->find('count', array(
				'recursive' => -1,
				'conditions' => $conditions,
			));
			$this->assertEquals(0, $count);
		}
	}

/**
 * DeleteのDataProvider
 *
 * #### 戻り値
 *  - data: 削除データ
 *  - associationModels: 削除確認の関連モデル array(model => conditions)
 *
 * @return array
 */
	public function dataProviderNoExistDataDelete() {
		$data = $this->__getData(999999, 'questionnaire_0');
		$association = $this->__getAssociation('questionnaire_0');
		return array(
			array($data, $association),
		);
	}

/**
 * ExceptionErrorのDataProvider
 *
 * #### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return void
 */
	public function dataProviderDeleteOnExceptionError() {
		$data = $this->__getData(2, 'questionnaire_2');
		return array(
			array($data, 'Questionnaires.Questionnaire', 'deleteAll'),
			array($data, 'Questionnaires.QuestionnaireFrameDisplayQuestionnaire', 'deleteAll'),
			array($data, 'Questionnaires.QuestionnaireAnswerSummary', 'deleteAll'),
		);
	}

}
