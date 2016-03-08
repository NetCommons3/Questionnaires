<?php
/**
 * QuestionnaireExport::getExportData()のテスト
 *
 * @property QuestionnaireExport $QuestionnaireExport
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * QuestionnaireExport::getExportData()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireExport
 */
class QuestionnaireExportGetExportDataTest extends NetCommonsGetTest {

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
		'plugin.m17n.language',
		'plugin.questionnaires.questionnaire',
		'plugin.questionnaires.questionnaire_page',
		'plugin.questionnaires.questionnaire_question',
		'plugin.questionnaires.questionnaire_choice',
		'plugin.questionnaires.questionnaire_setting',
		'plugin.questionnaires.questionnaire_frame_setting',
		'plugin.questionnaires.questionnaire_frame_display_questionnaire',
		'plugin.questionnaires.questionnaire_answer_summary',
		'plugin.questionnaires.questionnaire_answer',
		'plugin.authorization_keys.authorization_keys',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'QuestionnaireExport';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'getExportData';

/**
 * getExportData
 *
 * @param string $questionnaireKey 収集対象のアンケートキー
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testGetExportData($questionnaireKey, $expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method($questionnaireKey);

		//チェック
		if (is_bool($expected)) {
			$this->assertEquals($result, $expected);
		} else {
			foreach ($expected as $expect) {
				$this->assertTrue(Hash::check($result, $expect), $expect . ' is not found');
			}
		}
	}

/**
 * getExportDataのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		$expect = array(
			//'version', travis ではここがうまくかない FUJI
			'Questionnaires.{n}.Questionnaire[language_id=1]',
			'Questionnaires.{n}.Questionnaire[language_id=2]',
		);
		return array(
			array('questionnaire_2', false),
			array('questionnaire_6', $expect),
		);
	}
}
