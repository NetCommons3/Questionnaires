<?php
/**
 * QuestionnaireExport Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');
App::uses('WysiwygZip', 'Wysiwyg.Utility');

/**
 * Summary for Questionnaire Model
 */
class QuestionnaireExport extends QuestionnairesAppModel {

/**
 * Use table config
 *
 * @var bool
 */
	public $useTable = 'questionnaires';

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'AuthorizationKeys.AuthorizationKey',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

/**
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 * @see Model::__construct()
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->loadModels([
			'Questionnaire' => 'Questionnaires.Questionnaire',
			'QuestionnairePage' => 'Questionnaires.QuestionnairePage',
			'QuestionnaireQuestion' => 'Questionnaires.QuestionnaireQuestion',
		]);
	}

/**
 * getExportData
 *
 * @param string $questionnaireKey アンケートキー
 * @return array QuestionnaireData for Export
 */
	public function getExportData($questionnaireKey) {
		// アンケートデータをjsonにして記述した内容を含むZIPファイルを作成する
		$zipData = array();

		// バージョン情報を取得するためComposer情報を得る
		$Plugin = ClassRegistry::init('PluginManager.Plugin');
		$composer = $Plugin->getComposer('netcommons/questionnaires');
		// 最初のデータはアンケートプラグインのバージョン
		$zipData['version'] = $composer['version'];

		// 言語数分
		$Language = ClassRegistry::init('M17n.Language');
		$languages = $Language->getLanguage();
		$questionnaires = array();
		foreach ($languages as $lang) {
			// 指定のアンケートデータを取得
			$questionnaire = $this->Questionnaire->find('first', array(
				'conditions' => array(
					'Questionnaire.key' => $questionnaireKey,
					'Questionnaire.is_active' => true,
					'Questionnaire.is_latest' => true,
					'Questionnaire.language_id' => $lang['Language']['id']
				),
				'recursive' => 0
			));
			// 指定の言語データがない場合もあることを想定
			if (empty($questionnaire)) {
				continue;
			}
			$questionnaire = Hash::remove($questionnaire, 'Block');
			$questionnaire = Hash::remove($questionnaire, 'TrackableCreator');
			$questionnaire = Hash::remove($questionnaire, 'TrackableUpdater');
			$this->Questionnaire->clearQuestionnaireId($questionnaire);
			$questionnaires[] = $questionnaire;
		}
		// Exportするデータが一つも見つからないって
		if (empty($questionnaire)) {
			return false;
		}
		$zipData['Questionnaires'] = $questionnaires;
		return $zipData;
	}
/**
 * putToZip
 *
 * @param ZipDownloader $zipFile ZIPファイルオブジェクト
 * @param array $zipData zip data
 * @return void
 */
	public function putToZip($zipFile, $zipData) {
		$wysiswyg = new WysiwygZip();

		// アンケートデータの中でもWYSISWYGデータのものについては
		// フォルダ別に確保(フォルダの中にZIPがある）
		$flatQuestionnaire = Hash::flatten($zipData);
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
					$wysiswygZipFile = $wysiswyg->createWysiwygZip($value, $model->alias . '.' . $columnName);
					$wysiswygFileName = $key . '.zip';
					$zipFile->addFile($wysiswygZipFile, $wysiswygFileName);
					$value = $wysiswygFileName;
				}
			}
		}
		$questionnaire = Hash::expand($flatQuestionnaire);
		// jsonデータにして書き込み
		$zipFile->addFromString(
			QuestionnairesComponent::QUESTIONNAIRE_JSON_FILENAME,
			json_encode($questionnaire));
	}
}
