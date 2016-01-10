<?php
/**
 * QuestionnaireChoice::getDefaultChoice()のテスト
 *
 * @property QuestionnaireChoice $QuestionnaireChoice
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
 * QuestionnaireChoice::getDefaultChoice()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireChoice
 */
class QuestionnaireGetDefaultChoiceTest extends NetCommonsGetTest {

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
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'QuestionnaireChoice';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'getDefaultChoice';

/**
 * getDefaultChoiceのテスト
 *
 * @param array $expected 期待値（取得したキー情報）
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testGetDefaultChoice($expected) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method();

		//チェック
		$this->assertEquals($result, $expected);
	}

/**
 * getDefaultChoiceのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		$expect = array(
			'choice_sequence' => 0,
			'matrix_type' => QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX,
			'choice_label' => __d('questionnaires', 'new choice') . '1',
			'other_choice_type' => QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED,
			'graph_color' => '#f38631',
			'skip_page_sequence' => QuestionnairesComponent::SKIP_GO_TO_END
		);
		return array(
			array($expect),
		);
	}

}
