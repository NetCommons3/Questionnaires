<?php
/**
 * QuestionnaireFrameDisplayQuestionnaire::saveFrameDisplayQuestionnaire()のテスト
 *
 * @property QuestionnaireFrameDisplayQuestionnaire $QuestionnaireFrameDisplayQuestionnaire
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsSaveTest', 'NetCommons.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * QuestionnaireFrameDisplayQuestionnaire::saveFrameDisplayQuestionnaire()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireFrameDisplayQuestionnaire
 */
class QuestionnaireSaveFrameDisplayQuestionnaireTest extends NetCommonsSaveTest {

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
		'plugin.questionnaires.questionnaire_setting',
		'plugin.questionnaires.questionnaire_frame_setting',
		'plugin.questionnaires.questionnaire_frame_display_questionnaire',
		'plugin.questionnaires.questionnaire_page',
		'plugin.questionnaires.questionnaire_question',
		'plugin.questionnaires.questionnaire_choice',
		'plugin.questionnaires.questionnaire_answer_summary',
		'plugin.authorization_keys.authorization_keys'
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'QuestionnaireFrameDisplayQuestionnaire';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'saveFrameDisplayQuestionnaire';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Current::$current['Frame']['key'] = 'frame_3';
	}
/**
 * テストDataの取得
 *
 * @param string $key pageKey
 * @return array
 */
	protected function _getData($displayType = QuestionnairesComponent::DISPLAY_TYPE_SINGLE) {
		$data = array(
			'QuestionnaireFrameSetting' => array(
				'display_type' => $displayType,
				'display_num_per_page' => 10,
				'sort_type' => 'Questionnaire.modified DESC',
			),
			'List' => array(
				'QuestionnaireFrameDisplayQuestionnaire' => array(
					array('is_display' => '0', 'questionnaire_key' => 'questionnaire_2'),
					array('is_display' => '1', 'questionnaire_key' => 'questionnaire_4'),
					array('is_display' => '1', 'questionnaire_key' => 'questionnaire_6')
				)
			),
			'Single' => array(
				'QuestionnaireFrameDisplayQuestionnaire' => array(
					'questionnaire_key' => 'questionnaire_2',
				)
			)
		);
		return $data;
	}

/**
 * Saveのテスト
 *
 * @param array $data 登録データ
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($data) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->_mockForReturn($model, 'Questionnaires.Questionnaire', 'getBaseCondition', array());

		//チェック用データ取得
		$before = $this->$model->find('all', array(
			'recursive' => -1,
			'conditions' => array('frame_key' => Current::read('Frame.key')),
		));

		//テスト実行
		$result = $this->$model->$method($data);
		$this->assertNotEmpty($result);

		//登録データ取得
		$actual = $this->$model->find('all', array(
			'recursive' => -1,
			'conditions' => array('frame_key' => Current::read('Frame.key')),
			'order' => array('questionnaire_key asc'),
		));
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.created');
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.created_user');
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified');
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.modified_user');
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.id');
		$actual = Hash::remove($actual, '{n}.' . $this->$model->alias . '.frame_key');

		if ($data['QuestionnaireFrameSetting']['display_type'] == QuestionnairesComponent::DISPLAY_TYPE_SINGLE) {
			$expected[0] = Hash::extract($data, 'Single');
		} else {
			$expected = $before;
			foreach ($data['List']['QuestionnaireFrameDisplayQuestionnaire'] as $value) {
				if ($value['is_display']) {
					$questionnaire = Hash::extract($expected, '{n}.' . $this->$model->alias . '[questionnaire_key=' . $value['questionnaire_key'] . ']');
					if (! $questionnaire) {
						$expected[] = array('QuestionnaireFrameDisplayQuestionnaire' => array('questionnaire_key' => $value['questionnaire_key']));
					}
				} else {
					$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '[questionnaire_key=' . $value['questionnaire_key'] . ']');
				}
			}
			$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '.created');
			$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '.created_user');
			$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '.modified');
			$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '.modified_user');
			$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '.id');
			$expected = Hash::remove($expected, '{n}.' . $this->$model->alias . '.frame_key');
			$expected = Hash::sort($expected, '{n}.' . $this->$model->alias . '.questionnaire_key');
		}

		$this->assertEquals($expected, $actual);
	}

/**
 * SaveのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return void
 */
	public function dataProviderSave() {
		return array(
			array($this->_getData(QuestionnairesComponent::DISPLAY_TYPE_SINGLE)),
			array($this->_getData(QuestionnairesComponent::DISPLAY_TYPE_LIST)),
		);
	}

/**
 * SaveのExceptionErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return void
 */
	public function dataProviderSaveOnExceptionError() {
		return array(
			array(
				$this->_getData(QuestionnairesComponent::DISPLAY_TYPE_SINGLE),
				'Questionnaires.QuestionnaireFrameDisplayQuestionnaire',
				'save'),
			array(
				$this->_getData(QuestionnairesComponent::DISPLAY_TYPE_LIST),
				'Questionnaires.QuestionnaireFrameDisplayQuestionnaire',
				'deleteAll'),
			array(
				$this->_getData(QuestionnairesComponent::DISPLAY_TYPE_LIST),
				'Frames.Frame',
				'updateAll'),
		);
	}
/**
 * SaveのValidationErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *
 * @return void
 */
	public function dataProviderSaveOnValidationError() {
		$data = $this->_getData(QuestionnairesComponent::DISPLAY_TYPE_SINGLE);
		return array(
			array($data, 'Questionnaires.QuestionnaireFrameDisplayQuestionnaire'),
		);
	}
/**
 * ValidationErrorのDataProvider
 *
 * ### 戻り値
 *  - field フィールド名
 *  - value セットする値
 *  - message エラーメッセージ
 *  - overwrite 上書きするデータ
 *
 * @return void
 */
	public function dataProviderValidationError() {
		return array(
			array($this->_getData(), 'Single', null,
				__d('net_commons', 'Invalid request.')),
		);
	}

}
