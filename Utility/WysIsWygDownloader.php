<?php
/**
 * Questionnaires WysIsWyg Downloader
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */


App::uses('ZipDownloader', 'Files.Utility');
App::uses('UnZip', 'Files.Utility');
App::uses('UploadFile', 'Files.Model');

/**
 * QuestionnairesWysIsWygUtility
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class WysIsWygDownloader {

/**
 * csv download item count handling unit
 *
 * @var int
 */
	const	WYSIWYG_FILE_KEY_PATTERN = '/(.*?)\/wysiwyg\/(?:image|file)\/download\/(\d*?)\/(\d*?)"(.*)/';

/**
 * Constructor
 */
	public function __construct() {
		$this->UploadFiles = ClassRegistry::init('Files.UploadFiles');
	}

/**
 * getFromWysIsWygZIP
 * このメソッドはいずれはWYSISWYGエディタダウンロードコンポーネントへ移動します
 *
 * @param string $zipFilePath Zipファイルへのパス
 * @param string $file Zipファイルの中にある読み取るべきファイル名
 * @return string wysiswyg editor data
 * @throws InternalErrorException
 */
	public function getFromWysIsWygZIP($zipFilePath, $file) {
		$unZip = new UnZip($zipFilePath);
		$tmpFolder = $unZip->extract();
		if ($tmpFolder === false) {
			return false;
		}

		// $fileの中を開く
		$filePath = $unZip->path . DS . $file;
		$file = new File($filePath);
		$data = $file->read();

		$retStr = '';
		$roomId = Current::read('Room.id');
		// uploadFile登録に必要な data（block_key）を作成する。
		$uploadBlockKey = [
			'UploadFile' => [
				'block_key' => Current::read('Block.key'),
				'room_id' => Current::read('Block.room_id'),
			]
		];
		$uploadFileModel = ClassRegistry::init('Files.UploadFile');

		$tmpStr = str_replace('<img', "\n<img", $data);
		$tmpStr = str_replace('<a', "\n<a", $tmpStr);
		$tmpStrArr = explode("\n", $tmpStr);
		// 1行ずつ処理
		foreach ($tmpStrArr as $line) {
			// wysiwyg行があるか？
			$matchCount = preg_match(self::WYSIWYG_FILE_KEY_PATTERN, $line, $matches);
			// ある
			if ($matchCount > 0) {
				// その中に書かれているwysiwygで設定されたファイルのリスト（uploadId)を得る
				$uploadId = $matches[3];
				// imageなのかfileなのか
				if (preg_match('/^<img/', $matches[1])) {
					$type = 'image';
				} else {
					$type = 'file';
				}

				// uploadIdに一致するファイルを取り出す
				$upFileNames = $tmpFolder->find($uploadId . '.*');
				if (empty($upFileNames)) {
					CakeLog::error('Can not find wysiwyg file ' . $uploadId);
					throw new InternalErrorException();
				}
				$upFile = new File($tmpFolder->path . DS . $upFileNames[0]);

				// そのファイルをUPLOAD処理する
				$uploadedFile = $uploadFileModel->registByFile(
					$upFile,
					'wysiwyg',
					null,
					'Wysiwyg.file',
					$uploadBlockKey);
				if (! $uploadedFile) {
					CakeLog::error('Can not upload wysiwyg file ' . $uploadId);
					throw new InternalErrorException();
				}

				// wysiwygのパス情報を新ルームIDと新UPLOADIDに差し替える
				$line = sprintf('%s/wysiwyg/%s/download/%d/%d%s',
					$matches[1],
					$type,
					$roomId,
					$uploadedFile['UploadFile']['id'],
					$matches[4]);
			}

			// wysiwygテキスト再構築
			$retStr .= $line;
		}

		//
		// 構築したテキストを返す
		return $retStr;
	}
/**
 * createWysIsWygZIP
 * このメソッドはいずれはWYSISWYGエディタダウンロードコンポーネントへ移動します
 *
 * @param string $zipFileName Zipファイル名
 * @param string $data wysiswyg editor content
 * @return string path to zip file about "data"
 * @throws InternalErrorException
 */
	public function createWysIsWygZIP($zipFileName, $data) {
		$zip = new ZipDownloader();

		// UPLOADされているファイル情報を取り出す
		$tmpStr = $data;
		$tmpStr = str_replace('<img', "\n<img", $tmpStr);
		$tmpStr = str_replace('<a', "\n<a", $tmpStr);
		$matchCount = preg_match_all(self::WYSIWYG_FILE_KEY_PATTERN, $tmpStr, $matches);
		if ($matchCount > 0) {
			// ファイルのUPLOAD_IDを取り出す
			foreach ($matches[3] as $uploadId) {
				// ファイル情報を取得してくる
				$uploadFile = $this->UploadFiles->findById($uploadId);
				CakeLog::debug(print_r($uploadFile, true));
				if ($uploadFile) {
					$uploadFile = $uploadFile['UploadFiles'];
					// ルームチェック
					if ($uploadFile['room_id']) {
						$roomId = Current::read('Room.id');
						if ($uploadFile['room_id'] != $roomId) {
							CakeLog::error('Can not find wysiwyg file ' . $uploadId);
							throw new InternalErrorException();
						}
					}
					if ($uploadFile['block_key']) {
						// block_keyによるガード
						$Block = ClassRegistry::init('Blocks.Block');
						$uploadFileBlock = $Block->findByKeyAndLanguageId(
							$uploadFile['block_key'],
							Current::read('Language.id')
						);
						if ($Block->isVisible($uploadFileBlock) === false) {
							CakeLog::error('Can not find wysiwyg file ' . $uploadId);
							throw new InternalErrorException();
						}
					}
					// そのファイルをZIPに含める
					$path = WWW_ROOT . trim($uploadFile['path'], '/') . '/' .
						$uploadId . '/' . $uploadFile['real_file_name'];

					$zip->addFile($path, $uploadId . '.' . $uploadFile['extension']);
				}
			}

		}

		$zip->addFromString($zipFileName, $data);

		$zip->close();
		return $zip->path;
	}
}
