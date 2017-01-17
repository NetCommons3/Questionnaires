<?php
/**
 * NetCommonsSaveTest
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * NetCommonsSaveTest
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\NetCommons\TestSuite
 * @codeCoverageIgnore
 */
class QuestionnairesSaveTest extends NetCommonsModelTestCase {

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = '';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = '';

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

		//チェック用データ取得
		if (isset($data[$model][0]['id'])) {
			$before = $this->$model->find('first', array(
				'recursive' => -1,
				'conditions' => array('id' => $data[$this->$model->alias][0]['id']),
			));
		}

		//テスト実行
		$result = $this->$model->$method($data[$model]);
		$this->assertNotEmpty($result);

		//idのチェック
		if (isset($data[$this->$model->alias][0]['id'])) {
			$id = $data[$this->$model->alias][0]['id'];
		} else {
			$id = $this->$model->getLastInsertID();
		}

		//登録データ取得
		$actual = $this->$model->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $id),
		));

		if (isset($data[$this->$model->alias][0]['id'])) {
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'modified');
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'modified_user');
		} else {
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'created');
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'created_user');
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'modified');
			$actual[$this->$model->alias] = Hash::remove($actual[$this->$model->alias], 'modified_user');

			if ($this->$model->hasField('key') && !isset($data[$this->$model->alias][0]['key'])) {
				$data[$this->$model->alias][0]['key'] =
					OriginalKeyBehavior::generateKey($this->$model->name, $this->$model->useDbConfig);
			}
			$before[$this->$model->alias] = array();
		}

		$expected[$this->$model->alias] = Hash::merge(
			$before[$this->$model->alias],
			$data[$this->$model->alias][0],
			array(
				'id' => $id,
				'is_origin' => true,
				'is_translation' => false,
				'is_original_copy' => false,
			)
		);
		$expected[$this->$model->alias] = Hash::remove($expected[$this->$model->alias], 'modified');
		$expected[$this->$model->alias] = Hash::remove($expected[$this->$model->alias], 'modified_user');
		$expected = Hash::remove($expected, $this->$model->alias . '.QuestionnaireQuestion');
		$expected = Hash::remove($expected, $this->$model->alias . '.QuestionnaireChoice');

		$this->assertEquals($expected[$this->$model->alias], $actual[$this->$model->alias]);
	}

/**
 * SaveのExceptionErrorテスト
 *
 * @param array $data 登録データ
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnExceptionError
 * @return void
 */
	public function testSaveOnExceptionError($data, $mockModel, $mockMethod) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);

		$this->setExpectedException('InternalErrorException');
		$this->$model->$method($data);
	}

/**
 * SaveのValidationErrorテスト
 *
 * @param array $data 登録データ
 * @param array $options validateメソッドに渡すオプション配列
 * @param string $mockModel Mockのモデル
 * @param string $mockMethod Mockのメソッド
 * @dataProvider dataProviderSaveOnValidationError
 * @return void
 */
	public function testSaveOnValidationError($data, $options, $mockModel, $mockMethod = 'validates') {
		$model = $this->_modelName;

		$this->_mockForReturnFalse($model, $mockModel, $mockMethod);
		$this->$model->create();
		$this->$model->set($data);
		$result = $this->$model->$mockMethod($options);
		$this->assertFalse($result);
	}

/**
 * Validatesのテスト
 *
 * @param array $datas 登録データ
 * @param array $options validateメソッドに渡すオプション配列
 * @param string $field フィールド名
 * @param string $value セットする値
 * @param string $message エラーメッセージ
 * @param array $overwrite 上書きするデータ
 * @dataProvider dataProviderValidationError
 * @return void
 */
	public function testValidationError(
		$datas, $options, $field, $value, $message, $overwrite = array()) {
		$model = $this->_modelName;

		// 試験データは１つ分しか来ないけど
		foreach ($datas[$model] as $dataEntity) {
			$data = $dataEntity;
			if (is_null($value)) {
				unset($data[$field]);
			} else {
				$data[$field] = $value;
			}
			$data = Hash::merge($data, $overwrite);
			//validate処理実行
			$this->$model->set($data);

			$result = $this->$model->validates($options);
			$this->assertFalse($result);

			$this->assertEquals($this->$model->validationErrors[$field][0], $message);
		}
	}

}
