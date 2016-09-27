<?php
/**
 * ActionQuestionnaireAdd Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');
App::uses('TemporaryUploadFile', 'Files.Utility');
App::uses('UnZip', 'Files.Utility');
App::uses('WysiwygZip', 'Wysiwyg.Utility');

/**
 * Summary for ActionQuestionnaireAdd Model
 */
class ActionQuestionnaireAdd extends QuestionnairesAppModel {

/**
 * Use table config
 *
 * @var bool
 */
	public $useTable = 'questionnaires';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function beforeValidate($options = array()) {
		$this->validate = Hash::merge($this->validate, array(
			'create_option' => array(
				'rule' => array(
					'inList', array(
						QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW,
						QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_REUSE,
						QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_TEMPLATE)),
				'message' => __d('questionnaires', 'Please choose create option.'),
				'required' => true
			),
			'title' => array(
				'rule' => array(
					'requireWhen',
					'create_option',
					QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW),
				'message' => __d('net_commons', 'Please input %s.', __d('questionnaires', 'Title')),
				'required' => false,
			),
			'past_questionnaire_id' => array(
				'requireWhen' => array(
					'rule' => array(
						'requireWhen',
						'create_option',
						QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_REUSE),
					'message' => __d('questionnaires', 'Please select past questionnaire.'),
				),
				'checkPastQuestionnaire' => array(
					'rule' => array('checkPastQuestionnaire'),
					'message' => __d('questionnaires', 'Please select past questionnaire.'),
				),
			),
		));

		return parent::beforeValidate($options);
	}

/**
 * createQuestionnaire
 * アンケートデータを作成する
 *
 * @param array $data 作成するアンケートデータ
 * @return array|bool
 */
	public function createQuestionnaire($data) {
		// 渡されたQuestionnaireデータを自Modelデータとする
		$this->set($data);
		// データチェック
		if ($this->validates()) {
			// Postデータの内容に問題がない場合は、そのデータをもとに新しいアンケートデータを作成
			$questionnaire = $this->getNewQuestionnaire();
			return $questionnaire;
		} else {
			return false;
		}
	}

/**
 * requireWhen
 *
 * @param mixed $check チェック対象入力データ
 * @param string $sourceField チェック対象フィールド名
 * @param mix $sourceValue チェック値
 * @return bool
 */
	public function requireWhen($check, $sourceField, $sourceValue) {
		// チェックすべきかどうかの判定データが、指定の状態かチェック
		if ($this->data['ActionQuestionnaireAdd'][$sourceField] != $sourceValue) {
			// 指定状態でなければ問題なし
			return true;
		}
		// 指定の状態であれば、チェック対象データがちゃんと入っているか確認する
		// Validation::notBlank($check);
		if (! array_shift($check)) {
			// 指定のデータが指定の値になっている場合は、このデータ空っぽの場合はエラー
			return false;
		}
		return true;
	}

/**
 * checkPastQuestionnaire
 *
 * @param mix $check チェック対象入力データ
 * @return bool
 */
	public function checkPastQuestionnaire($check) {
		if ($this->data['ActionQuestionnaireAdd']['create_option'] !=
			QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_REUSE) {
			return true;
		}
		$this->Questionnaire = ClassRegistry::init('Questionnaires.Questionnaire', true);
		$baseCondition = $this->Questionnaire->getBaseCondition(array(
			'Questionnaire.id' => $check['past_questionnaire_id']
		));
		$cnt = $this->Questionnaire->find('count', array(
			'conditions' => $baseCondition,
			'recursive' => -1
		));
		if ($cnt == 0) {
			return false;
		}
		return true;
	}

/**
 * getNewQuestionnaire
 *
 * @return array
 */
	public function getNewQuestionnaire() {
		$this->Questionnaire = ClassRegistry::init('Questionnaires.Questionnaire', true);
		$this->QuestionnairePage = ClassRegistry::init('Questionnaires.QuestionnairePage', true);
		$this->QuestionnaireQuestion = ClassRegistry::init('Questionnaires.QuestionnaireQuestion', true);
		$createOption = $this->data['ActionQuestionnaireAdd']['create_option'];

		// 指定された作成のオプションによって処理分岐
		if ($createOption == QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW) {
			// 空の新規作成
			$questionnaire = $this->_createNew();
		} elseif ($createOption == QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_REUSE) {
			// 過去データからの作成
			$questionnaire = $this->_createFromReuse();
		} elseif ($createOption == QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_TEMPLATE) {
			// テンプレートファイルからの作成
			$questionnaire = $this->_createFromTemplate();
		}
		return $questionnaire;
	}
/**
 * _createNew
 *
 * @return array QuestionnaireData
 */
	protected function _createNew() {
		// アンケートデータを新規に作成する
		// 新規作成の場合、タイトル文字のみ画面で設定されPOSTされる
		// Titleをもとに、アンケートデータ基本構成を作成し返す

		// デフォルトデータをもとに新規作成
		$questionnaire = $this->_getDefaultQuestionnaire(array(
			'title' => $this->data['ActionQuestionnaireAdd']['title']));
		// アンケートデータを返す
		return $questionnaire;
	}
/**
 * _createFromReuse
 *
 * @return array QuestionnaireData
 */
	protected function _createFromReuse() {
		// アンケートデータを過去のアンケートデータをもとにして作成する
		// 過去からの作成の場合、参考にする過去のアンケートのidのみPOSTされてくる
		// (orgin_idではなくidである点に注意！)
		// idをもとに、過去のアンケートデータを取得し、
		// そのデータから今回作成するアンケートデータ基本構成を作成し返す

		// 過去のアンケートのコピー・クローンで作成
		$questionnaireId = $this->data['ActionQuestionnaireAdd']['past_questionnaire_id'];
		$questionnaire = $this->_getQuestionnaireCloneById($questionnaireId);
		return $questionnaire;
	}
/**
 * _getDefaultQuestionnaire
 * get default data of questionnaires
 *
 * @param array $addData add data to Default data
 * @return array
 */
	protected function _getDefaultQuestionnaire($addData) {
		$questionnaire = array();
		$questionnaire['Questionnaire'] = Hash::merge(
			array(
				'block_id' => Current::read('Block.id'),
				'title' => '',
				'key' => '',
				'status' => WorkflowComponent::STATUS_IN_DRAFT,
				'is_total_show' => QuestionnairesComponent::EXPRESSION_SHOW,
				'answer_timing' => QuestionnairesComponent::USES_NOT_USE,
				'is_key_pass_use' => QuestionnairesComponent::USES_NOT_USE,
				'total_show_timing' => QuestionnairesComponent::USES_NOT_USE,
			),
			$addData);

		$questionnaire['QuestionnairePage'][0] = $this->QuestionnairePage->getDefaultPage($questionnaire);
		return $questionnaire;
	}
/**
 * _getQuestionnaireCloneById 指定されたIDにのアンケートデータのクローンを取得する
 *
 * @param int $questionnaireId アンケートID(編集なのでoriginではなくRAWなIDのほう
 * @return array
 */
	protected function _getQuestionnaireCloneById($questionnaireId) {
		// 前もってValidate処理で存在確認されている場合しか
		// この関数が呼ばれないので$questionnaireの判断は不要
		$questionnaire = $this->Questionnaire->find('first', array(
			'conditions' => array('Questionnaire.id' => $questionnaireId),
			'recursive' => 1
		));
		// ID値のみクリア
		$this->Questionnaire->clearQuestionnaireId($questionnaire);

		return $questionnaire;
	}
/**
 * _createFromTemplate
 *
 * @return array QuestionnaireData
 */
	protected function _createFromTemplate() {
		// アンケートデータをUPLOADされたアンケートテンプレートファイルのデータをもとにして作成する
		// テンプレートからの作成の場合、テンプレートファイルがUPLOADされてくる
		// アップされたファイルをもとに、アンケートデータを解凍、取得し、
		// そのデータから今回作成するアンケートデータ基本構成を作成し返す

		if (empty($this->data['ActionQuestionnaireAdd']['template_file']['name'])) {
			$this->validationErrors['template_file'][] =
				__d('questionnaires', 'Please input template file.');
			return null;
		}

		try {
			// アップロードファイルを受け取り、
			// エラーチェックはない。ここでのエラー時はInternalErrorExceptionとなる
			$uploadFile = new TemporaryUploadFile($this->data['ActionQuestionnaireAdd']['template_file']);

			// アップロードファイル解凍
			$unZip = new UnZip($uploadFile->path);
			$temporaryFolder = $unZip->extract();
			// エラーチェック
			if (! $temporaryFolder) {
				$this->validationErrors['template_file'][] = __d('questionnaires', 'illegal import file.');
				return null;
			}

			// フィンガープリント確認
			$fingerPrint = $this->__checkFingerPrint($temporaryFolder->path);
			if ($fingerPrint === false) {
				$this->validationErrors['template_file'][] = __d('questionnaires', 'illegal import file.');
				return null;
			}

			// アンケートテンプレートファイル本体をテンポラリフォルダに展開する。
			$questionnaireZip = new UnZip(
				$temporaryFolder->path . DS . QuestionnairesComponent::QUESTIONNAIRE_TEMPLATE_FILENAME);
			if (! $questionnaireZip->extract()) {
				$this->validationErrors['template_file'][] =
					__d('questionnaires', 'illegal import file.');
				return null;
			}

			// jsonファイルを読み取り、PHPオブジェクトに変換
			$jsonFilePath =
				$questionnaireZip->path . DS . QuestionnairesComponent::QUESTIONNAIRE_JSON_FILENAME;
			$jsonFile = new File($jsonFilePath);
			$jsonData = $jsonFile->read();
			$jsonQuestionnaire = json_decode($jsonData, true);
		} catch (Exception $ex) {
			$this->validationErrors['template_file'][] = __d('questionnaires', 'file upload error.');
			return null;
		}

		// 初めにファイルに記載されているアンケートプラグインのバージョンと
		// 現サイトのアンケートプラグインのバージョンを突合し、差分がある場合はインポート処理を中断する。
		if ($this->_checkVersion($jsonQuestionnaire) === false) {
			$this->validationErrors['template_file'][] = __d('questionnaires', 'version is different.');
			return null;
		}

		// バージョンが一致した場合、アンケートデータをメモリ上に構築
		$questionnaires = $this->_getQuestionnaires(
			$questionnaireZip->path,
			$jsonQuestionnaire['Questionnaires'],
			$fingerPrint);

		// 現在の言語環境にマッチしたデータを返す
		return $questionnaires[0];
	}

/**
 * _getQuestionnaires
 *
 * @param string $folderPath path string to import zip file exist
 * @param array $questionnaires questionnaire data in import json file
 * @param string $importKey import key (hash string)
 * @return array QuestionnaireData
 */
	protected function _getQuestionnaires($folderPath, $questionnaires, $importKey) {
		$wysiswyg = new WysiwygZip();

		foreach ($questionnaires as &$q) {
			// WysIsWygのデータを入れなおす
			$flatQuestionnaire = Hash::flatten($q);
			foreach ($flatQuestionnaire as $key => &$value) {
				$model = null;
				if (strpos($key, 'QuestionnaireQuestion.') !== false) {
					$model = $this->QuestionnaireQuestion;
				} elseif (strpos($key, 'QuestionnairePage.') !== false) {
					$model = $this->QuestionnairePage;
				} elseif (strpos($key, 'Questionnaire.') !== false) {
					$model = $this->Questionnaire;
				}
				if (!$model) {
					continue;
				}
				$columnName = substr($key, strrpos($key, '.') + 1);

				if ($model->hasField($columnName)) {
					if ($model->getColumnType($columnName) == 'text') {
						// keyと同じ名前のフォルダの下にあるkeyの名前のZIPファイルを渡して
						// その返ってきた値をこのカラムに設定
						$value =
							$wysiswyg->getFromWysiwygZip(
								$folderPath . DS . $value, $model->alias . '.' . $columnName);
					}
				}
			}
			$q = Hash::expand($flatQuestionnaire);
			$q['Questionnaire']['import_key'] = $importKey;
		}
		return $questionnaires;
	}
/**
 * __checkFingerPrint
 *
 * @param string $folderPath folder path
 * @return string finger print string
 */
	private function __checkFingerPrint($folderPath) {
		// フィンガープリントファイルを取得
		$file = new File(
				$folderPath . DS . QuestionnairesComponent::QUESTIONNAIRE_FINGER_PRINT_FILENAME,
				false);
		$fingerPrint = $file->read();

		// ファイル内容から算出されるハッシュ値と指定されたフットプリント値を比較し
		// 同一であれば正当性が保証されたと判断する（フォーマットチェックなどは行わない）
		$questionnaireZipFile =
			$folderPath . DS . QuestionnairesComponent::QUESTIONNAIRE_TEMPLATE_FILENAME;
		if (! file_exists($questionnaireZipFile)) {
			return false;
		}
		if (sha1_file($questionnaireZipFile, false) != $fingerPrint) {
			return false;
		}
		$file->close();
		return $fingerPrint;
	}
/**
 * _checkVersion
 *
 * @param array $jsonData バージョンが含まれたJson
 * @return bool
 */
	protected function _checkVersion($jsonData) {
		// バージョン情報を取得するためComposer情報を得る
		$Plugin = ClassRegistry::init('PluginManager.Plugin');
		$composer = $Plugin->getComposer('netcommons/questionnaires');
		if (!$composer) {
			return false;
		}
		if (!isset($jsonData['version'])) {
			return false;
		}
		if ($composer['version'] != $jsonData['version']) {
			return false;
		}
		return true;
	}
}
