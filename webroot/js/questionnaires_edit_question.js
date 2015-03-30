/**
 * Created by りか on 2015/02/18.
 */


NetCommonsApp.controller('Questionnaires.edit.question',
    function($scope, NetCommonsBase, NetCommonsWysiwyg,
             NetCommonsUser, $timeout, dialogs) {

        /**
         * plugin
         *
         * @type {object}
         */
        $scope.plugin = NetCommonsBase.initUrl('questionnaires', 'questionnaire_questions');

        /**
         * show user information method
         *
         * @param {number} users.id
         * @return {string}
         */
        $scope.user = NetCommonsUser.new();

        /**
         * tinymce
         *
         * @type {object}
         */
        $scope.tinymce = NetCommonsWysiwyg.new();

        /**
         * serverValidationClear method
         *
         * @param {number} users.id
         * @return {string}
         */
        $scope.serverValidationClear = NetCommonsBase.serverValidationClear;

        /**
         * variables
         *
         * @type {Object.<string>}
         */
        var variables = {
            /**
             * Relative path to login form
             *
             * @const
             */
            OTHER_CHOICE_TYPE_NO_OTHER_FILED: '0',
            TYPE_OPTION_NUMERIC: '1',
            TYPE_OPTION_DATE: '2',
            TYPE_OPTION_TIME: '3',
            TYPE_OPTION_EMAIL: '4',
            TYPE_OPTION_URL: '5',
            TYPE_OPTION_PHONE_NUMBER: '6',
            TYPE_OPTION_DATE_TIME: '7'

        };

        /**
         * Initialize
         *
         * @return {void}
         */
        $scope.initialize = function(frameId, questionnaire) {
            $scope.frameId = frameId;
            $scope.questionnaire = questionnaire;
            for(var pIdx=0 ; pIdx<$scope.questionnaire.QuestionnairePage.length ; pIdx++) {
                for(var qIdx=0 ; qIdx<$scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion.length ; qIdx++) {
                    // 各質問の選択肢があればその選択肢の中に「その他」が入っているかの確認とフラグ設定
                    for(var cIdx=0 ; cIdx<$scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].QuestionnaireChoice.length ; cIdx++) {
                        if($scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].QuestionnaireChoice[cIdx].other_choice_type != variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
                            $scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].has_another_choice = true;
                            break;
                        }
                    }
                    // 各質問が日付・時刻のタイプならば、範囲設定があるかの確認
                    $scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].setDateTimeRange = false;
                    if($scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].question_type_option == variables.TYPE_OPTION_DATE ||
                       $scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].question_type_option == variables.TYPE_OPTION_TIME ||
                       $scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].question_type_option == variables.TYPE_OPTION_DATE_TIME) {
                        if($scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].min.length > 0 ) {
                            $scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].setDateTimeRange = true;
                            var d = new Date($scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].min);
                            $scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].min = d;
                        }
                        if($scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].max.length > 0) {
                            $scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].setDateTimeRange = true;
                            var d = new Date($scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].max);
                            $scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].max = d;
                        }
                    }
                    $scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].calendar_opened = [false, false];
                }
            }
        };

        /**
         * Add Questionnaire Page
         *
         * @return {void}
         */
        $scope.addPage = function($event, newLabel) {
            var page = new Object();
            page['page_title'] = newLabel + ($scope.questionnaire.QuestionnairePage.length + 1);
            page['page_sequence'] = $scope.questionnaire.QuestionnairePage.length;
            page['QuestionnaireQuestion'] = new Array();
            $scope.questionnaire.QuestionnairePage.push(page);

            $event.stopPropagation();
        };

        /**
         * Delete Questionnaire Page
         *
         * @return {void}
         */
        $scope.deletePage = function(idx, message) {
            dialogs.confirm(message)
                .result.then(
                function(yes) {
                    $scope.questionnaire.QuestionnairePage.splice(idx, 1);
                    $scope._resetQuestionnairePageSequence();
                });
        };

        /**
         * Move Questionnaire Page
         *
         * @return {void}
         */
        $scope.movePage = function(before_idx_str, after_idx_str) {
            var before_idx = parseInt(before_idx_str);
            var after_idx = parseInt(after_idx_str);
            var before_q = $scope.questionnaire.QuestionnairePage[before_idx];
            if(before_idx < after_idx) {
                for(var i=before_idx+1 ; i<after_idx ; i++) {
                    var tmp_q = $scope.questionnaire.QuestionnairePage[i];
                    $scope.questionnaire.QuestionnairePage.splice(i-1, 1, tmp_q);
                }
                $scope.questionnaire.QuestionnairePage.splice(after_idx-1, 1, before_q);
            }
            else {
                for(var i=before_idx ; i>=after_idx ; i--) {
                    var tmp_q = $scope.questionnaire.QuestionnairePage[i-1];
                    $scope.questionnaire.QuestionnairePage.splice(i, 1, tmp_q);
                }
                $scope.questionnaire.QuestionnairePage.splice(after_idx, 1, before_q);
            }
            $scope._resetQuestionnairePageSequence();
        };
        /**
         * Questionnaire Page Sequence reset
         *
         * @return {void}
         */
        $scope._resetQuestionnairePageSequence = function() {
            for(var i=0; i<$scope.questionnaire.QuestionnairePage.length; i++) {
                $scope.questionnaire.QuestionnairePage[i].page_sequence = i;
            }
        }

        /**
         * Delete Questionnaire Question
         *
         * @return {void}
         */
        $scope.deleteQuestion = function($event, pageIndex, idx, message) {
            dialogs.confirm(message)
                .result.then(
                function(yes) {
                    $scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion.splice(idx, 1);
                    $scope._resetQuestionnaireQuestionSequence(pageIndex);
                });
            $event.stopPropagation();
        };
        /**
         * Add Questionnaire Question
         *
         * @return {void}
         */
        $scope.addQuestion = function($event, pageIndex, newLabel) {
            var question = new Object();
            if(!$scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion) {
                $scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion = new Array();
            }
            var newIndex = $scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion.length;
            question['question_value'] = newLabel + (newIndex + 1);
            question['question_sequence'] = newIndex;
            question['question_type'] = 1;
            question['QuestionnaireChoice'] = new Array();
            $scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion.push(question);

            $event.stopPropagation();
        };
        /**
         * Move Questionnaire Question
         *
         * @return {void}
         */
        $scope.moveQuestion = function($event, pageIndex, before_idx_str, after_idx_str) {
            var before_idx = parseInt(before_idx_str);
            var after_idx = parseInt(after_idx_str);
            var before_q = $scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion[before_idx];
            if(before_idx < after_idx) {
                for(var i=before_idx+1 ; i<=after_idx ; i++) {
                    var tmp_q = $scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion[i];
                    $scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion.splice(i-1, 1, tmp_q);
                }
                $scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion.splice(after_idx, 1, before_q);
            }
            else {
                for(var i=before_idx ; i>=after_idx ; i--) {
                    var tmp_q = $scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion[i-1];
                    $scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion.splice(i, 1, tmp_q);
                }
                $scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion.splice(after_idx, 1, before_q);
            }
            $scope._resetQuestionnaireQuestionSequence(pageIndex);
            $event.stopPropagation();
        };
        /**
         * Questionnaire Page Sequence reset
         *
         * @return {void}
         */
        $scope._resetQuestionnaireQuestionSequence = function(pageIndex) {
            for(var i=0; i<$scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion.length; i++) {
                $scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion[i].question_sequence = i;
            }
        }
        /**
         * Add Questionnaire Choice
         *
         * @return {void}
         */
        $scope.addChoice = function($event, pIdx, qIdx, newLabel, otherType, matrixType) {
            var choice = new Object();
            if (!$scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].QuestionnaireChoice) {
                $scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].QuestionnaireChoice = new Array();
            }
            var newIndex = $scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].QuestionnaireChoice.length;
            if(newLabel.length > 0) {
                choice['choice_label'] = newLabel + (newIndex + 1);
            }
            choice['choice_sequence'] = newIndex;
            choice['other_choice_type'] = otherType;
            choice['matrix_type'] = matrixType;

            // その他選択肢は必ず最後にするためにいったん取りのけておく
            var otherChoice = null;
            for(var i=0 ; i<$scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].QuestionnaireChoice.length ; i++) {
                if($scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].QuestionnaireChoice[i].other_choice_type != variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
                    otherChoice = $scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].QuestionnaireChoice[i];
                    $scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].QuestionnaireChoice.splice(i, 1);
                }
            }
            // 指定された新しい選択肢を追加する
            $scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].QuestionnaireChoice.push(choice);
            // 取りのけておいたその他選択肢を元通り最後に追加する
            if(otherChoice != null) {
                $scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].QuestionnaireChoice.push(otherChoice);
            }

            if ($event != null) {
                $event.stopPropagation();
            }
        };
        /**
         * Add Questionnaire Choice
         *
         * @return {void}
         */
        $scope.changeAnotherChoice = function(pIdx, qIdx, newLabel, otherType, matrixType) {
            if($scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].has_another_choice) {
                $scope.addChoice(null, pIdx, qIdx, newLabel, otherType, matrixType);
            }
            else {
                for(var i=0; i<$scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].QuestionnaireChoice.length; i++) {
                    if($scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].QuestionnaireChoice[i].other_choice_type != variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
                        $scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].QuestionnaireChoice.splice(i, 1);
                    }
                }
            }
            $scope._resetQuestionnaireChoiceSequence(pIdx, qIdx);
        }
        /**
         * Delete Questionnaire Choice
         *
         * @return {void}
         */
        $scope.deleteChoice = function($event, pIdx, qIdx, seq, confirmMessage) {
            dialogs.confirm(confirmMessage)
                .result.then(
                function(yes) {
                    for(var i=0; i<$scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].QuestionnaireChoice.length; i++) {
                        if($scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].QuestionnaireChoice[i].choice_sequence == seq) {
                            $scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].QuestionnaireChoice.splice(i, 1);
                        }
                    }
                    $scope._resetQuestionnaireChoiceSequence(pIdx, qIdx);
                });
            $event.stopPropagation();
        };
        /**
         * Questionnaire Page Sequence reset
         *
         * @return {void}
         */
        $scope._resetQuestionnaireChoiceSequence = function(pageIndex, qIndex) {
            for(var i=0; i<$scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex].QuestionnaireChoice.length; i++) {
                $scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex].QuestionnaireChoice[i].choice_sequence = i;
            }
        }
        /**
         * Questionnaire Date Time Option Set
         *
         * @return {void}
         */
        $scope.changeDateTimeOption = function(pageIndex, qIndex, opt) {
            if($scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex].timeOption && $scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex].dateOption) {
                $scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex].question_type_option = variables.TYPE_OPTION_DATE_TIME;
            }
            else if($scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex].timeOption) {
                $scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex].question_type_option = variables.TYPE_OPTION_TIME;
            }
            else if($scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex].dateOption) {
                $scope.questionnaire.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex].question_type_option = variables.TYPE_OPTION_DATE;
            }
            else {

            }
        }
        /**
         * Questionnaire Date Time Option Calendar open
         *
         * @return {void}
         */
        $scope.openCal = function($event, pIdx, qIdx, opt) {
            $event.preventDefault();
            $event.stopPropagation();
            $scope.questionnaire.QuestionnairePage[pIdx].QuestionnaireQuestion[qIdx].calendar_opened[opt] = true;
        };

    });
