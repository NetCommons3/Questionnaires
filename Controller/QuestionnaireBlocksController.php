<?php
/**
 * QuestionnaireBlocksController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryo Ozawa <ozawa.ryo@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

/**
 * BlocksController
 *
 * @author Ryo Ozawa <ozawa.ryo@withone.co.jp>
 * @package NetCommons\Questionnaires\Controller
 */
class QuestionnaireBlocksController extends QuestionnairesAppController {

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
		'Blocks.Block',
		'Frames.Frame',
		'Questionnaires.Questionnaire',
		'Questionnaires.QuestionnaireFrameSetting',
		'Questionnaires.QuestionnairePage',
		'Questionnaires.QuestionnaireQuestion',
		'Questionnaires.QuestionnaireChoice',
		'Questionnaires.QuestionnaireAnswerSummary',
		'Questionnaires.QuestionnaireAnswerSummaryCsv',
		'Comments.Comment',
		'Categories.Category',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.NetCommonsBlock',
		'NetCommons.NetCommonsFrame',
		'NetCommons.NetCommonsRoomRole' => array(
			//コンテンツの権限設定
			'allowedActions' => array(
				'blockEditable' => array('index', 'download')
			),
		),
		'Questionnaires.Questionnaires',
		'Paginator',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.Date',
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->deny('index');

		$results = $this->camelizeKeyRecursive($this->NetCommonsFrame->data);
		$this->set($results);

		//タブの設定
		$this->initTabs('block_index');
	}

/**
 * index
 *
 * @return void
 * @throws Exception
 */
	public function index() {
		// 条件設定値取得
		$conditions = $this->Questionnaire->getConditionForAnswer(
			$this->viewVars['blockId'],
			$this->Auth->user('id'),
			$this->viewVars,
			$this->getNowTime()
		);
		// データ取得
		// Modelの方ではカスタムfindメソッドを装備している
		// ここではtype属性を指定することでカスタムFindを呼び出すように指示している
		try {
			$subQuery = $this->Questionnaire->getQuestionnairesCommonForAnswer($this->Session->id(), $this->Auth->user('id'));
			$this->paginate = array(
				'conditions' => $conditions,
				'page' => 1,
				'sort' => QuestionnairesComponent::DISPLAY_SORT_TYPE_NEW_ARRIVALS,
				'limit' => QuestionnairesComponent::QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE,
				'direction' => 'desc',
				'recursive' => 0,
				'joins' => $subQuery,
				'fields' => array(
					'Block.*',
					'Questionnaire.*',
					'TrackableCreator.*',
					'TrackableUpdater.*',
					'CountAnswerSummary.*'
				),
				'sessionId' => $this->Session->id(),
				'userId' => $this->Auth->user('id')
			);
			$questionnaire = $this->paginate('Questionnaire');
		} catch (NotFoundException $e) {
			// NotFoundの例外
			// アンケートデータが存在しないこととする
			$questionnaire = array();
		}
		$this->set('questionnaires', $questionnaire);
	}

/**
 * download
 *
 * @param int $frameId frame id
 * @param int $questionnaireId questionnaire origin id
 * @return void
 */
	public function download($frameId, $questionnaireId) {
		// viewを使用しない
		$this->autoRender = false;

		$questionnaire = $this->Questionnaire->find('first', array(
			'conditions' => array(
				'origin_id' => $questionnaireId,
				'is_active' => true,
			)
		));
		if (empty($questionnaire)) {
			$this->Session->setFlash(__d('questionnaires', 'download error'));
			return;
		}

		$downloadFileName = $questionnaire['Questionnaire']['title'] . '.csv';
		$fileName = date('Ymd_his') . '.csv';
		$offset = 0;

		// テンポラリファイルオープン
		$folder = new Folder();
		$folderName = TMP . 'Questionnaires' . DS . 'download' . DS . microtime(true);
		$folder->create($folderName);
		$folder->cd($folderName);

		// フォルダ内のお掃除
		$this->__cleanupDownloadFolder(TMP . 'Questionnaires' . DS . 'download');

		$filePath = $folder->pwd() . DS . $fileName;
		$fp = fopen($filePath, 'w+');
		do {
			$datas = $this->QuestionnaireAnswerSummaryCsv->getAnswerSummaryCsv($questionnaire, QuestionnairesComponent::QUESTIONNAIRE_CSV_UNIT_NUMBER, $offset);
			// テンポラリファイルにCSV形式で書きこみ
			foreach ($datas as $data) {
				fputcsv($fp, $data);
			}
			$offset += count($datas);
			$dataCount = count($datas);
		} while ($dataCount == QuestionnairesComponent::QUESTIONNAIRE_CSV_UNIT_NUMBER);
		// ファイルクローズ
		fclose($fp);

		// 暗号圧縮？現時点ではコマンドでしか実行できない
		if ($this->__compressFile($filePath)) {
			$downloadFileName = substr($downloadFileName, 0, strrpos($downloadFileName, '.')) . '.zip';
		}
		// 出力
		$this->response->file($filePath, array('download' => true, 'name' => rawurlencode($downloadFileName)));
		return $this->response;
	}

/**
 * __cleanupDownloadFolder
 *
 * @param string $folderPath download folder path
 * @return bool
 */
	private function __cleanupDownloadFolder($folderPath) {
		$folder = new Folder($folderPath);
		$files = $folder->read(true, true, true);
		// フォルダは返される配列の０番目の配列に設定されている
		if (isset($files[0])) {
			$nowTime = time();
			foreach ($files[0] as $dir) {
				// 作成時間を確認
				$stat = stat($dir);
				// 既定時間より以前に作成されたものなら消してしまう
				if ($stat['mtime'] < ($nowTime - 60 * 10)) {
					$delFolder = new Folder($dir);
					$delFolder->delete();
				}
			}
		}
		return true;
	}

/**
 * __compressFile
 *
 * @param string &$filePath input file path
 * @return bool
 */
	private function __compressFile(&$filePath) {
		$cmd = '/usr/bin/zip';
		if (!file_exists($cmd)) {
			return false;
		}

		$outputFilePath = $filePath . '.zip';

		$execCmd = sprintf('%s -j -e -P %s %s %s', $cmd, $this->Auth->user('username'), $outputFilePath, $filePath);

		// コマンドを実行する
		exec(escapeshellcmd($execCmd));

		// 入力ファイルを削除する
		unlink($filePath);

		$filePath = $outputFilePath;
		return true;
	}

/**
 * initTabs
 *
 * @param string $activeTab Block active tab
 * @return void
 */
	public function initTabs($activeTab) {
		$block = $this->Block->find('first', array(
			'conditions' => array(
				'Block.room_id' => $this->viewVars['roomId'],
				'Block.plugin_key' => 'questionnaires'
			)
		));
		if (isset($block['Block']['id'])) {
			$blockId = (int)$block['Block']['id'];
		} else {
			$blockId = null;
		}

		//タブの設定
		$settingTabs = array(
			'tabs' => array(
				'block_index' => array(
					'url' => array(
						'plugin' => $this->params['plugin'],
						'controller' => 'questionnaire_blocks',
						'action' => 'index',
						$this->viewVars['frameId'],
					)
				),
				'frame_settings' => array(
					'url' => array(
						'plugin' => $this->params['plugin'],
						'controller' => 'questionnaire_frame_settings',
						'action' => 'edit',
						$this->viewVars['frameId'],
						$blockId
					)
				),
				'role_permissions' => array(
					'url' => array(
						'plugin' => $this->params['plugin'],
						'controller' => 'questionnaire_block_role_permissions',
						'action' => 'edit',
						$this->viewVars['frameId'],
						$blockId
					)
				),
			),
			'active' => $activeTab
		);
		$this->set('settingTabs', $settingTabs);
	}
}
