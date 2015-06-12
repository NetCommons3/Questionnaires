/**
 * Created by りか on 2015/02/18.
 */


NetCommonsApp.controller('Questionnaires.edit.question',
    function($scope, NetCommonsBase, NetCommonsWysiwyg,
             NetCommonsUser, $timeout) {

      /**
         * plugin
         *
         * @type {object}
         */
      $scope.plugin =
          NetCommonsBase.initUrl('questionnaires', 'questionnaire_questions');

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
        EXPRESSION_NOT_SHOW: '0',

        OTHER_CHOICE_TYPE_NO_OTHER_FILED: '0',

        TYPE_OPTION_NUMERIC: '1',
        TYPE_OPTION_DATE: '2',
        TYPE_OPTION_TIME: '3',
        TYPE_OPTION_EMAIL: '4',
        TYPE_OPTION_URL: '5',
        TYPE_OPTION_PHONE_NUMBER: '6',
        TYPE_OPTION_DATE_TIME: '7',

        RESULT_DISPLAY_TYPE_BAR_CHART: '0',

        TYPE_SELECTION: '1',
        TYPE_MULTIPLE_SELECTION: '2',
        TYPE_TEXT: '3',
        TYPE_TEXT_AREA: '4',
        TYPE_MATRIX_SELECTION_LIST: '5',
        TYPE_MATRIX_MULTIPLE: '6',
        TYPE_DATE_AND_TIME: '7',
        TYPE_SINGLE_SELECT_BOX: '8',

        MATRIX_TYPE_ROW_OR_NO_MATRIX: '0',

        SKIP_GO_TO_END: '99999'
      };

      $scope.colorPickerPalette =
          ['#f38631', '#e0e4cd', '#69d2e7', '#68e2a7', '#f64649',
           '#4d5361', '#47bfbd', '#7c4f6c', '#23313c', '#9c9b7f',
           '#be5945', '#cccccc'];

      /**
         * Initialize
         *
         * @return {void}
         */
      $scope.initialize =
          function(frameId, isPublished, questionnaire, errors,
          newPageLabel, newQuestionLabel, newChoiceLabel,
          newChoiceColumnLabel, newChoiceOtherLabel) {
        $scope.frameId = frameId;
        $scope.isPublished = isPublished;
        $scope.questionnaire = questionnaire;
        $scope.errors = errors;

        for (var pIdx = 0; pIdx <
             $scope.questionnaire.QuestionnairePage.length;
             pIdx++) {

          $scope.questionnaire.QuestionnairePage[pIdx].tab_active = false;

          if (
              !$scope.questionnaire.QuestionnairePage[pIdx].
                  QuestionnaireQuestion
          ) {
            continue;
          }
          for (var qIdx = 0;
              qIdx <
              $scope.questionnaire.QuestionnairePage[pIdx].
              QuestionnaireQuestion.length;
              qIdx++) {
            // 各質問が日付・時刻のタイプならば、範囲設定があるかの確認
            $scope.questionnaire.QuestionnairePage[pIdx].
                QuestionnaireQuestion[qIdx].setDateTimeRange = false;
            if ($scope.questionnaire.QuestionnairePage[pIdx].
                QuestionnaireQuestion[qIdx].question_type_option ==
                    variables.TYPE_OPTION_DATE ||
                $scope.questionnaire.QuestionnairePage[pIdx].
                    QuestionnaireQuestion[qIdx].
                question_type_option == variables.TYPE_OPTION_TIME ||
                $scope.questionnaire.QuestionnairePage[pIdx].
                    QuestionnaireQuestion[qIdx].question_type_option ==
                variables.TYPE_OPTION_DATE_TIME) {
              if ($scope.questionnaire.QuestionnairePage[pIdx].
                  QuestionnaireQuestion[qIdx].min.length > 0) {
                $scope.questionnaire.QuestionnairePage[pIdx].
                    QuestionnaireQuestion[qIdx].setDateTimeRange = true;
                var d = new Date(
                    $scope.questionnaire.QuestionnairePage[pIdx].
                    QuestionnaireQuestion[qIdx].min);
                $scope.questionnaire.QuestionnairePage[pIdx].
                    QuestionnaireQuestion[qIdx].min = d;
              }
              if ($scope.questionnaire.QuestionnairePage[pIdx].
                  QuestionnaireQuestion[qIdx].max.length > 0) {
                $scope.questionnaire.QuestionnairePage[pIdx].
                    QuestionnaireQuestion[qIdx].setDateTimeRange = true;
                var d = new Date($scope.questionnaire.QuestionnairePage[pIdx].
                    QuestionnaireQuestion[qIdx].max);
                $scope.questionnaire.QuestionnairePage[pIdx].
                    QuestionnaireQuestion[qIdx].max = d;
              }
            }
            // テキスト、１行テキスト、日付け型は集計結果を出さない設定
            if ($scope.questionnaire.QuestionnairePage[pIdx].
                QuestionnaireQuestion[qIdx].question_type ==
                variables.TYPE_TEXT ||
                $scope.questionnaire.QuestionnairePage[pIdx].
                QuestionnaireQuestion[qIdx].question_type ==
                variables.TYPE_TEXT_AREA ||
                $scope.questionnaire.QuestionnairePage[pIdx].
                QuestionnaireQuestion[qIdx].question_type ==
                variables.TYPE_DATE_AND_TIME) {
              $scope.questionnaire.QuestionnairePage[pIdx].
                  QuestionnaireQuestion[qIdx].is_result_display =
                  variables.EXPRESSION_NOT_SHOW;
            }
            // 日付け型の設定画面でカレンダーの開閉オプション
            $scope.questionnaire.QuestionnairePage[pIdx].
                QuestionnaireQuestion[qIdx].calendar_opened = [false, false];

            // 選択肢がないのならここでcontinue;
            if (!$scope.questionnaire.QuestionnairePage[pIdx].
                QuestionnaireQuestion[qIdx].QuestionnaireChoice) {
              continue;
            }
            // 各質問の選択肢があればその選択肢の中に「その他」が入っているかの確認とフラグ設定
            for (var cIdx = 0; cIdx <
                $scope.questionnaire.QuestionnairePage[pIdx].
                QuestionnaireQuestion[qIdx].QuestionnaireChoice.length;
                cIdx++) {
              if ($scope.questionnaire.QuestionnairePage[pIdx].
                  QuestionnaireQuestion[qIdx].QuestionnaireChoice[cIdx].
                  other_choice_type !=
                  variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
                $scope.questionnaire.QuestionnairePage[pIdx].
                    QuestionnaireQuestion[qIdx].has_another_choice = true;
                break;
              }
            }

          }
        }
        $scope.questionnaire.QuestionnairePage[0].tab_active = true;

        $scope.newPageLabel = newPageLabel;
        $scope.newQuestionLabel = newQuestionLabel;
        $scope.newChoiceLabel = newChoiceLabel;
        $scope.newChoiceColumnLabel = newChoiceColumnLabel;
        $scope.newChoiceOtherLabel = newChoiceOtherLabel;
      };
      /**
         * Questionnaire EnterSubmit gard
         *
         * @return {void}
         */
      $scope.handleKeydown = function(e) {
        if (e.which === 13) {
          e.stopPropagation();
          return false;
        }
      };

      /**
         * Add Questionnaire Page
         *
         * @return {void}
         */
      $scope.addPage = function($event) {
        var page = new Object();
        page['page_title'] =
            $scope.newPageLabel +
            ($scope.questionnaire.QuestionnairePage.length + 1);
        page['page_sequence'] =
            $scope.questionnaire.QuestionnairePage.length;
        page['origin_id'] = 0;
        page['QuestionnaireQuestion'] = new Array();
        $scope.questionnaire.QuestionnairePage.push(page);

        $scope.addQuestion($event,
            $scope.questionnaire.QuestionnairePage.length - 1);

        $scope.questionnaire.QuestionnairePage[$scope.questionnaire.
            QuestionnairePage.length - 1].tab_active = true;
        if ($event) {
          $event.stopPropagation();
        }
      };

      /**
         * Delete Questionnaire Page
         *
         * @return {void}
         */
      $scope.deletePage = function(idx, message) {
        if ($scope.questionnaire.QuestionnairePage.length < 2) {
          // 残り１ページは削除させない
          return;
        }
        if (confirm(message)) {
          $scope.questionnaire.QuestionnairePage.splice(idx, 1);
          $scope._resetQuestionnairePageSequence();
        }
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
        if (before_idx < after_idx) {
          for (var i = before_idx + 1; i < after_idx; i++) {
            var tmp_q = $scope.questionnaire.QuestionnairePage[i];
            $scope.questionnaire.QuestionnairePage.
                splice(i - 1, 1, tmp_q);
          }
          $scope.questionnaire.QuestionnairePage.splice(after_idx - 1, 1,
              before_q);
        }
        else {
          for (var i = before_idx; i >= after_idx; i--) {
            var tmp_q = $scope.questionnaire.QuestionnairePage[i - 1];
            $scope.questionnaire.QuestionnairePage.splice(i, 1, tmp_q);
          }
          $scope.questionnaire.QuestionnairePage.splice(after_idx,
              1, before_q);
        }
        $scope._resetQuestionnairePageSequence();
      };

      /**
         * Questionnaire Page Sequence reset
         *
         * @return {void}
         */
      $scope._resetQuestionnairePageSequence = function() {
        for (var i = 0;
            i < $scope.questionnaire.QuestionnairePage.length; i++) {
          $scope.questionnaire.QuestionnairePage[i].page_sequence = i;
        }
      };

      /**
         * Delete Questionnaire Question
         *
         * @return {void}
         */
      $scope.deleteQuestion = function($event, pageIndex, idx, message) {
        if ($scope.questionnaire.QuestionnairePage[pageIndex].
            QuestionnaireQuestion.length < 2) {
          return;
        }
        if (confirm(message)) {
          $scope.questionnaire.QuestionnairePage[pageIndex].
              QuestionnaireQuestion.splice(idx, 1);
          $scope._resetQuestionnaireQuestionSequence(pageIndex);
        }
        $event.stopPropagation();
      };

      /**
         * Add Questionnaire Question
         *
         * @return {void}
         */
      $scope.addQuestion = function($event, pageIndex) {
        var question = new Object();
        if (!$scope.questionnaire.QuestionnairePage[pageIndex].
            QuestionnaireQuestion) {
          $scope.questionnaire.QuestionnairePage[pageIndex].
              QuestionnaireQuestion = new Array();
        }
        var newIndex =
            $scope.questionnaire.QuestionnairePage[pageIndex].
                QuestionnaireQuestion.length;
        question['question_value'] = $scope.newQuestionLabel + (newIndex + 1);
        question['question_sequence'] = newIndex;
        question['question_type'] = variables.TYPE_SELECTION;
        question['origin_id'] = 0;
        question['is_result_display'] = 1;
        question['result_display_type'] =
            variables.RESULT_DISPLAY_TYPE_BAR_CHART;
        question['QuestionnaireChoice'] = new Array();
        question['isOpen'] = true;
        $scope.questionnaire.QuestionnairePage[pageIndex].
            QuestionnaireQuestion.push(question);

        $scope.addChoice($event,
            pageIndex,
            $scope.questionnaire.QuestionnairePage[pageIndex].
                QuestionnaireQuestion.length - 1,
            0,
            variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED,
            variables.MATRIX_TYPE_ROW_OR_NO_MATRIX);

        if ($event) {
          $event.stopPropagation();
        }
      };

      /**
         * Move Questionnaire Question
         *
         * @return {void}
         */
      $scope.moveQuestion =
          function($event, pageIndex, before_idx_str, after_idx_str) {
        var before_idx = parseInt(before_idx_str);
        var after_idx = parseInt(after_idx_str);
        var before_q =
            $scope.questionnaire.QuestionnairePage[pageIndex].
                QuestionnaireQuestion[before_idx];
        if (before_idx < after_idx) {
          for (var i = before_idx + 1; i <= after_idx; i++) {
            var tmp_q =
                $scope.questionnaire.QuestionnairePage[pageIndex].
                    QuestionnaireQuestion[i];
            $scope.questionnaire.QuestionnairePage[pageIndex].
                QuestionnaireQuestion.splice(i - 1, 1, tmp_q);
          }
          $scope.questionnaire.QuestionnairePage[pageIndex].
              QuestionnaireQuestion.splice(after_idx, 1, before_q);
        }
        else {
          for (var i = before_idx; i >= after_idx; i--) {
            var tmp_q =
                $scope.questionnaire.QuestionnairePage[pageIndex].
                    QuestionnaireQuestion[i - 1];
            $scope.questionnaire.QuestionnairePage[pageIndex].
                QuestionnaireQuestion.splice(i, 1, tmp_q);
          }
          $scope.questionnaire.QuestionnairePage[pageIndex].
              QuestionnaireQuestion.splice(after_idx, 1, before_q);
        }
        $scope._resetQuestionnaireQuestionSequence(pageIndex);
        $event.stopPropagation();
      };
      /**
         * Move to another page Questionnaire Question
         *
         * @return {void}
         */
      $scope.moveQuestionToAnotherPage =
          function($event, pageIndex, qIndex, movePageIndex) {
        var tmp_q =
            $scope.questionnaire.QuestionnairePage[pageIndex].
                QuestionnaireQuestion.splice(qIndex, 1);
        $scope.questionnaire.QuestionnairePage[movePageIndex].
            QuestionnaireQuestion.push(tmp_q[0]);

        $scope._resetQuestionnaireQuestionSequence(pageIndex);
        //$event.stopPropagation();
      };
      /**
         * Questionnaire Page Sequence reset
         *
         * @return {void}
         */
      $scope._resetQuestionnaireQuestionSequence = function(pageIndex) {
        for (var i = 0; i < $scope.questionnaire.QuestionnairePage[pageIndex].
            QuestionnaireQuestion.length; i++) {
          $scope.questionnaire.QuestionnairePage[pageIndex].
              QuestionnaireQuestion[i].question_sequence = i;
        }
      };
      /**
         * Add Questionnaire Choice
         *
         * @return {void}
         */
      $scope.addChoice =
          function($event, pIdx, qIdx, choiceCount, otherType, matrixType) {
        var choice = new Object();
        if (!$scope.questionnaire.QuestionnairePage[pIdx].
            QuestionnaireQuestion[qIdx].QuestionnaireChoice) {
          $scope.questionnaire.QuestionnairePage[pIdx].
              QuestionnaireQuestion[qIdx].QuestionnaireChoice = new Array();
        }
        var newIndex = $scope.questionnaire.QuestionnairePage[pIdx].
            QuestionnaireQuestion[qIdx].QuestionnaireChoice.length;

        if (otherType != variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
          choice['choice_label'] = $scope.newChoiceOtherLabel;
        } else {
          if (matrixType == variables.MATRIX_TYPE_ROW_OR_NO_MATRIX) {
            choice['choice_label'] =
                $scope.newChoiceLabel + (choiceCount + 1);
          } else {
            choice['choice_label'] =
                $scope.newChoiceColumnLabel + (choiceCount + 1);
          }
        }

        // その他選択肢は必ず最後にするためにいったん取りのけておく
        var otherChoice = null;
        for (var i = 0;
            i < $scope.questionnaire.QuestionnairePage[pIdx].
                QuestionnaireQuestion[qIdx].QuestionnaireChoice.length; i++) {
          if ($scope.questionnaire.QuestionnairePage[pIdx].
              QuestionnaireQuestion[qIdx].QuestionnaireChoice[i].
              other_choice_type != variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
            otherChoice = $scope.questionnaire.QuestionnairePage[pIdx].
                QuestionnaireQuestion[qIdx].QuestionnaireChoice[i];
            $scope.questionnaire.QuestionnairePage[pIdx].
                QuestionnaireQuestion[qIdx].QuestionnaireChoice.splice(i, 1);
          }
        }

        if (otherChoice) {
          choice['choice_sequence'] = newIndex - 1;
          otherChoice['choice_sequence'] = newIndex;
        } else {
          choice['choice_sequence'] = newIndex;
        }

        choice['other_choice_type'] = otherType;
        choice['matrix_type'] = matrixType;
        choice['origin_id'] = 0;
        if (otherType != variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
          choice['graph_color'] =
              $scope.colorPickerPalette[choice['choice_sequence'] % 12];
        } else {
          choice['graph_color'] =
              $scope.colorPickerPalette[choiceCount % 12];
        }

        // 指定された新しい選択肢を追加する
        $scope.questionnaire.QuestionnairePage[pIdx].
            QuestionnaireQuestion[qIdx].QuestionnaireChoice.push(choice);
        // 取りのけておいたその他選択肢を元通り最後に追加する
        if (otherChoice != null) {
          $scope.questionnaire.QuestionnairePage[pIdx].
              QuestionnaireQuestion[qIdx].QuestionnaireChoice.push(otherChoice);
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
      $scope.changeAnotherChoice =
          function(pIdx, qIdx, otherType, matrixType) {
        if ($scope.questionnaire.QuestionnairePage[pIdx].
            QuestionnaireQuestion[qIdx].has_another_choice) {
          $scope.addChoice(null, pIdx, qIdx, 0, otherType, matrixType);
        }
        else {
          for (var i = 0;
              i < $scope.questionnaire.QuestionnairePage[pIdx].
                  QuestionnaireQuestion[qIdx].QuestionnaireChoice.length; i++) {
            if ($scope.questionnaire.QuestionnairePage[pIdx].
                QuestionnaireQuestion[qIdx].QuestionnaireChoice[i].
                    other_choice_type !=
                        variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
              $scope.questionnaire.QuestionnairePage[pIdx].
                  QuestionnaireQuestion[qIdx].QuestionnaireChoice.splice(i, 1);
            }
          }
        }
        $scope._resetQuestionnaireChoiceSequence(pIdx, qIdx);
      };
      /**
         * Change skip page about Choice
         *
         * @return {void}
         */
      $scope.changeSkipPageChoice = function(skipPageIndex) {
        skipPageIndex = parseInt(skipPageIndex);
        // 選択中のoptionを調べる
        if (skipPageIndex == variables.SKIP_GO_TO_END) {
          return;
        }
        if ($scope.questionnaire.QuestionnairePage.length - 1 >=
            skipPageIndex) {
          return;
        }
        // ないページを指定された場合は新しく作る
        $scope.addPage(null);
      };
      /**
         * Delete Questionnaire Choice
         *
         * @return {void}
         */
      $scope.deleteChoice = function($event, pIdx, qIdx, seq) {
        if ($scope.questionnaire.QuestionnairePage[pIdx].
            QuestionnaireQuestion[qIdx].QuestionnaireChoice.length < 2) {
          return;
        }
        for (var i = 0;
            i < $scope.questionnaire.QuestionnairePage[pIdx].
                QuestionnaireQuestion[qIdx].QuestionnaireChoice.length; i++) {
          if ($scope.questionnaire.QuestionnairePage[pIdx].
              QuestionnaireQuestion[qIdx].QuestionnaireChoice[i].
                  choice_sequence == seq) {
            $scope.questionnaire.QuestionnairePage[pIdx].
                QuestionnaireQuestion[qIdx].QuestionnaireChoice.splice(i, 1);
          }
        }
        $scope._resetQuestionnaireChoiceSequence(pIdx, qIdx);

        if ($event) {
          $event.stopPropagation();
        }
      };
      /**
         * Questionnaire Page Sequence reset
         *
         * @return {void}
         */
      $scope._resetQuestionnaireChoiceSequence = function(pageIndex, qIndex) {
        for (var i = 0; i <
            $scope.questionnaire.QuestionnairePage[pageIndex].
            QuestionnaireQuestion[qIndex].QuestionnaireChoice.length; i++) {
          $scope.questionnaire.QuestionnairePage[pageIndex].
              QuestionnaireQuestion[qIndex].QuestionnaireChoice[i].
              choice_sequence = i;
        }
      };
      /**
         * Questionnaire Date Time Option Set
         *
         * @return {void}
         */
      $scope.changeDateTimeOption = function(pageIndex, qIndex, opt) {
        if ($scope.questionnaire.QuestionnairePage[pageIndex].
            QuestionnaireQuestion[qIndex].timeOption &&
            $scope.questionnaire.QuestionnairePage[pageIndex].
            QuestionnaireQuestion[qIndex].dateOption) {
          $scope.questionnaire.QuestionnairePage[pageIndex].
              QuestionnaireQuestion[qIndex].question_type_option =
                  variables.TYPE_OPTION_DATE_TIME;
        }
        else if ($scope.questionnaire.QuestionnairePage[pageIndex].
            QuestionnaireQuestion[qIndex].timeOption) {
          $scope.questionnaire.QuestionnairePage[pageIndex].
              QuestionnaireQuestion[qIndex].question_type_option =
              variables.TYPE_OPTION_TIME;
        }
        else if ($scope.questionnaire.QuestionnairePage[pageIndex].
            QuestionnaireQuestion[qIndex].dateOption) {
          $scope.questionnaire.QuestionnairePage[pageIndex].
              QuestionnaireQuestion[qIndex].question_type_option =
              variables.TYPE_OPTION_DATE;
        }
        else {

        }
      };
      /**
         * Questionnaire Date Time Option Calendar open
         *
         * @return {void}
         */
      $scope.openCal = function($event, pIdx, qIdx, opt) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.questionnaire.QuestionnairePage[pIdx].
            QuestionnaireQuestion[qIdx].calendar_opened[opt] = true;
      };

    });
