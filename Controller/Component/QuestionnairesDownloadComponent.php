<?php
/**
 * QuestionnairesDownloadComponent
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Ryo Ozawa <ozawa.ryo@withone.co.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
App::uses('Component', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

/**
 * QuestionnairesDownloadComponent
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuestionnairesDownloadComponent extends Component {

/**
 * working folder for download
 *
 * @var Folder
 */
    protected $_workingFolder = null;
/**
 * file name for download
 *
 * @var string
 */
    protected $_downloadFileName = '';
/**
 * extension for download
 *
 * @var string
 */
    protected $_downloadFileExt = '';

/**
 * shutdown
 *
 * @param Controller $controller
 * @return void
 */
    public function shutdown(Controller $controller) {
        if ($this->_workingFolder) {
            //$this->_workingFolder->delete();
        }
    }
/**
 * createTemporaryFolder
 *
 * @param string $folderName temporary middle name
 * @return Folder
 */
    public function createTemporaryFolder($controller, $folderName = 'download') {
        $folder = new Folder();
        if (!$folder) {
            throw new Exception(__d('net_commons', 'can not create folder'));
        }
        $folderName = TMP . $controller->plugin . DS . $folderName . DS . microtime(true);
        $folder->create($folderName);
        if (!$folder->cd($folderName)) {
            throw new Exception(__d('net_commons', 'can not change folder'));
        }
        $this->_workingFolder = $folder;
        return $folder;
    }

/**
 * createDownloadZipFile
 *
 * create zip file for download, and open it
 *
 * @param Folder $folder working folder object
 * @param string $fileName filename
 * @return ZipArchive
 */
    public function createDownloadZipFile($folder, $fileName) {
        $zip = new ZipArchive();
        $this->_downloadFileExt = 'zip';
        $ret = $zip->open($folder->pwd() . DS . $fileName, ZipArchive::CREATE);
        if (!$ret) {
            throw new Exception(__d('net_commons', 'can not create archive file'));
        }
        $this->_downloadFileName = $fileName;
        return $zip;
    }

/**
 * createDownloadFile
 *
 * create file for download, and open it
 *
 * @param Folder $folder working folder object
 * @param string $fileName filename
 * @return File
 */
    public function createDownloadFile($folder, $fileName) {
        $filePath = $folder->pwd() . DS . $fileName;
        $fp = fopen($filePath, 'w+');
        if (!$fp) {
            throw new Exception(__d('net_commons', 'can not create file'));
        }
        $this->_downloadFileName = $fileName;
        $this->_downloadFileExt = pathinfo($filePath, PATHINFO_EXTENSION);
        return $fp;
    }

/**
 * getDownloadFilePath
 *
 * get path of download file
 *
 * @return string
 */
    public function getDownloadFilePath() {
        return $this->_workingFolder->pwd() . DS . $this->_downloadFileName;
    }
/**
 * getDownloadFileExtension
 *
 * get path of download file extension
 *
 * @return string
 */
    public function getDownloadFileExtension() {
        return $this->_downloadFileExt;
    }

/**
 * compressFile
 *
 * @param string &$filePath input file path
 * @return string
 */
    public function compressFile($password) {
        // 暗号化ZIPにするのはノーマルファイルの場合のみと考える
        $filePath = $this->_workingFolder->pwd() . DS . $this->_downloadFileName;

        $cmd = '/usr/bin/zip';
        if (!file_exists($cmd)) {
            return $filePath;
        }

        $pathInfo = pathinfo($filePath);
        $this->_downloadFileName = $pathInfo['filename'] . '.zip';
        $outputFilePath = $pathInfo['dirname'] . DS . $this->_downloadFileName;

        $execCmd = sprintf('%s -j -e -P %s %s %s', $cmd, $password, $outputFilePath, $filePath);

        // コマンドを実行する
        exec(escapeshellcmd($execCmd));

        // 入力ファイルを削除する
        @unlink($filePath);

        $this->_downloadFileExt = 'zip';

        return $outputFilePath;
    }
}

