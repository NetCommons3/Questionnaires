<?php
/**
 * Created by PhpStorm.
 * User: りか
 * Date: 2015/01/05
 * Time: 17:58
 */

App::uses('AppController', 'Controller');

class QuestionnaireQuestionsController extends QuestionnairesAppController {

    /**
     * use model
     *
     * @var array
     */
    public $uses = array(
        'Questionnaires.Questionnaire',
        'Questionnaires.QuestionnaireEntity',
        'Questionnaires.QuestionnairePage',
        'Questionnaires.QuestionnaireQuestion',
        'Questionnaires.QuestionnaireChoice',
        'Comments.Comment',
        'Questionnaires.QuestionnaireAnswerSummary',
        'Questionnaires.QuestionnaireAnswer',
    );

    /**
     * use components
     *
     * @var array
     */
    public $components = array(
        'NetCommons.NetCommonsBlock', //Use Questionnaire model
        'NetCommons.NetCommonsFrame',
        'NetCommons.NetCommonsRoomRole' => array(
            //コンテンツの権限設定
            'allowedActions' => array(
                'contentEditable' => array('setting_list', 'setting_total'),
            ),
        ),
        'Questionnaires.Questionnaires',
    );
    /**
     * use helpers
     *
     */
    public $helpers = array(
        'NetCommons.Token'
    );
    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('answer', 'confirm', 'total');
    }
    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->set('questionnaire', array('name'=>'FUJIWARA RIKA'));
        $this->view = 'QuestionnaireQuestions/index';
    }

    /**
     * view method
     *
     * @return void
     */
    public function answer($frameId = 0, $questionnaireId = 0) {

        $errors = array();

        // 指定されたアンケート情報を取り出す
        $questionnaireEntity = $this->QuestionnaireEntity->getQuestionnaireEntityById($questionnaireId, $this->viewVars['contentEditable']);
        if (!$questionnaireEntity) {
            throw new NotFoundException(__('Invalid questionnaire'));
        }
        $questions = array();
        foreach($questionnaireEntity['QuestionnairePage'] as $page) {
            foreach($page['QuestionnaireQuestion'] as $q) {
                $this->_shuffleChoice($q, $this->viewVars['contentEditable']);
                $questions[$q['id']] = $q;
            }
        }

        // UserIDの取得
        $userId = $this->Auth->user('id');

        // 強制URLハックのガード
        // 指定のアンケートの状態と回答者の権限を照らし合わせてガードをかける
        // 公開状態にない
        // 期間外
        // 停止中
        // 繰り返し回答
        // 会員以外には許してないのに未ログインである
        if (!$this->Questionnaire->isAbleToAnswer(
            $questionnaireId,
            $this->viewVars['contentEditable'],
            $this->viewVars['roomRoleKey'],
            $userId,
            $this->Session->id())) {
            throw new ForbiddenException(__d('net_commons', 'Permission denied'));
        }

        // ページの指定のない場合はFIRST_PAGE_SEQUENCEをデフォルトとする
        $nextPageSeq = QuestionnairesComponent::FIRST_PAGE_SEQUENCE;    // default
        $skipPageSeq = false;

        // POSTチェック
        if ($this->request->isPost()) {
            // PRE回答がある場合はチェックの上、セッション登録する
            if(isset($this->data['PreAnswer'])) {
                if (!$this->_checkKeyPhrase($questionnaireEntity,$this->data)) {
                    $errors['PreAnswer']['key_phrase'][] = __d('questionnaires', 'Invalid key phrase');
                }
                if (!$this->_checkImageAuth($questionnaireEntity, $this->data)) {
                    $errors['PreAnswer']['image_auth'][] = __d('questionnaires', 'Invalid key Image Auth');
                }
            }
            // 回答データがある場合は回答をDBに書きこむ
            else if(isset($this->data['QuestionnaireAnswer'])) {

                // サマリレコード取得
                $summary = $this->QuestionnaireAnswerSummary->find('first', array(
                    'conditions' => array(
                        'questionnaire_id' => $questionnaireId,
                        'answer_status' => QuestionnairesComponent::ACTION_NOT_ACT,
                        'session_value' => $this->Session->id(),
                        'user_id' => $userId
                    )
                ));
                // なければ作成
                if (!$summary) {
                    $this->QuestionnaireAnswerSummary->create();
                    $this->QuestionnaireAnswerSummary->save(array(
                        'answer_status' => QuestionnairesComponent::ACTION_NOT_ACT,
                        'test_status' => ($questionnaireEntity['QuestionnaireEntity']['status'] != NetCommonsBlockComponent::STATUS_PUBLISHED) ? QuestionnairesComponent::TEST_ANSWER_STATUS_TEST : QuestionnairesComponent::TEST_ANSWER_STATUS_PEFORM,
                        'answer_number' => 1,
                        'questionnaire_id' => $questionnaireId,
                        'session_value' => $this->Session->id(),
                        'user_id' => $userId,
                    ));
                    $summaryId = $this->QuestionnaireAnswerSummary->id;
                }
                else {
                    $summaryId = $summary['QuestionnaireAnswerSummary']['id'];
                }

                // 次に表示するべきページのシーケンス番号を取得する
                $nextPageSeq = $this->data['QuestionnairePage']['page_sequence'] + 1;

                //
                foreach($this->data['QuestionnaireAnswer'] as $aId => $answer) {

                    // 初回回答か再回答かを確認する
                    // 初めは「ID」がPOSTに入っているかどうかで判断しようと思っていたが
                    // Cakeは簡単にブラウザの「戻る」で前画面を表示させたりするので、
                    // POSTのIDは再回答であるにもかかわらず空ってことがありうる
                    // なので毎回DBチェックするしかないかと思う I think so.
                    $pastAnswer = $this->QuestionnaireAnswer->find('first', array(
                        'conditions' => array(
                            'questionnaire_answer_summary_id' => $summaryId,
                            'QuestionnaireAnswer.questionnaire_question_id' => $answer['questionnaire_question_id'],
                            'QuestionnaireAnswer.matrix_choice_id' => $answer['matrix_choice_id'],
                            )
                    ));

                    // 初回のとき
                    if (!$pastAnswer) {
                        $this->QuestionnaireAnswer->create();
                        $answer['questionnaire_answer_summary_id'] = $summaryId;
                    }
                    else {
                        $answer['id'] = $pastAnswer['QuestionnaireAnswer']['id'];
                    }
                    // 質問によってバリデーション動作が変わるので
                    $this->QuestionnaireAnswer->validator()->getField('answer_value')->setRule(
                        'answerValidation',
                        array('rule' => array(
                            'checkAnswerValue',
                            $questions[$answer['questionnaire_question_id']]),
                            'message'=>''
                    ));

                    // データ保存
                    if (!$this->QuestionnaireAnswer->save($answer)) {
                        $errors[$answer['questionnaire_question_id']] = $this->QuestionnaireAnswer->validationErrors;
                        $nextPageSeq = $this->data['QuestionnairePage']['page_sequence'];
                    }

                    // 回答データがあり、かつ、スキップロジックにHITしていたらページを変更する
                    $skipPageSeq = $this->_checkSkipPage($this->data['QuestionnaireAnswer'], $questionnaireEntity);
                }
            }

        }

        // 表示すべきページ番号がFIRST_PAGE_SEQUENCEで
        // かつパスフレーズや画像認証が要求されており、
        // かつ、それらがまだ認証されていない場合
        // ０ページ目としてそれらを表示する
        if (($questionnaireEntity['QuestionnaireEntity']['key_pass_use_flag'] == QuestionnairesComponent::USES_USE
                || $questionnaireEntity['QuestionnaireEntity']['image_authentication_flag'] == QuestionnairesComponent::USES_USE)
            && $nextPageSeq == QuestionnairesComponent::FIRST_PAGE_SEQUENCE) {
            $checkKeyPhrase = true;
            if ($questionnaireEntity['QuestionnaireEntity']['key_pass_use_flag'] == QuestionnairesComponent::USES_USE) {
                $checkKeyPhrase = $this->_checkPreAnswer($questionnaireId, 'key_phrase');
            }
            $checkImageAuth = true;
            if ($questionnaireEntity['QuestionnaireEntity']['image_authentication_flag'] == QuestionnairesComponent::USES_USE) {
                $checkImageAuth = $this->_checkPreAnswer($questionnaireId, 'image_auth');
            }
            if (!$checkKeyPhrase || !$checkImageAuth) {
                // キャンセル時URL
                $this->set('topUrl', $this->Questionnaires->getPageUrl($this->viewVars['frameId']));
                $this->set('isDuringTest', $this->Questionnaire->isDuringTest($questionnaireId, $this->viewVars['contentEditable']));
                $this->set('questionnaire', $questionnaireEntity);
                $this->set('errors', $errors);
                $this->view = 'QuestionnaireQuestions/pre_answer';
                return;
            }
        }

        // 次の指定ページの質問取り出し
        if ($skipPageSeq) {
            $nextPageSeq = $skipPageSeq;
        }

        // 指定ページはすでに存在するページを超える場合ー＞確認画面へ
        // スキップで「最後へ」と指示されている場合ー＞確認画面へ
        if ($this->_checkEndPage($questionnaireEntity, $nextPageSeq)) {
            $this->redirect('confirm/'.$this->viewVars['frameId'].'/'.$questionnaireId);
        }

        // 次のページが普通に存在する場合
        // （すでに回答している場合もあるので、回答も合わせて取り出すこと）
        $nextPage = array();
        if (isset($questionnaireEntity['QuestionnairePage'])) {

            $answerSummary = $this->QuestionnaireAnswerSummary->getProgressiveSummaryOfThisUser($questionnaireId, $userId, $this->Session->id());

            foreach($questionnaireEntity['QuestionnairePage'] as &$page) {
                if ($page['page_sequence'] == $nextPageSeq) {
                    $nextPage = $page;
                    if (isset($nextPage['QuestionnaireQuestion'])) {
                        foreach($nextPage['QuestionnaireQuestion'] as &$question) {

                            if (empty($errors)) {
                                if ($answerSummary) {
                                    $a = Hash::extract($answerSummary[0], 'QuestionnaireAnswer.{n}[questionnaire_question_id='.$question['id'].']');
                                    if ($a) {
                                        $nextPage['QuestionnaireAnswer'][$question['id']] = $a;
                                    }
                                }
                            }
                            else {
                            }
                        }
                    }
                    break;
                }
            }
        }
        // 質問情報をView変数にセット
        $this->set('questionnaire', $questionnaireEntity);
        $this->set('isDuringTest', $this->Questionnaire->isDuringTest($questionnaireId, $this->viewVars['contentEditable']));
        $this->set('comments', $this->getComments($questionnaireEntity['Questionnaire']));
        $this->set('questionPage', $nextPage);
        $this->set('errors', $errors);
    }
    /**
     * confirm method
     *
     * @return void
     */
    public function confirm($frameId = 0, $questionnaireId = 0) {

        // 指定されたアンケート情報を取り出す
        $questionnaireEntity = $this->QuestionnaireEntity->getQuestionnaireEntityById($questionnaireId, $this->viewVars['contentEditable']);
        if (!$questionnaireEntity) {
            throw new NotFoundException(__('Invalid questionnaire'));
        }
        foreach($questionnaireEntity['QuestionnairePage'] as $page) {
            foreach($page['QuestionnaireQuestion'] as $q) {
                $this->_shuffleChoice($q, $this->viewVars['contentEditable']);
                $questions[$q['id']] = $q;
            }
        }

        $userId = $this->Auth->user('id');

        // 回答中サマリレコード取得
        $summary = $this->QuestionnaireAnswerSummary->getProgressiveSummaryOfThisUser(
            $questionnaireId,
            $this->Auth->user('id'),
            $this->Session->id());
        if (!$summary) {
            throw new NotFoundException(__('Invalid answers'));
        }
        $summary = $summary[0];

        // POSTチェック
        if ($this->request->isPost()) {
            // サマリの状態を完了にして確定する
            $summary['QuestionnaireAnswerSummary']['answer_status'] = QuestionnairesComponent::ACTION_ACT;
            $summary['QuestionnaireAnswerSummary']['answer_time'] = $this->getNowTime();
            $this->QuestionnaireAnswerSummary->save($summary['QuestionnaireAnswerSummary']);

            // ありがとう画面へ行く
            $this->redirect('../questionnaires/thanks/'.$this->viewVars['frameId'].'/'.$questionnaireId);
        }

        // 回答情報取得
        $answers = $this->QuestionnaireAnswer->find('all', array(
            'conditions' => array('questionnaire_answer_summary_id' => $summary['QuestionnaireAnswerSummary']['id'])
        ));

        // 回答情報並べ替え
        $setAnswers = array();
        foreach($answers as $answer) {
            $setAnswers[$answer['QuestionnaireAnswer']['questionnaire_question_id']][] = Hash::extract($answer, 'QuestionnaireAnswer');
        }


        // 質問情報をView変数にセット
        $this->set('questionnaireId', $questionnaireId);
        $this->set('questionnaire', $questionnaireEntity);
        $this->set('isDuringTest', $this->Questionnaire->isDuringTest($questionnaireId, $this->viewVars['contentEditable']));
        $this->set('comments', $this->getComments($questionnaireEntity['Questionnaire']));
        $this->set('answers', $setAnswers);
    }
    /**
     * total method
     *
     * @return void
     */
    public function total($frameId = 0, $questionnaireId = 0) {

        $error = array();

        // 指定されたアンケート情報を取り出す
        $questionnaireEntity = $this->QuestionnaireEntity->getQuestionnaireEntityById($questionnaireId, $this->viewVars['contentEditable']);
        if (!$questionnaireEntity) {
            throw new NotFoundException(__('Invalid questionnaire'));
        }
        $questions = array();
        foreach($questionnaireEntity['QuestionnairePage'] as $page) {	//このアンケートのページ毎
            foreach($page['QuestionnaireQuestion'] as $q) {		//このページ中の質問毎

				$conditions = array( 'questionnaire_question_id' => $q['id'] );

				//同時に、questionnaire_question_id毎に、配下のquestionnaire_choice情報を取得し、追記しておく。
				//>choicesをつかわず、recersiveの２階層取得を利用することにした。
				//$q['choices'] = $this->QuestionnaireChoice->getChoicesByQuestionnaireQuestionId($q['id'],$conditions,true);

				//各質問＋選択子($q)情報を、 $questions[(questionnaire_question_id値)]に格納していく。
                $questions[$q['id']] = $q;	
            }
        }

        $userId = isset($this->viewVars['userId']) ? $this->viewVars['userId'] : null;	//ユーザidを取り出す。

		//集計表示していいかどうかの判断

        if (!$this->Questionnaire->isAbleToDisplayAggrigatedData(
            $questionnaireId,
            $this->viewVars['contentEditable'],
            $this->viewVars['roomRoleKey'],
            $userId,
            $this->Session->id())) {
            throw new ForbiddenException(__d('net_commons', 'Permission denied'));
        }

        // POSTチェック ... 回答の場合、PRE回答などがあり、save()が走るが、集計は参照onlyなので、POSTチェックは不要

        //$this->log('DBG: questionnaireEntity['.print_r($questionnaireEntity,true).']','debug');
        //$this->log('DBG: questions['. print_r($questions,true) .']', 'debug');


		//集計処理を行います。
		$this->__aggrigateAnswer($questionnaireId,$questionnaireEntity,$this->viewVars['contentEditable'],$questions);


        //ニックネームとuser_idを取り出す.　QuestinnaireControllerに合わせる。
        $username = CakeSession::read('Auth.User.username');
        $username = empty($username) ?  __d('questionnaires','annonymous person') : $username;
        //$user_id = CakeSession::read('Auth.User.id');
        //$user_id = empty($user_id) ? null : $user_id;

        //画面用データをセットする。
    	$this->set('frameId', $frameId);							//ng-initのinitilize()引数用
        $this->set('questionnaire', array('name'=>$username));		//ng-initのinitilize()引数用
		$this->set('topUrl', $this->Questionnaires->getPageUrl($this->viewVars['frameId']));	//戻るボタン用

		$this->set('questionnaireId', $questionnaireId);
		$this->set('questionnaireEntity',$questionnaireEntity);
		$this->set('questions', $questions);

		//集計結果を、Viewに渡し、表示してもらう。
        //$this->view = 'QuestionnaireQuestions/total';
    }
    /**
     * setting method
     *
     * @return void
     */
    public function setting_list() {

        if($this->request->isPost()) {

            $postQuestionnaire = $this->request->data;
            $questionnaire = $this->Session->read('Questionnaires.questionnaire');

            $questionnaire = $this->questionnaire_array_merge_recursive_distinct($questionnaire, $postQuestionnaire);


            // それをキャッシュに書く
            $this->Session->write('Questionnaires.questionnaire', $questionnaire);

            // 次の画面へリダイレクト
            $this->redirect('setting_total/'.$this->viewVars['frameId']);
        }
        else {
            // redirectで来るか、もしくは本当に直接のURL指定で来るかのどちらか
            // クエリでアンケートIDの指定がある場合はそちらを優先
            if(!empty($this->request->query['questionnaire_id'])) {
                $selectedQuestionnaireId = $this->request->query['questionnaire_id'];

                // 指定されたアンケートデータを取得
                $questionnaire = $this->QuestionnaireEntity->getQuestionnaireEntityById($selectedQuestionnaireId, $this->viewVars['contentEditable']);

                // 前の画面で指定された値をセッションキャッシュに設定
                $this->Session->write('Questionnaires.QuestionnairesSettingList.selectedQuestionnaireId', $selectedQuestionnaireId);
            }
            // クエリがない場合はセッションを確認
            else if($this->Session->check('Questionnaires.questionnaire')) {
                $questionnaire = $this->Session->read('Questionnaires.questionnaire');

            }
            // それもない場合はエラー？空の作成？　TODO
            else {

            }

            // 作成・取り出しだけでは不足項目がある場合がある。補完する
            $this->QuestionnaireEntity->supplementQuestionnaire($questionnaire);

            // それをキャッシュに書く
            $this->Session->write('Questionnaires.questionnaire', $questionnaire);

            $this->set('questionnaire', $questionnaire);
            $this->set('contentStatus', $questionnaire['QuestionnaireEntity']['status']);
            $this->set('comments', $this->getComments($questionnaire['Questionnaire']));

            $this->set('topUrl', $this->Questionnaires->getPageUrl($this->viewVars['frameId']));
            $this->set('backUrl', '/' . $this->Session->read('Questionnaires.nowUrl'));
            $this->set('questionTypeOptions', $this->Questionnaires->getQuestionTypeOptionsWithLabel());
        }
    }
    /**
     * setting method
     *
     * @return void
     */
    public function setting_total() {
        if($this->request->isPost()) {

            $postQuestionnaire = $this->request->data;

            $questionnaire = $this->Session->read('Questionnaires.questionnaire');

            $questionnaire = $this->array_merge_recursive_distinct($questionnaire, $postQuestionnaire);

            // それをキャッシュに書く
            $this->Session->write('Questionnaires.questionnaire', $questionnaire);

            // 次の画面へリダイレクト
            $this->redirect('../questionnaires/setting/'.$this->viewVars['frameId']);
        }
        else {
            // redirectで来るか、もしくは本当に直接のURL指定で来るかのどちらか
            // クエリでアンケートIDの指定がある場合はそちらを優先
            if(!empty($this->request->query['questionnaire_id'])) {
                $selectedQuestionnaireId = $this->request->query['questionnaire_id'];

                // 指定されたアンケートデータを取得
                $questionnaire = $this->QuestionnaireEntity->getQuestionnaireEntityById($selectedQuestionnaireId, $this->viewVars['contentEditable']);
            }
            // クエリがない場合はセッションを確認
            else if($this->Session->check('Questionnaires.questionnaire')) {
                $questionnaire = $this->Session->read('Questionnaires.questionnaire');
            }
            // それもない場合はエラー　TODO
            else {
                var_dump("ERR");
            }

            // 作成・取り出しだけでは不足項目がある場合がある。補完する
            $this->QuestionnaireEntity->supplementQuestionnaire($questionnaire);


            $this->set('questionnaire', $questionnaire);
            $this->set('contentStatus', $questionnaire['QuestionnaireEntity']['status']);
            $this->set('comments', $this->getComments($questionnaire['Questionnaire']));

            $this->set('topUrl', $this->Questionnaires->getPageUrl($this->viewVars['frameId']));
            $this->set('backUrl', '/questionnaires/questionnaire_questions/setting_list/' . $this->viewVars['frameId']);
        }
    }

    /**
     * check skip page method
     * 回答にスキップロジックで指定されたものがないかチェックし、行き先があるならそのページ番号を返す
     * 何もないときはfalse
     * @return pageId or false
     */
    private function _checkSkipPage($answers, $questionnaireEntity) {
        try {
            foreach($answers as $answer) {
                $q = $this->QuestionnaireQuestion->findById($answer['questionnaire_question_id']);
                if ($q){
                    if ($q['QuestionnaireQuestion']['skip_flag'] == QuestionnairesComponent::SKIP_FLAGS_SKIP) {
                        list($choice_id,$value) = explode(':', trim($answer['answer_value'], '|'));
                        $choice = $this->QuestionnaireChoice->findById($choice_id);
                        if ($choice) {
                            return $choice['QuestionnaireChoice']['skip_page_sequence'];
                        }
                        else {
                            return false;   // TODO
                        }
                    }
                }
                else {
                    return false;   // TODO
                }
            }
        } catch(Exception $ex) {
            CakeLog::error($ex);
            throw $ex;  // TODO
        }
    }

    /**
     * _checkPreAnswer
     * アンケート開始前にキーフレーズの入力や画像認証が求められている場合
     * それらの回答がすんでいるかどうかをチェックする
     * @param $questionnaireId
     * @return boolean
     */
    private function _checkPreAnswer($questionnaireId, $checkType) {
        $check = $this->Session->check('Questionnaires.' . $questionnaireId . '.' . $checkType);
        if ($check) {
            return true;
        }
        return false;
    }

    /**
     * _checkKeyPhrase
     * アンケート開始前にキーフレーズの入力が求められている場合の入力パスフレーズ確認
     * あっていたらセッションに書きこむ
     * @param $questionnaireEntity アンケート情報
     * @param $data 入力データ
     * @return boolean
     */
    private function _checkKeyPhrase($questionnaireEntity, $data) {
        if ($questionnaireEntity['QuestionnaireEntity']['key_phrase'] == $data['PreAnswer']['key_phrase']) {
            $this->Session->write('Questionnaires.' . $questionnaireEntity['QuestionnaireEntity']['questionnaire_id'] . '.key_phrase', true);
            return true;
        }
        return false;
    }
    private function _checkImageAuth($data) {   // TODO
        return true;
    }
    /**
     * _checkEndPage
     * 指定された次ページはすでにアンケートの最後になるか
     * @param $questionnaireEntity アンケート情報
     * @param $nextPageSeq 指定次ページ
     * @return boolean
     */
    private function _checkEndPage($questionnaireEntity, $nextPageSeq) {
        if ($nextPageSeq == QuestionnairesComponent::SKIP_GO_TO_END) {
            return true;
        }

        // ページ配列はページのシーケンス番号順に取り出されているので
        $pages = $questionnaireEntity['QuestionnairePage'];
        $endPage = end($pages);
        if ($endPage['page_sequence'] < $nextPageSeq) {
            return true;
        }
        return false;
    }
    /**
     * _shuffleChoice
     * 選択肢をシャッフルする（セッションに書く
     *
     */
    private function _shuffleChoice(&$question, $contentEditable) {
        $choices = $question['QuestionnaireChoice'];
        if (!$contentEditable && $question['choice_random_flag'] == QuestionnairesComponent::USES_USE) {
            if ($this->Session->check('Questionnaires.QuestionnaireQuestion.'.$question['id'].'.QuestionnaireChoice')) {
                $choices = $this->Session->read('Questionnaire.QuestionnaireQuestion.'.$question['id'].'.QuestionnaireChoice');
            }
            else {
                shuffle($choices);
                $this->Session->write('Questionnaire.QuestionnaireQuestion.'.$question['id'].'.QuestionnaireChoice', $choices);
            }
        }
        $question['QuestionnaireChoice'] = $choices;
    }
	/**
	 * __aggrigateAnswer
	 * 集計処理の実施
     * @param $entity アンケート情報
	 * @param $contentEditable 編集可能フラグ
     * @param $questions アンケート質問(集計結果を配列追加して返します)
     * @return void
	 */
	private function __aggrigateAnswer($questionnaireId, $entity,$contentEditable, &$questions)
	{

		//公開時は本番時回答、テスト時(=非公開時)はテスト時回答を対象とする。
       	if ($this->Questionnaire->isDuringTest($questionnaireId,$contentEditable)) {
			$test_status = QuestionnairesComponent::TEST_ANSWER_STATUS_TEST;
		} else {
			$test_status = QuestionnairesComponent::TEST_ANSWER_STATUS_PEFORM;
		}

		$conditions = array(
			'answer_status' => QuestionnairesComponent::ACTION_ACT,
        	'test_status' => $test_status,		
			'questionnaire_id' => $questionnaireId
		);

        $summaries = $this->QuestionnaireAnswerSummary->find('all', array(
            'conditions' => $conditions
        ));
		//$this->log('DBGDBG: summaries['.print_r($summaries,true).']','debug');


		//$this->log('DBG: summaries['.print_r($summaries,true).']','debug');

		//質問毎に、まとめあげる.
		//$questionsは、questionnaire_question_idをキーとし、questionnaire_question配下が代入されている。
		//
		foreach ($questions as $questionnaireQuestionId => &$questionnaireQuestion) {
			if ($questionnaireQuestion['result_display_flag'] != QuestionnairesComponent::EXPRESSION_SHOW) {
				//集計表示をしない、なので飛ばす
				continue;
			}
			// questionnaire_question_idが一致する回答データをすべて抜き出し、answer_value毎に合算する。
        	$answer_total_cnt = $this->__getEachAggregatedAnswerByQuestionnaireQuestionId($questionnaireId,$questionnaireQuestion,$summaries);
			
			// 戻り値の、この質問の合計回答数を記録しておく。
			$questionnaireQuestion['answer_total_cnt'] = $answer_total_cnt;
		}

		return;
	}

	/**
	 * __getEachAggregatedAnswerByQuestionnaireQuestionId このアンケートの回答群より、
	 * 指定した質問IDに対応する回答群を抜き出し、集計結果を含めた形で返す。
	 * @param $questionnaireId
	 * @param $questionnaireQuestion 　選択肢情報つき質問情報(ここに集計結果をつめて返す）
	 * @param $summaries このアンケートの全回答群
	 * @return $answer_total_cnt  この質問(questionnaireQuestion)の合計回答数
	 */
	private function __getEachAggregatedAnswerByQuestionnaireQuestionId($questionnaireId, &$questionnaireQuestion, &$summaries)
	{
       	$answer_total_cnt = 0;
		foreach ($summaries as $summary) {
			foreach ($summary['QuestionnaireAnswer'] as $questionnaireAnswer) {

				//$this->log("q_q_id[".$questionnaireQuestion['id']."] vs q_q_id of q_a[".$questionnaireAnswer['questionnaire_question_id']."]",'debug');
				if ($questionnaireQuestion['id'] == $questionnaireAnswer['questionnaire_question_id']) {

					//対応する回答がみつかったので、集計実施. 結果は$questionnaireQuestionに追記
				
					//必須でない場合、未回答＝対応する回答データなしとなる。
					//つまり、合計回答数と合計回答者数とは一致しない。ここで加算しているのは、合計回答数の方となる。
       				++$answer_total_cnt;
					
					$this->__aggrigate($questionnaireId,$questionnaireQuestion,$questionnaireAnswer);
				}
			}
		}

		if ( $questionnaireQuestion['question_type'] == QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST ||
					$questionnaireQuestion['question_type'] == QuestionnairesComponent::TYPE_MATRIX_MULTIPLE ) {
			//マトリクス回答

			//マトリクスの場合、選択肢「行」数が１セット回答なので、同行数で割らないと、正しい合計回答数にならない。
			//値を調整してから返す。
			$choice_row_num = 0;
			foreach($questionnaireQuestion['QuestionnaireChoice'] as $questionnaireChoice){
				if ($questionnaireChoice['matrix_type']==QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX) {
					++$choice_row_num;
				}
			}
			if($choice_row_num > 0){
				return intval($answer_total_cnt / $choice_row_num);	//割り切れるはずだが、念のため整数にして返す。
			}
			else {
				//０除算になるので、おかしい.暫定的にそのまま値を返す。
				return $answer_total_cnt;
			}
		}

		//非マトリクス回答
		return $answer_total_cnt;
	}

	/**
	 * __aggrigate 集計実施
	 * @param $questionnaireId アンケートID
	 * @param $questionnaireQuestion アンケートの質問。集計結果はここに追記して返す。
	 * @param $questionnaireAnswer  アンケートの回答。
	 * @return void
	 */
	private function __aggrigate($questionnaireId,&$questionnaireQuestion,&$questionnaireAnswer)
	{

		if ($questionnaireQuestion['question_type'] == QuestionnairesComponent::TYPE_SELECTION  ||
			$questionnaireQuestion['question_type'] == QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX ||
			$questionnaireQuestion['question_type'] == QuestionnairesComponent::TYPE_MULTIPLE_SELECTION) {
			//単一選択またはリスト選択. (違いはその他あるかないかだけで、集計上は同じ)
			//choiceデータにaggrigate_totalフィールドを追加し、そこに加算していく。
			//複数選択も、(複数の)choiceデータにaggrigate_totalフィールドを追加し、そこに加算していく
			//ので処理としては同じ。
			
			$isMatrix = false;
			$questionnaireQuestion['isMatrix'] = $isMatrix;	//記録しておく
			$this->__incrementChoiceAggrigateTotalEx($questionnaireQuestion,$questionnaireAnswer,$isMatrix);

		} else if ( $questionnaireQuestion['question_type'] == QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST ||
					$questionnaireQuestion['question_type'] == QuestionnairesComponent::TYPE_MATRIX_MULTIPLE ) {
			//マトリクス(単一)はここ。
			//マトリクス(複数)も、ここで対応できるようにしています。

			$isMatrix = true;
			$questionnaireQuestion['isMatrix'] = $isMatrix;	//記録しておく
			$this->__incrementChoiceAggrigateTotalEx($questionnaireQuestion,$questionnaireAnswer,$isMatrix);

		} else {
			// TYPE_TEXT (テキスト) 、 TYPE_TEXT_AREA(テキストエリア)、 TYPE_DATE_AND_TIME(日付・時刻)
			; // これらは定性データなので集計不可。よって処理しない。
		}

		//集計結果を確認
		//$this->log("DBG2: __aggrigate(q_id[".$questionnaireId."] question[".print_r($questionnaireQuestion,true)."] answer[".print_r($questionnaireAnswer,true)."]",'debug');
		return;
	}

//	/**
//	 * __incrementChoiceAggrigateTotal 回答の集計トータル加算
//	 * @param $questionnaireQuestion アンケートの質問。集計結果はここに追記して返す。
//	 * @param $questionnaireAnswer  アンケートの回答
//	 * @return void
//	 */
//	private function __incrementChoiceAggrigateTotal(&$questionnaireQuestion,&$questionnaireAnswer)
//	{
//		$answers = $this->__splitAnswerStr($questionnaireAnswer['answer_value']);
//		foreach($answers as $id => $val){	//複数選択を考慮し、LOOP
//			foreach($questionnaireQuestion['QuestionnaireChoice'] as &$questionnaireChoice){
//				if ($questionnaireChoice['id'] == $id) {
//					if (!isset($questionnaireChoice['aggrigate_total'])) {
//						$questionnaireChoice['aggrigate_total'] = 0;
//					}
//					++$questionnaireChoice['aggrigate_total'];	//前置加算で多少高速化
//					continue;
//				}
//			}
//		}
//		return;
//	}

	/**
	 * __incrementChoiceAggrigateTotalEx  (マトリクス・非マトリクス両方に対応した）回答の集計トータル加算
	 * @param $questionnaireQuestion アンケートの質問。集計結果はここに追記して返す。
	 * @param $questionnaireAnswer  アンケートの回答
	 * @param $isMatrix マトリクスかどうか (default false)
	 * @return void
	 */
	private function __incrementChoiceAggrigateTotalEx(&$questionnaireQuestion,&$questionnaireAnswer,$isMatrix=false)
	{
		//複数選択に対応するため、answer_valueを分割展開しておく。
		$answers = $this->__splitAnswerStr($questionnaireAnswer['answer_value']);

		foreach($answers as $id => $val){	//複数選択を考慮し、LOOP
			foreach($questionnaireQuestion['QuestionnaireChoice'] as &$questionnaireChoice){

				if ($isMatrix) {
					//マトリクスの場合
					//matrix_choice_idに、questionnaire_choiceの(matrix_type==行(0))のレコードのquestionnaire_choice_idが格納されている。
					//よって、それを、選択肢（行）idとする
					//answer_valueに格納されている$idを、選択肢（列）idとする。
			
					$row_choice_id = $questionnaireAnswer['matrix_choice_id'];
					$col_choice_id = $id;
					
				} else {
					//非マトリクスでは、分割展開された回答の$idが、ターゲットのchoice_idである。
					//よって、それを、選択肢（行）idとする.
					//選択肢（列）idというものは本来存在しないので、固定的な値にしておく。

					$row_choice_id = $id;
					$col_choice_id = QuestionnairesComponent::AGGRIGATE_NOT_MATRIX;
				}

				if ($questionnaireChoice['id'] == $row_choice_id) {
					//対象のマトリクスの選択肢データ(マトリクスの場合は、選択肢「行」)

					//集計合計「配列」がなければ作成.
					if (!isset($questionnaireChoice['aggrigate_total'])) {
						$questionnaireChoice['aggrigate_total'] = array();
					}

					//集計合計「配列」内に、選択肢「列」がなければ作成
					//注）非マトリクスの場合、$idに後から判別できる固定値(文字列)を使う。
					//
					if (!isset($questionnaireChoice['aggrigate_total'][$col_choice_id])) {
						$questionnaireChoice['aggrigate_total'][$col_choice_id] = 0;
					}

					++$questionnaireChoice['aggrigate_total'][$col_choice_id];	//前置加算で多少高速化
					continue;
				}

			}
		}
		return;
	}

	/**
	 * __splitAnswerStr |,:で連結された回答列を分割し、配列に格納して返す
	 * @param $answerStr
	 * @return $answers
	 */
	private function __splitAnswerStr($answerStr) 
	{
		$answers = array();
		$elms = explode(QuestionnairesComponent::ANSWER_DELIMITER, $answerStr);
		foreach ($elms as $elm) {
			////list($id,$val) = explode(':',$elm); //$answerStrが''のケースがあるので、考慮追加。
			$idval = explode(QuestionnairesComponent::ANSWER_VALUE_DELIMITER, $elm);
			if (count($idval)==2) {
				$answers[$idval[0]] = $idval[1];
			}
		}
		return $answers;
	}

}
