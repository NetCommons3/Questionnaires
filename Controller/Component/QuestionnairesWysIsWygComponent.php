<?php
/**
 * Questionnaires WysIsWyg Component
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Component', 'Controller');

/**
 * QuestionnairesWysIsWygComponent
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuestionnairesWysIsWygComponent extends Component {
/**
 * getFromWysIsWygZIP
 *
 * ���̃��\�b�h�͂������WYSISWYG�G�f�B�^�_�E�����[�h�R���|�[�l���g�ֈړ����܂�
 * @param string $zipFilePath Zip�t�@�C���ւ̃p�X
 * @param string $file Zip�t�@�C���̒��ɂ���ǂݎ��ׂ��t�@�C����
 * @return string wysiswyg editor data
 */
    public function getFromWysIsWygZIP($zipFilePath, $file) {
        $zip = new ZipArchive;
        if ($zip->open($zipFilePath) !== true) {
            return false;
        }
        if ($zip->extractTo(pathinfo($zipFilePath, PATHINFO_DIRNAME)) === false) {
            return false;
        }
        $zip->close();

        //
        // �{���͂��̕ӂœY�t�t�@�C����UPLOADS�ɐݒ肷�鏈��������
        //

        $filePath = pathinfo($zipFilePath, PATHINFO_DIRNAME) . DS . $file;
        $fileSize = filesize($filePath);
        if ($fileSize == 0) {
            return '';
        }
        $fp = fopen($filePath, 'rb');
        $data = fread($fp, $fileSize);
        fclose($fp);
        return $data;
    }
/**
 * createWysIsWygZIP
 *
 * ���̃��\�b�h�͂������WYSISWYG�G�f�B�^�_�E�����[�h�R���|�[�l���g�ֈړ����܂�
 * @param Folder $folder
 * @param string $zipFileName
 * @param string $data wysiswyg editor content
 * @return string path to zip file about "data"
 */
    public function  createWysIsWygZIP($folder, $zipFileName, $data) {
        $fileName = $zipFileName . '.zip';
        $zip = new ZipArchive();
        $filePath = $folder->pwd() . DS . $fileName;
        $zipFp = $zip->open($filePath, ZipArchive::CREATE);
        if ($zipFp === true) {
            $zip->addFromString($zipFileName, $data);
        } else {
            return null;
        }

        //
        // �{���͂�����WysISWyg�G�f�B�^�̒��ɓY�t����Ă���摜�t�@�C���Ȃǂ�
        // zip�ɓ˂����ޏ���������
        //

        $zip->close();
        return $filePath;
    }
}