<?php
/**
 * QuestionnaireFrameDisplayQuestionnaire::validateFrameDisplayQuestionnaire()のテスト
 *
 * @property QuestionnaireFrameDisplayQuestionnaire $QuestionnaireFrameDisplayQuestionnaire
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsValidateTest', 'NetCommons.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * QuestionnaireFrameDisplayQuestionnaire::validateFrameDisplayQuestionnaire()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireFrameDisplayQuestionnaire
 */
class QuestionnaireValidateFrameDisplayQuestionnaireTest extends NetCommonsValidateTest {

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
	protected $_methodName = 'validateFrameDisplayQuestionnaire';

/**
 * Validatesのテスト
 *
 * @param array $data 登録データ
 * @param string $field フィールド名
 * @param string $value セットする値
 * @param string $message エラーメッセージ
 * @param array $overwrite 上書きするデータ
 * @dataProvider dataProviderValidationError
 * @return void
 */
	public function testValidationError($data, $field, $value, $message, $overwrite = array()) {
		$model = $this->_modelName;

		//validate処理実行
		$result = $this->$model->validateFrameDisplayQuestionnaire($data);
		$this->assertFalse($result);

		if ($message) {
			if ($data['QuestionnaireFrameSetting']['display_type'] == QuestionnairesComponent::DISPLAY_TYPE_SINGLE) {
				$this->assertEquals($this->$model->validationErrors[$field][0], $message);
			} else {
				// FUJI 問題のあるレコードIndexを固定的に設定しているのは問題か
				$this->assertEquals($this->$model->validationErrors[2][$field][0], $message);
			}
		}
	}
/**
 * _getData
 *
 * @param int $displayType 表示形式
 * @return array
 */
	protected function _getData($displayType) {
		$data = array(
			'QuestionnaireFrameSetting' => array(
				'display_type' => $displayType,
				'display_num_per_page' => 10,
				'sort_type' => 0,
			),
			'List' => array(
				'QuestionnaireFrameDisplayQuestionnaire' => array(
					array('is_display' => 1, 'questionnaire_key' => 'questionnaire_2'),
					array('is_display' => 1, 'questionnaire_key' => 'questionnaire_4'),
					array('is_display' => 1, 'questionnaire_key' => '')
				)
			),
			'Single' => array(
				'QuestionnaireFrameDisplayQuestionnaire' => array(
					'questionnaire_key' => '',
				)
			)
		);
		return $data;
	}
/**
 * validateFrameDisplayQuestionnaireのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderValidationError() {
		return array(
			array($this->_getData(QuestionnairesComponent::DISPLAY_TYPE_SINGLE), 'questionnaire_key', '',
				__d('net_commons', 'Invalid request.')),
			array($this->_getData(QuestionnairesComponent::DISPLAY_TYPE_LIST), 'questionnaire_key', '',
				__d('net_commons', 'Invalid request.')),
		);
	}
}
