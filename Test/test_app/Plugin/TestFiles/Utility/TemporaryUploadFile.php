<?php
/**
 * TemporaryUploadFileTesting
 *
 * @author   Ryuji AMANO <ryuji@ryus.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('File', 'Utility');
App::uses('TemporaryFolder', 'Files.Utility');

/**
 * Class TemporaryUploadFileTesting
 */
class TemporaryUploadFile extends File {

/**
 * TemporaryUploadFile constructor.
 *
 * アップロードファイルは、自動的に作成されたテンポラリフォルダに配置される。
 * インスタンス破棄時にテンポラリフォルダ毎ファイルも削除される
 * ファイル名は自動的にハッシュしたものに書き換わる。
 *
 * @param array $file アップロードファイルの配列
 * @throws InternalErrorException
 */
	public function __construct($file) {
		$this->temporaryFolder = new TemporaryFolder();
		$path = $file['tmp_name'];

		$this->originalName = $file['name'];

		$destFileName = Security::hash(mt_rand() . microtime(), 'md5') . '.' . pathinfo(
				$file['name'],
				PATHINFO_EXTENSION
			);
		$result = $this->_moveFile($path, $this->temporaryFolder->path . DS . $destFileName);
		if ($result === false) {
			throw new InternalErrorException('move_uploaded_file failed');
		}
		$this->error = $file['error'];
		parent::__construct($this->temporaryFolder->path . DS . $destFileName);
	}

/**
 * _move
 *
 * @param string $path 移動元パス
 * @param string $destPath 移動先パス
 * @return bool
 */
	protected function _moveFile($path, $destPath) {
		if (! file_exists($path)) {
			return false;
		}
		return rename($path, $destPath);
	}
}
