<?php
/**
 * QuestionnaireBlocksSetting Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireTestBase', 'Questionnaires.Test/Case/Model');

/**
 * Summary for QuestionnaireBlocksSetting Test Case
 */
class QuestionnaireBlocksSettingTest extends QuestionnaireTestBase {

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
 * __assertValidationError
 *
 * @param string $field Field name
 * @param array $data Save data
 * @param array $expected Expected value
 * @return void
 */
	private function __assertValidationError($field, $data, $expected) {
		//初期処理
		$this->setUp();

		//validate処理実行
		$this->QuestionnaireBlocksSetting->set($data);
		$result = $this->QuestionnaireBlocksSetting->validates();

		//戻り値チェック
		$expectMessage = 'Expect `' . $field . '` field, error data: ' . print_r($data, true);
		$this->assertFalse($result, $expectMessage);

		//validationErrorsチェック
		$this->assertEquals($this->QuestionnaireBlocksSetting->validationErrors, $expected);

		//終了処理
		$this->tearDown();
	}

/**
 * validate method
 *  validate試験'block_id'が空白の場合(エラー)
 * @return void
 */
	public function testbeforeValidate1() {
		$field = 'block_id';

		//データ生成
		$data = array(
			'block_id' => '',
		);
		//期待値
		$expected = array(
				$field => array(__('numeric')));

		//テスト実施
		$this->__assertValidationError($field, $data, $expected);
	}

/**
 * saveQuestionnaireBlocksSetting method
 * validationエラーのときfalseを返却
 * @return void
 */
	public function testsaveQuestionnaireBlocksSetting() {
		//初期処理
		$this->setUp();

		//データ生成
		$data = array(
			'BlockRolePermission' => array(
				'alias' => array(
					'block_id' => 1
				)
			)
		);
		// 処理実行
		$result = $this->QuestionnaireBlocksSetting->saveQuestionnaireBlocksSetting($data);

		// テスト実施
		$this->assertFalse($result);

		//終了処理
		$this->tearDown();
	}

/**
 * saveQuestionnaireBlocksSetting method
 * エラー無しのときtrueを返却
 * @return void
 */
	public function testOKsaveQuestionnaireBlocksSetting() {
		//初期処理
		$this->setUp();

		//データ生成
		$data = array(
			'BlockRolePermission' => array(
				'alias' => array(
					'block_id' => array(
						'roles_room_id' => 1,
						'block_key' => 'aaa',
						'permission' => 1,
					)
		)));
		// 処理実行
		$result = $this->QuestionnaireBlocksSetting->saveQuestionnaireBlocksSetting($data);

		// テスト実施
		$this->assertTrue($result);

		//終了処理
		$this->tearDown();
	}

/**
 * validateQuestionnaireBlocksSetting method
 * validationエラー無しのときtrueを返却
 * @return void
 */
	public function testvalidateQuestionnaireBlocksSetting() {
		//初期処理
		$this->setUp();

		//データ生成
		$data = array(
			array(
				'block_id' => 1
			)
		);

		// 処理実行
		$result = $this->QuestionnaireBlocksSetting->validateQuestionnaireBlocksSetting($data);

		// テスト実施
		$this->assertTrue($result);

		//終了処理
		$this->tearDown();
	}
}
