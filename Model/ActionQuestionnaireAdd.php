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
App::uses('TemporaryFolder', 'Folder.Utility');

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
					'requireWhen', 'create_option', QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW),
				'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('questionnaires', 'Title')),
				'required' => false,
			),
			'past_questionnaire_id' => array(
				'requireWhen' => array(
					'rule' => array('requireWhen', 'create_option', QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_REUSE),
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
		if (empty(array_shift($check))) {
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
		if ($this->data['ActionQuestionnaireAdd']['create_option'] != QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_REUSE) {
			return true;
		}
		$this->Questionnaire = ClassRegistry::init('Questionnaires.Questionnaire', true);
		$cnt = $this->Questionnaire->find('count', array('id' => $check));
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
		$questionnaire = $this->_getQuestionnaireCloneById($this->data['ActionQuestionnaireAdd']['past_questionnaire_id']);
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
				'public_type' => WorkflowBehavior::PUBLIC_TYPE_PUBLIC,
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
		$questionnaire = $this->Questionnaire->find('first', array(
			'conditions' => array('Questionnaire.id' => $questionnaireId),
		));

		if (!$questionnaire) {
			return $this->getDefaultQuestionnaire(array('title' => ''));
		}
		// ID値のみクリア
		$this->clearQuestionnaireId($questionnaire);

		return $questionnaire;
	}
/**
 * clearQuestionnaireId アンケートデータからＩＤのみをクリアする
 *
 * @param array &$questionnaire アンケートデータ
 * @return void
 */
	public function clearQuestionnaireId(&$questionnaire) {
		foreach ($questionnaire as $qKey => $q) {
			if (is_array($q)) {
				$this->clearQuestionnaireId($questionnaire[$qKey]);
			} elseif (preg_match('/(.*?)id$/', $qKey) ||
				preg_match('/^key$/', $qKey) ||
				preg_match('/^created(.*?)/', $qKey) ||
				preg_match('/^modified(.*?)/', $qKey)) {
				unset($questionnaire[$qKey]);
			}
		}
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

		// アップロードファイルを受け取り、
		$file = $this->FileUpload->upload('Questionnaire', 'template_file');

		// テンポラリフォルダ作成とカレントディレクトリ変更
		//$folder = $this->QuestionnairesDownload->createTemporaryFolder($this, 'template');
		$folder = new TemporaryFolder();

		// ファイルを移動
		$importFilePath = $folder->pwd() . DS . QuestionnairesComponent::QUESTIONNAIRE_TEMPLATE_EXPORT_FILENAME;
		move_uploaded_file($file['tmp_name'], $importFilePath);

		// 解凍
		if ($this->QuestionnaireDownload->extractZip($importFilePath, $folder->pwd()) === false) {
			$this->validationErrors['Questionnaire']['template_file'] = __d('questionnaires', 'illegal import file.');
			return null;
		}

		// フィンガープリント確認
		$fingerPrint = $this->__checkFingerPrint($folder->pwd());
		if ($fingerPrint === false) {
			$this->validationErrors['Questionnaire']['template_file'] = __d('questionnaires', 'illegal import file.');
			return null;
		}

		// アンケートテンプレートファイル本体をテンポラリフォルダに展開する。
		$questionnaireZipFile = $folder->pwd() . DS . QuestionnairesComponent::QUESTIONNAIRE_TEMPLATE_FILENAME;
		if ($this->QuestionnaireDownload->extractZip($questionnaireZipFile, $folder->pwd()) === false) {
			$this->validationErrors['Questionnaire']['template_file'] = __d('questionnaires', 'illegal import file.');
			return null;
		}

		// jsonファイルを読み取り、PHPオブジェクトに変換
		$jsonFilePath = $folder->pwd() . DS . QuestionnairesComponent::QUESTIONNAIRE_JSON_FILENAME;
		$jsonFileFp = fopen($jsonFilePath, 'rb');
		$jsonData = fread($jsonFileFp, filesize($jsonFilePath));
		$jsonQuestionnaire = json_decode($jsonData, true);

		// 初めにファイルに記載されているアンケートプラグインのバージョンと
		// 現サイトのアンケートプラグインのバージョンを突合し、差分がある場合はインポート処理を中断する。
		if ($this->__checkVersion($jsonQuestionnaire) === false) {
			$this->validationErrors['Questionnaire']['template_file'] = __d('questionnaires', 'version is different.');
			return null;
		}

		// バージョンが一致した場合、アンケートデータをメモリ上に構築
		$questionnaires = $this->_getQuestionnaires(
			$folder->pwd(),
			$jsonQuestionnaire['Questionnaires'],
			$fingerPrint);

		// インポートデータ「オリジナルデータ」をセッションに書きこんでおく
		$this->Session->write('Questionnaires.importQuestionnaire', $questionnaires);

		// 代表データを返す
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
		foreach ($questionnaires as &$q) {
			// id, keyはクリアする
			$this->Questionnaire->clearQuestionnaireId($q);

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
						$value = $this->QuestionnairesWysIsWyg->getFromWysIsWygZIP($folderPath . DS . $key . DS . $key . '.zip', $key);
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
		$fingerPrintFp = fopen($folderPath . DS . QuestionnairesComponent::QUESTIONNAIRE_FINGER_PRINT_FILENAME, 'rb');
		if ($fingerPrintFp === false) {
			return false;
		}
		$fingerPrint = fread($fingerPrintFp, 1024);

		// ファイル内容から算出されるハッシュ値と指定されたフットプリント値を比較し
		// 同一であれば正当性が保証されたと判断する（フォーマットチェックなどは行わない）
		$questionnaireZipFile = $folderPath . DS . QuestionnairesComponent::QUESTIONNAIRE_TEMPLATE_FILENAME;
		if (sha1_file($questionnaireZipFile, false) != $fingerPrint) {
			return false;
		}
		fclose($fingerPrintFp);
		return $fingerPrint;
	}
/**
 * __checkVersion
 *
 * @param array $jsonData バージョンが含まれたJson
 * @return bool
 */
	private function __checkVersion($jsonData) {
		$composer = $this->Plugin->getComposer('netcommons/questionnaires');
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