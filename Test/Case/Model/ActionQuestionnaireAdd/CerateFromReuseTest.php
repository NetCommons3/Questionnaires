<?php
/**
 * ActionQuestionnaireAdd::_createFromReuse()のテスト
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
 * ActionQuestionnaireAdd::_createFromReuse()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\ActionQuestionnaireAdd
 */
class ActionQuestionnaireAddCreateFromReuseTest extends NetCommonsGetTest {

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
		'plugin.questionnaires.block_setting_for_questionnaire',
		'plugin.questionnaires.questionnaire_frame_setting',
		'plugin.questionnaires.questionnaire_frame_display_questionnaire',
		'plugin.questionnaires.questionnaire_page',
		'plugin.questionnaires.questionnaire_question',
		'plugin.questionnaires.questionnaire_choice',
		'plugin.questionnaires.questionnaire_answer_summary',
		'plugin.questionnaires.questionnaire_answer',
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'ActionQuestionnaireAdd';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'getNewQuestionnaire';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Questionnaire = ClassRegistry::init('Questionnaires.Questionnaire');
		$this->Questionnaire->Behaviors->unload('AuthorizationKey');
		Current::$current['Block']['id'] = 2;
	}

/**
 * _createFromReuse()のテスト
 *
 * @param array $data POSTデータ
 * @dataProvider dataProviderGet
 *
 * @return void
 */
	public function testCreateFromReuse($data) {
		$this->ActionQuestionnaireAdd->create();
		$this->ActionQuestionnaireAdd->set($data);
		// getNewQuestionnaireを呼ぶことで_createNewが呼ばれる仕組み
		$result = $this->ActionQuestionnaireAdd->getNewQuestionnaire();
		$this->assertTrue(Hash::check($result, 'Questionnaire[title=questionnaire_4]'));
		for ($i = 0; $i < 7; $i++) {
			$this->assertTrue(Hash::check($result, 'QuestionnairePage.' . $i));
			$this->assertTrue(Hash::check($result, 'QuestionnairePage.' . $i . '.QuestionnaireQuestion.0'));
		}
	}
/**
 * testCreateFromReuseのDataProvider
 *
 * #### 戻り値
 *  - array 取得するキー情報
 *  - array 期待値 （取得したキー情報）
 *
 * @return array
 */
	public function dataProviderGet() {
		return array(
			array(
				array('ActionQuestionnaireAdd' => array(
					'create_option' => 'reuse',
					'past_questionnaire_id' => '4'
				)),
			),
		);
	}
}
