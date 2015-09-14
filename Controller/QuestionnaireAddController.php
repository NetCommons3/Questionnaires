<?php
/**
 * QuestionnairesAdd Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * QuestionnairesAddController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuestionnaireAddController extends QuestionnairesAppController
{

    /**
     * use model
     *
     * @var array
     */
    public $uses = array(
        'Questionnaires.Questionnaire',
        'Questionnaires.QuestionnairePage',
        'Questionnaires.QuestionnaireQuestion',
        'Questionnaires.QuestionnaireChoice',
        'Questionnaires.QuestionnaireAnswerSummary',
        'Comments.Comment',
        'Files.FileModel',					// FileUpload
        'PluginManager.Plugin',
        'Questionnaires.QuestionnaireFrameDisplayQuestionnaire',
    );

    /**
     * use components
     *
     * @var array
     */
    public $components = array(
        'Files.FileUpload',					// FileUpload
        'NetCommons.NetCommonsBlock', //Use Questionnaire model
        'NetCommons.NetCommonsFrame',
        'NetCommons.NetCommonsRoomRole' => array(
            //�R���e���c�̌����ݒ�
            'allowedActions' => array(
                'contentEditable' => array('add')
            ),
        ),
        'Questionnaires.Questionnaires',
        'Questionnaires.QuestionnairesDownload',
        'Questionnaires.QuestionnairesWysIsWyg',
    );

    /**
     * use helpers
     *
     * @var array
     */
    public $helpers = array(
        'NetCommons.Token',
        'NetCommons.Date',
        'NetCommons.BackToPage',
        'Questionnaires.QuestionnaireStatusLabel',
        'Questionnaires.QuestionnaireUtil'
    );
/**
 * add questionnaire display method
 *
 * @return void
 */
    public function add() {
        // �쐬���f�[�^
        $pastQuestionnaires = array();
        $createOption = QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW;

        // POST���ꂽ�f�[�^��ǂݎ��
        if ($this->request->isPost()) {

            $questionnaire = null;

            // �I�𐶐����@�ݒ�
            if (isset($this->data['create_option'])) {

                $createOption = $this->data['create_option'];

                // �w�肳�ꂽ�쐬�̃I�v�V�����ɂ���ď�������
                if ($createOption == QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW) {
                    // ��̐V�K�쐬
                    $questionnaire = $this->_createNew();
                } elseif ($createOption == QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_REUSE) {
                    // �ߋ��f�[�^����̍쐬
                    $questionnaire = $this->_createFromReuse();
                } elseif ($createOption == QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_TEMPLATE) {
                    // �e���v���[�g�t�@�C������̍쐬
                    $questionnaire = $this->_createFromTemplate();
                }
            }

            if ($questionnaire) {
                $questionnaire['Questionnaire']['block_id'] = $this->viewVars['blockId'];
                // �쐬���A���P�[�g�f�[�^���Z�b�V�����L���b�V���ɏ���
                $this->Session->write('Questionnaires.questionnaire', $questionnaire);

                // ���̉�ʂփ��_�C���N�g
                $this->redirect(array(
                    'controller' => 'questionnaire_questions',
                    'action' => 'edit',
                    $this->viewVars['frameId']
                ));
                return;
            } else {
                // �쐬���@�̎w�����Ȃ��̂�POST����Ă���Ȃǂ̃G���[�̏ꍇ�́A��ʂ̍ĕ\���Ƃ���
                $this->validationErrors['Questionnaire']['create_option'] = __d('questionnaires', 'Please choose create option.');
            }
        }

        // �ߋ��f�[�^ ���o��
        // �\�����@�ݒ�l�擾
        $settings = $this->QuestionnaireFrameSetting->getQuestionnaireFrameSetting($this->viewVars['frameId']);
        $conditions = $this->Questionnaire->getConditionForAnswer(
            $this->viewVars['blockId'],
            $this->Auth->user('id'),
            $this->viewVars,
            $this->getNowTime()
        );
        $pastQuestionnaires['items'] = $this->Questionnaire->getQuestionnairesList(
            $conditions,
            $this->Session->id(),
            $this->Auth->user('id'),
            array(),
            $settings[1] . ' ' . $settings[2],	// 1:sort 2:direction
            0,
            1000
        );

        $this->set('jsPastQuestionnaires', $this->camelizeKeyRecursive($this->_changeBooleansToNumbers($pastQuestionnaires)));
        $this->set('createOption', $createOption);
    }

/**
 * _createNew
 *
 * @return array QuestionnaireData
 */
    protected function _createNew() {
        // �A���P�[�g�f�[�^��V�K�ɍ쐬����
        // �V�K�쐬�̏ꍇ�A�^�C�g�������̂݉�ʂŐݒ肳��POST�����
        // Title�����ƂɁA�A���P�[�g�f�[�^��{�\�����쐬���Ԃ�
        $questionnaire = null;

        // POST�f�[�^���Z�b�g
        $this->Questionnaire->set($this->request->data);

        // �V�K�쐬����validation���s
        if ($this->Questionnaire->validates(array(
            'fieldList' => array('title')))) {
            // �f�t�H���g�f�[�^�����ƂɐV�K�쐬
            $questionnaire = $this->Questionnaire->getDefaultQuestionnaire(array(
                'title' => $this->data['Questionnaire']['title']));
        } else {
            $this->validationErrors = $this->Questionnaire->validationErrors;
        }
        // ���̏ꍇ�ATitle�����ɕs������������validate�Ŏ����I�ɃG���[���b�Z�[�W�������Ă��邩��
        // else��p�ӂ���K�v���Ȃ�

        // �A���P�[�g�f�[�^��Ԃ�
        return $questionnaire;
   }
/**
 * _createFromTemplate
 *
 * @return array QuestionnaireData
 */
    protected function _createFromTemplate() {
        // �A���P�[�g�f�[�^��UPLOAD���ꂽ�A���P�[�g�e���v���[�g�t�@�C���̃f�[�^�����Ƃɂ��č쐬����
        // �e���v���[�g����̍쐬�̏ꍇ�A�e���v���[�g�t�@�C����UPLOAD����Ă���
        // �A�b�v���ꂽ�t�@�C�������ƂɁA�A���P�[�g�f�[�^���𓀁A�擾���A
        // ���̃f�[�^���獡��쐬����A���P�[�g�f�[�^��{�\�����쐬���Ԃ�

        // �A�b�v���[�h�t�@�C�����󂯎��A
        $file = $this->FileUpload->upload('Questionnaire', 'template_file');

        // �e���|�����t�H���_�쐬�ƃJ�����g�f�B���N�g���ύX
        $folder = $this->QuestionnairesDownload->createTemporaryFolder($this, 'template');

        // �t�@�C�����ړ�
        $importFilePath = $folder->pwd() . DS . QuestionnairesComponent::QUESTIONNAIRE_TEMPLATE_EXPORT_FILENAME;
        move_uploaded_file($file['tmp_name'], $importFilePath);

        // ��
        if ($this->__extractZip($importFilePath, $folder->pwd()) === false) {
            $this->validationErrors['Questionnaire']['template_file'] = __d('questionnaires', 'illegal import file.');
            return null;
        }

        // �t�B���K�[�v�����g�m�F
        $fingerPrint = $this->__checkFingerPrint($folder->pwd());
        if ($fingerPrint === false) {
            $this->validationErrors['Questionnaire']['template_file'] = __d('questionnaires', 'illegal import file.');
            return null;
        }

        // �t�@�C�����e���e���|�����t�H���_�ɓW�J����B
        $questionnaireZipFile = $folder->pwd() . DS . QuestionnairesComponent::QUESTIONNAIRE_TEMPLATE_FILENAME;
        if ($this->__extractZip($questionnaireZipFile, $folder->pwd()) === false) {
            $this->validationErrors['Questionnaire']['template_file'] = __d('questionnaires', 'illegal import file.');
            return null;
        }

        // json�t�@�C����ǂݎ��APHP�I�u�W�F�N�g�ɕϊ�
        $jsonFilePath = $folder->pwd() . DS . QuestionnairesComponent::QUESTIONNAIRE_JSON_FILENAME;
        $jsonFileFp = fopen($jsonFilePath, 'rb');
        $jsonData = fread($jsonFileFp, filesize($jsonFilePath));
        $jsonQuestionnaire = json_decode($jsonData, true);

        // ���߂Ƀt�@�C���ɋL�ڂ���Ă���A���P�[�g�v���O�C���̃o�[�W������
        // ���T�C�g�̃A���P�[�g�v���O�C���̃o�[�W������ˍ����A����������ꍇ�̓C���|�[�g�����𒆒f����B
        if ($this->__checkVersion($jsonQuestionnaire) === false) {
            $this->validationErrors['Questionnaire']['template_file'] = __d('questionnaires', 'version is different.');
            return null;
        }

        // �o�[�W��������v�����ꍇ�A�A���P�[�g�f�[�^����������ɍ\�z
        $questionnaires = $this->_getQuestionnaires(
            $folder->pwd(),
            $jsonQuestionnaire['Questionnaires'],
            $fingerPrint);

        // �C���|�[�g�f�[�^�u�I���W�i���f�[�^�v���Z�b�V�����ɏ�������ł���
        $this->Session->write('Questionnaires.importQuestionnaire', $questionnaires);

        return $questionnaires[0];  // ��\�f�[�^��Ԃ�
   }
/**
 * _createFromReuse
 *
 * @return array QuestionnaireData
 */
    protected function _createFromReuse() {
        // �A���P�[�g�f�[�^���ߋ��̃A���P�[�g�f�[�^�����Ƃɂ��č쐬����
        // �ߋ�����̍쐬�̏ꍇ�A�Q�l�ɂ���ߋ��̃A���P�[�g��id�̂�POST����Ă���
        // (orgin_id�ł͂Ȃ�id�ł���_�ɒ��ӁI)
        // id�����ƂɁA�ߋ��̃A���P�[�g�f�[�^���擾���A
        // ���̃f�[�^���獡��쐬����A���P�[�g�f�[�^��{�\�����쐬���Ԃ�
        $questionnaire = null;

        if (isset($this->data['past_questionnaire_id'])) {
            // �ߋ��̃A���P�[�g�̃R�s�[�E�N���[���ō쐬
            $questionnaire = $this->Questionnaire->getQuestionnaireCloneById($this->data['past_questionnaire_id']);
        } else {
            // Model�ɂ͂Ȃ������̃G���[������
            $this->validationErrors['Questionnaire']['past_questionnaire_id'] = __d('questionnaires', 'Please select past questionnaire.');
        }
        return $questionnaire;
    }
/**
 * _getQuestionnaires
 *
 * @param string $folderPath path string to import zip file exist
 * @param array $questionnaires questionnaire data in import json file
 * @param $importKey import key (hash string)
 * @return array QuestionnaireData
 */
    protected function _getQuestionnaires($folderPath, $questionnaires, $importKey) {

        foreach ($questionnaires as &$q) {
            // id, key�̓N���A����
            $this->Questionnaire->clearQuestionnaireId($q);

            // WysIsWyg�̃f�[�^�����Ȃ���
            $flatQuestionnaire = Hash::flatten($q);
            foreach ($flatQuestionnaire as $key => &$value) {
                $model = null;
                if (strpos($key, 'QuestionnaireQuestion.') !== false) {
                    $model = $this->QuestionnaireQuestion;
                } else if (strpos($key, 'QuestionnairePage.') !== false) {
                    $model = $this->QuestionnairePage;
                } else if (strpos($key, 'Questionnaire.') !== false) {
                    $model = $this->Questionnaire;
                }
                if (!$model) {
                    continue;
                }
                $columnName = substr($key, strrpos($key, '.') + 1);
                if ($model->hasField($columnName)) {
                    if ($model->getColumnType($columnName) == 'text') {
                        // key�Ɠ������O�̃t�H���_�̉��ɂ���key�̖��O��ZIP�t�@�C����n����
                        // ���̕Ԃ��Ă����l�����̃J�����ɐݒ�
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
 * __extractZip
 *
 * @param string $filePath zip file path
 * @param string $folderPath folder path
 * @return bool
 */
    private function __extractZip($filePath, $folderPath) {
        $zip = new ZipArchive;
        if ($zip->open($filePath) !== true) {
            return false;
        }
        if ($zip->extractTo($folderPath) === false) {
            return false;
        }
        $zip->close();
        return true;
    }
/**
 * __checkFingerPrint
 *
 * @param string $folderPath folder path
 * @return string finger print string
 */
    private function __checkFingerPrint($folderPath) {
        // �t�B���K�[�v�����g�t�@�C�����擾
        $fingerPrintFp = fopen($folderPath . DS . QuestionnairesComponent::QUESTIONNAIRE_FINGER_PRINT_FILENAME, 'rb');
        if ($fingerPrintFp === false) {
            return false;
        }
        $fingerPrint = fread($fingerPrintFp, 1024);

        // �t�@�C�����e����Z�o�����n�b�V���l�Ǝw�肳�ꂽ�t�b�g�v�����g�l���r��
        // ����ł���ΐ��������ۏ؂��ꂽ�Ɣ��f����i�t�H�[�}�b�g�`�F�b�N�Ȃǂ͍s��Ȃ��j
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
 * @param array $jsonData
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