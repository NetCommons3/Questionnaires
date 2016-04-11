<?php
/**
 * QuestionnaireAnswersController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * QuestionnaireAnswersController Test Case
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\AuthorizationKeys\Test\Case\Controller
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class QuestionnaireAnswersControllerPostTest extends NetCommonsControllerTestCase {

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
		'plugin.questionnaires.questionnaire_answer',
		'plugin.authorization_keys.authorization_keys',
	);

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'questionnaires';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'questionnaire_answers';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->generateNc(Inflector::camelize($this->_controller));
	}

/**
 * アクションのPOSTテスト
 * KeyAuthへPost
 *
 * @return void
 */
	public function testKeyAuthPost() {
		$controller = $this->generate('Questionnaires.QuestionnaireAnswers', array(
			'components' => array(
				'Auth' => array('user'),
				'Session',
				'Security',
				'NetCommons.Permission',
				'Questionnaires.Questionnaires',
				'Questionnaires.QuestionnairesOwnAnswer',
				'AuthorizationKeys.AuthorizationKey'
			)
		));
		$data = array(
			'data' => array(
				'Frame' => array('id' => 6),
				'Block' => array('id' => 2),
				'AuthorizationKeys' => array('key' => 'test')
			)
		);
		$controller->AuthorizationKey->expects($this->any())
			->method('check')
			->will(
				$this->returnValue(true));

		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		$this->_testPostAction('post', $data, array('action' => 'key_auth', 'frame_id' => 6, 'block_id' => 2, 'key' => 'questionnaire_6'));
		$result = $this->headers['Location'];

		$this->assertTextContains('questionnaire_6', $result);

		TestAuthGeneral::logout($this);
	}

/**
 * アクションのPOSTテスト
 * ImgAuthへPost
 *
 * @return void
 */
	public function testImgAuthPost() {
		$controller = $this->generate('Questionnaires.QuestionnaireAnswers', array(
			'components' => array(
				'Auth' => array('user'),
				'Session',
				'Security',
				'NetCommons.Permission',
				'Questionnaires.Questionnaires',
				'Questionnaires.QuestionnairesOwnAnswer',
				'AuthorizationKeys.AuthorizationKey',
				'VisualCaptcha.VisualCaptcha'
			)
		));
		$data = array(
			'data' => array(
				'Frame' => array('id' => 6),
				'Block' => array('id' => 2),
				'VisualCaptcha' => array('test' => 'test')	// Mock使うんでなんでもよい
			)
		);
		$controller->VisualCaptcha->expects($this->any())
			->method('check')
			->will(
				$this->returnValue(true));

		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_GENERAL_USER);

		$this->_testPostAction('post', $data, array('action' => 'img_auth', 'frame_id' => 6, 'block_id' => 2, 'key' => 'questionnaire_8'));
		$result = $this->headers['Location'];

		$this->assertTextContains('questionnaire_8', $result);

		TestAuthGeneral::logout($this);
	}

/**
 * アクションのPOSTテスト
 * 回答Post
 *
 * @param array $data 投入データ
 * @param int $role ロール
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderAnswerPost
 * @return void
 */
	public function testAnswerPost($data, $role, $urlOptions, $assert, $exception = null, $return = 'view') {
		//ログイン
		if (isset($role)) {
			TestAuthGeneral::login($this, $role);
		}

		//テスト実施
		$result = $this->_testPostAction('post', $data, Hash::merge(array('action' => 'view'), $urlOptions), $exception, $return);

		//正常の場合、リダイレクト
		if (! $exception) {
			if ($assert == 'confirm') {
				$header = $this->controller->response->header();
				$this->assertNotEmpty($header['Location']);
			} elseif ($assert == 'err') {
				$this->assertTextContains('Question_1', $result);
			} else {
				$this->assertTextContains($assert, $result);
			}
		}

		//ログアウト
		if (isset($role)) {
			TestAuthGeneral::logout($this);
		}
	}

/**
 * アクションのPOSTテスト
 * 回答Postデータプロバイダ
 *
 * @return void
 */
	public function dataProviderAnswerPost() {
		$data = array(
			'data' => array(
				'Frame' => array('id' => 6),
				'Block' => array('id' => 2),
				'QuestionnairePage' => array('page_sequence' => 0),
				'QuestionnaireAnswer' => array(
					'questionnaire_2' => array(
						array(
							'answer_value' => '|choice_2:choice label1',
							'questionnaire_question_key' => 'qKey_1')
					)))
		);
		$errData = $data;
		$errData['data']['QuestionnaireAnswer']['questionnaire_2'][0]['answer_value'] = '|choice_800:nainainai';
		$skipData = array(
			'data' => array(
				'Frame' => array('id' => 6),
				'Block' => array('id' => 2),
				'QuestionnairePage' => array('page_sequence' => 0),
				'QuestionnaireAnswer' => array(
					'questionnaire_4' => array(
						array(
							'answer_value' => '|choice_6:choice label3',
							'questionnaire_question_key' => 'qKey_3')
					)))
		);
		$skipNoSelectData = $skipData;
		$skipNoSelectData['data']['QuestionnaireAnswer']['questionnaire_4'][0]['answer_value'] = '';

		return array(
			array(
				'data' => $data,
				'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => 6, 'block_id' => 2, 'key' => 'questionnaire_2'),
				'assert' => 'confirm'),
			array(
				'data' => $errData,
				'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => 6, 'block_id' => 2, 'key' => 'questionnaire_2'),
				'assert' => 'err'),
			array(
				'data' => $skipData,
				'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
				'urlOptions' => array('frame_id' => 6, 'block_id' => 2, 'key' => 'questionnaire_4'),
				'assert' => 'name="data[QuestionnairePage][page_sequence]" value="4"'),
			array(
				'data' => $skipNoSelectData,
				'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
				'urlOptions' => array('frame_id' => 6, 'block_id' => 2, 'key' => 'questionnaire_4'),
				'assert' => 'name="data[QuestionnairePage][page_sequence]" value="1"'),
		);
	}

/**
 * アクションのPOSTテスト
 * ConfirmhへPost
 *
 * @return void
 */
	public function testConfirmPost() {
		//ログイン
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR);

		$data = array(
			'data' => array(
				'Frame' => array('id' => 6),
				'Block' => array('id' => 2),
			)
		);
		$this->_testPostAction('post', $data, array('action' => 'confirm', 'frame_id' => 6, 'block_id' => 2, 'key' => 'questionnaire_12'));
		$result = $this->headers['Location'];
		$this->assertTextContains('thanks', $result);
		TestAuthGeneral::logout($this);
	}

}