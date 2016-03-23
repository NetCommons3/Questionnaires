<?php
/**
 * QuestionnaireBlocksController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');
App::uses('TemporaryFolder', 'Files.Utility');
App::uses('CsvFileWriter', 'Files.Utility');
App::uses('ZipDownloader', 'Files.Utility');

/**
 * BlocksController
 *
 * @author Ryo Ozawa <ozawa.ryo@withone.co.jp>
 * @package NetCommons\Questionnaires\Controller
 */
class QuestionnaireBlocksController extends QuestionnairesAppController {

/**
 * csv download item count handling unit
 *
 * @var int
 */
	const	QUESTIONNAIRE_CSV_UNIT_NUMBER = 1000;

/**
 * layout
 *
 * @var array
 */
	public $layout = 'NetCommons.setting';

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Questionnaires.Questionnaire',
		'Questionnaires.QuestionnaireFrameSetting',
		'Questionnaires.QuestionnaireAnswerSummary',
		'Questionnaires.QuestionnaireAnswerSummaryCsv',
		'Blocks.Block',
		'Questionnaires.QuestionnaireExport',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				'index,download,export' => 'block_editable',
			),
		),
		'Paginator',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'Session',
		'Blocks.BlockForm',
		'Blocks.BlockTabs' => array(
			'mainTabs' => array(
				'block_index' => array('url' => array('controller' => 'questionnaire_blocks')),
				'role_permissions' => array('url' => array('controller' => 'questionnaire_block_role_permissions')),
				'frame_settings' => array('url' => array('controller' => 'questionnaire_frame_settings')),
				'mail_settings' => array('url' => array('controller' => 'questionnaire_mail_settings')),
			),
		),
		'NetCommons.NetCommonsForm',
		'NetCommons.Date',
		'NetCommons.TitleIcon',
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->deny('index');
		// 設定画面を表示する前にこのルームのアンケートブロックがあるか確認
		// 万が一、まだ存在しない場合には作成しておく
		// afterFrameSaveが呼ばれないような状況の想定
		$frame['Frame'] = Current::read('Frame');
		$this->Questionnaire->afterFrameSave($frame);
	}

/**
 * index
 *
 * @return void
 */
	public function index() {
		// 条件設定値取得
		// 条件設定値取得
		$conditions = $this->Questionnaire->getBaseCondition();

		// データ取得
		$this->paginate = array(
			'conditions' => $conditions,
			'page' => 1,
			'order' => array('Questionnaire.modified' => 'DESC'),
			'limit' => QuestionnairesComponent::QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE,
			'recursive' => 0,
		);
		$questionnaire = $this->paginate('Questionnaire');
		if (! $questionnaire) {
			$this->view = 'not_found';
			return;
		}

		$this->set('questionnaires', $questionnaire);
	}

/**
 * download
 *
 * @return void
 * @throws InternalErrorException
 */
	public function download() {
		// NetCommonsお約束：コンテンツ操作のためのURLには対象のコンテンツキーが必ず含まれている
		// まずは、そのキーを取り出す
		// アンケートキー
		$questionnaireKey = $this->_getQuestionnaireKeyFromPass();
		// キー情報をもとにデータを取り出す
		$questionnaire = $this->QuestionnaireAnswerSummaryCsv->getQuestionnaireForAnswerCsv($questionnaireKey);
		if (! $questionnaire) {
			$this->_setFlashMessageAndRedirect(__d('questionnaires', 'Designation of the questionnaire does not exist.'));
			return;
		}
		// 圧縮用パスワードキーを求める
		if (! empty($this->request->data['AuthorizationKey']['authorization_key'])) {
			$zipPassword = $this->request->data['AuthorizationKey']['authorization_key'];
		} else {
			$this->_setFlashMessageAndRedirect(__d('questionnaires', 'Setting of password is required always to download answers.'));
			return;
		}

		try {
			$tmpFolder = new TemporaryFolder();
			$csvFile = new CsvFileWriter(array(
				'folder' => $tmpFolder->path
			));
			// 回答データを一気に全部取得するのは、データ爆発の可能性があるので
			// QUESTIONNAIRE_CSV_UNIT_NUMBER分に制限して取得する
			$offset = 0;
			do {
				$datas = $this->QuestionnaireAnswerSummaryCsv->getAnswerSummaryCsv($questionnaire, self::QUESTIONNAIRE_CSV_UNIT_NUMBER, $offset);
				// CSV形式で書きこみ
				foreach ($datas as $data) {
					$csvFile->add($data);
				}
				$dataCount = count($datas);	// データ数カウント
				$offset += $dataCount;		// 次の取得開始位置をずらす
			} while ($dataCount == self::QUESTIONNAIRE_CSV_UNIT_NUMBER);
			// データ取得数が制限値分だけとれている間は繰り返す

		} catch (Exception $e) {
			// NetCommonsお約束:エラーメッセージのFlash表示
			$this->_setFlashMessageAndRedirect(__d('questionnaires', 'download error'));
			return;
		}
		// Downloadの時はviewを使用しない
		$this->autoRender = false;
		// ダウンロードファイル名決定 アンケート名称をつける
		$zipFileName = $questionnaire['Questionnaire']['title'] . '.zip';
		$downloadFileName = $questionnaire['Questionnaire']['title'] . '.csv';
		// 出力
		return $csvFile->zipDownload(rawurlencode($zipFileName), $downloadFileName, $zipPassword);
	}

/**
 * _setFlashMessageAndRedirect
 *
 * @param string $message flash error message
 *
 * @return void
 */
	protected function _setFlashMessageAndRedirect($message) {
		$this->NetCommons->setFlashNotification($message, array('interval' => NetCommonsComponent::ALERT_VALIDATE_ERROR_INTERVAL));
		$this->redirect(NetCommonsUrl::actionUrl(array(
			'controller' => 'questionnaire_blocks',
			'action' => 'index',
			'frame_id' => Current::read('Frame.id'))));
	}

/**
 * export
 *
 * template file about questionnaire export action
 *
 * @return void
 */
	public function export() {
		// NetCommonsお約束：コンテンツ操作のためのURLには対象のコンテンツキーが必ず含まれている
		// まずは、そのキーを取り出す
		// アンケートキー
		$questionnaireKey = $this->_getQuestionnaireKeyFromPass();
		// キー情報をもとにデータを取り出す
		$questionnaire = $this->QuestionnaireAnswerSummaryCsv->getQuestionnaireForAnswerCsv($questionnaireKey);
		if (! $questionnaire) {
			$this->_setFlashMessageAndRedirect(__d('questionnaires', 'Designation of the questionnaire does not exist.'));
			return;
		}

		try {
			// zipファイル準備
			$zipFile = new ZipDownloader();

			// Export用のデータ配列を取得する
			$zipData = $this->QuestionnaireExport->getExportData($questionnaireKey);

			// Export用ファイルデータをZIPファイルに出力する
			// ※この中でWYSISWYGエディタデータは適宜処理されている
			$this->QuestionnaireExport->putToZip($zipFile, $zipData);

			// アーカイブ閉じる
			$zipFile->close();
		} catch(Exception $e) {
			$this->_setFlashMessageAndRedirect(__d('questionnaires', 'export error' . $e->getMessage()));
			return;
		}
		// 大外枠zipファイル準備
		$zipWrapperFile = new ZipDownloader();
		// アンケートデータファイルのフィンガープリントを得る
		$fingerPrint = sha1_file($zipFile->path, false);
		// フィンガープリントをアーカイブに加える
		$zipWrapperFile->addFromString(QuestionnairesComponent::QUESTIONNAIRE_FINGER_PRINT_FILENAME, $fingerPrint);
		// 本体ファイルを
		$zipWrapperFile->addFile($zipFile->path, QuestionnairesComponent::QUESTIONNAIRE_TEMPLATE_FILENAME);
		// export-key 設定
		$this->Questionnaire->saveExportKey($questionnaire['Questionnaire']['id'], $fingerPrint);

		// viewを使用しない
		$this->autoRender = false;

		// ダウンロード出力ファイル名確定
		$exportFileName = $questionnaire['Questionnaire']['title'] . '.zip';
		// 出力
		return $zipWrapperFile->download(rawurlencode($exportFileName));
	}
}