<?php
/**
 * QuestionnaireFrameSettingGet Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireTestBase', 'Questionnaires.Test/Case/Model');

/**
 * Summary for QuestionnaireFrameSetting Test Case
 */
class QuestionnaireFrameSettingGetTest extends QuestionnaireTestBase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
	}

/**
 * getQuestionnaireFrameSetting method
 *
 * @return void
 */
	public function testgetQuestionnaireFrameSetting() {
		// フレーム設定が存在しない場合のデフォルト値取得試験
		//初期処理
		$this->setUp();

		$frameKey = '88554c2b94b5db80d4ec975b63ec';

		$expected = array(
			QuestionnairesComponent::DISPLAY_TYPE_LIST,
			QuestionnairesComponent::QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE,
			'modified',
			'DESC'
		);

		// 処理実行
		$result = $this->QuestionnaireFrameSetting->getQuestionnaireFrameSetting($frameKey);

		// テスト実施
		$this->_assertArray($expected, $result);

		//終了処理
		$this->tearDown();
	}

/**
 * getQuestionnaireFrameSetting method
 *
 * @return void
 */
	public function testgetQuestionnaireFrameSetting1() {
		// フレーム設定が存在する場合の試験(QUESTIONNAIRE_SORT_CREATED)
		//初期処理
		$this->setUp();

		$frameKey = 'frame_1';

		$expected = array(
			QuestionnairesComponent::DISPLAY_TYPE_SINGLE, //QuestionnairesComponent::DISPLAY_TYPE_LIST,
			1,
			'created',
			'DESC'
		);

		// 処理実行
		$result = $this->QuestionnaireFrameSetting->getQuestionnaireFrameSetting($frameKey);

		// テスト実施
		$this->_assertArray($expected, $result);

		//終了処理
		$this->tearDown();
	}

/**
 * getQuestionnaireFrameSetting method
 *
 * @return void
 */
	public function testgetQuestionnaireFrameSetting2() {
		// フレーム設定が存在する場合の試験(QUESTIONNAIRE_SORT_MODIFIED)
		//初期処理
		$this->setUp();

		$frameKey = 'frame_2';

		$expected = array(
			QuestionnairesComponent::DISPLAY_TYPE_LIST,
			1,
			'modified',
			'DESC'
		);

		// 処理実行
		$result = $this->QuestionnaireFrameSetting->getQuestionnaireFrameSetting($frameKey);

		// テスト実施
		$this->_assertArray($expected, $result);

		//終了処理
		$this->tearDown();
	}

/**
 * getQuestionnaireFrameSetting method
 *
 * @return void
 */
	public function testgetQuestionnaireFrameSetting3() {
		// フレーム設定が存在する場合の試験(QUESTIONNAIRE_SORT_TITLE)
		//初期処理
		$this->setUp();

		$frameKey = 'frame_3';

		$expected = array(
			QuestionnairesComponent::DISPLAY_TYPE_LIST,
			1,
			'title',
			'ASC'
		);

		// 処理実行
		$result = $this->QuestionnaireFrameSetting->getQuestionnaireFrameSetting($frameKey);

		// テスト実施
		$this->_assertArray($expected, $result);

		//終了処理
		$this->tearDown();
	}

/**
 * getQuestionnaireFrameSetting method
 *
 * @return void
 */
	public function testgetQuestionnaireFrameSetting4() {
		// フレーム設定が存在する場合の試験(QUESTIONNAIRE_SORT_END)
		//初期処理
		$this->setUp();

		$frameKey = 'frame_4';

		$expected = array(
			QuestionnairesComponent::DISPLAY_TYPE_LIST,
			1,
			'end_period',
			'ASC'
		);

		// 処理実行
		$result = $this->QuestionnaireFrameSetting->getQuestionnaireFrameSetting($frameKey);

		// テスト実施
		$this->_assertArray($expected, $result);

		//終了処理
		$this->tearDown();
	}

/**
 * getQuestionnaireFrameSetting method
 *
 * @return void
 */
	public function testgetDefaultFrameSetting() {
		//初期処理
		$this->setUp();

		//期待値の生成
		$expected = array(
			'QuestionnaireFrameSetting' => array(
				'display_type' => QuestionnairesComponent::DISPLAY_TYPE_LIST,
				'display_num_per_page' => QuestionnairesComponent::QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE,
				'sort_type' => QuestionnairesComponent::DISPLAY_SORT_TYPE_NEW_ARRIVALS,
				'id' => ''
			)
		);

		// 処理実行
		$result = $this->QuestionnaireFrameSetting->getDefaultFrameSetting();

		// テスト実施
		$this->_assertArray($expected, $result);

		//終了処理
		$this->tearDown();
	}

}
