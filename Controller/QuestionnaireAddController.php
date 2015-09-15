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
            //コンテンツの権限設定
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
        // 作成中データ
        $pastQuestionnaires = array();
        $createOption = QuestionnairesComponent::QUESTIONNAIRE_CREATE_OPT_NEW;

        // POSTされたデータを読み取り
        if ($this->request->isPost()) {

            $questionnaire = null;

            // 選択生成方法設定
            if (isset($this->data['create_option'])) {

                $createOption = $this->data['create_option'];

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
            }

            if ($questionnaire) {
                $questionnaire['Questionnaire']['block_id'] = $this->viewVars['blockId'];
                // 作成中アンケートデータをセッションキャッシュに書く
                $this->Session->write('Questionnaires.questionnaire', $questionnaire);

                // 次の画面へリダイレクト
                $this->redirect(array(
                    'controller' => 'questionnaire_questions',
                    'action' => 'edit',
                    $this->viewVars['frameId']
                ));
                return;
            } else {
                // 作成方法の指示がないのにPOSTされているなどのエラーの場合は、画面の再表示とする
                $this->validationErrors['Questionnaire']['create_option'] = __d('questionnaires', 'Please choose create option.');
            }
        }

        // 過去データ 取り出し
        // 表示方法設定値取得
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
        // アンケートデータを新規に作成する
        // 新規作成の場合、タイトル文字のみ画面で設定されPOSTされる
        // Titleをもとに、アンケートデータ基本構成を作成し返す
        $questionnaire = null;

        // POSTデータをセット
        $this->Questionnaire->set($this->request->data);

        // 新規作成時のvalidation実行
        if ($this->Questionnaire->validates(array(
            'fieldList' => array('title')))) {
            // デフォルトデータをもとに新規作成
            $questionnaire = $this->Questionnaire->getDefaultQuestionnaire(array(
                'title' => $this->data['Questionnaire']['title']));
        } else {
            $this->validationErrors = $this->Questionnaire->validationErrors;
        }
        // この場合、Title文字に不備があったらvalidateで自動的にエラーメッセージが入っているから
        // elseを用意する必要がない

        // アンケートデータを返す
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

        // アップロードファイルを受け取り、
        $file = $this->FileUpload->upload('Questionnaire', 'template_file');

        // テンポラリフォルダ作成とカレントディレクトリ変更
        $folder = $this->QuestionnairesDownload->createTemporaryFolder($this, 'template');

        // ファイルを移動
        $importFilePath = $folder->pwd() . DS . QuestionnairesComponent::QUESTIONNAIRE_TEMPLATE_EXPORT_FILENAME;
        move_uploaded_file($file['tmp_name'], $importFilePath);

        // 解凍
        if ($this->__extractZip($importFilePath, $folder->pwd()) === false) {
            $this->validationErrors['Questionnaire']['template_file'] = __d('questionnaires', 'illegal import file.');
            return null;
        }

        // フィンガープリント確認
        $fingerPrint = $this->__checkFingerPrint($folder->pwd());
        if ($fingerPrint === false) {
            $this->validationErrors['Questionnaire']['template_file'] = __d('questionnaires', 'illegal import file.');
            return null;
        }

        // ファイル内容をテンポラリフォルダに展開する。
        $questionnaireZipFile = $folder->pwd() . DS . QuestionnairesComponent::QUESTIONNAIRE_TEMPLATE_FILENAME;
        if ($this->__extractZip($questionnaireZipFile, $folder->pwd()) === false) {
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

        return $questionnaires[0];  // 代表データを返す
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
        $questionnaire = null;

        if (isset($this->data['past_questionnaire_id'])) {
            // 過去のアンケートのコピー・クローンで作成
            $questionnaire = $this->Questionnaire->getQuestionnaireCloneById($this->data['past_questionnaire_id']);
        } else {
            // Modelにはない属性のエラーを入れる
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
            // id, keyはクリアする
            $this->Questionnaire->clearQuestionnaireId($q);

            // WysIsWygのデータを入れなおす
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