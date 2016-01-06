<?php
/**
 * QuestionnairePage::getDefaultPage()のテスト
 *
 * @property QuestionnairePage $QuestionnairePage
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
 * QuestionnairePage::getDefaultPage()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnairePage
 */
class QuestionnaireGetDefaultPageTest extends NetCommonsGetTest {

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
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'QuestionnairePage';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'getDefaultPage';

/**
 * getDefaultPageのテスト
 *
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testGetDefaultPage($expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method();
		// Questionは省く
		$result = Hash::remove($result, 'QuestionnaireQuestion');

		//チェック
		$this->assertEquals($result, $expected);
	}

/**
 * getDefaultPageのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		$expect = array(
			'page_title' => __d('questionnaires', 'First Page'),
			'route_number' => 0,
			'page_sequence' => 0,
			'key' => '',
		);
		return array(
			array($expect),
		);
	}

}
