/**
 * Created by りか on 2015/02/18.
 */


NetCommonsApp.controller('Questionnaires.edit.question',
    function($scope, NetCommonsBase, NetCommonsWysiwyg,
             $timeout) {

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
       * isDateTimeType
       *
       * @return {bool}
       */
      $scope.isDateTimeType = function(typeOpt) {
        if (typeOpt == variables.TYPE_OPTION_DATE ||
            typeOpt == variables.TYPE_OPTION_TIME ||
            typeOpt == variables.TYPE_OPTION_DATE_TIME) {
          return true;
        } else {
          return false;
        }
      };


      /**
       * get Date Object
       *
       * @return {Date}
       */
      $scope.Date = function(dateStr) {
        if (Date.parse(dateStr)) {
          return new Date(dateStr);
        } else {
          return null;
        }
      };

      /**
         * Initialize
         *
         * @return {void}
         */
      $scope.initialize =
          function(frameId, isPublished, questionnaire,
          newPageLabel, newQuestionLabel, newChoiceLabel,
          newChoiceColumnLabel, newChoiceOtherLabel) {
        $scope.frameId = frameId;
        $scope.isPublished = isPublished;
        $scope.questionnaire = questionnaire;

        // 各ページ処理
        for (var pIdx = 0; pIdx <
             $scope.questionnaire.questionnairePage.length;
             pIdx++) {
          var page = $scope.questionnaire.questionnairePage[pIdx];

          $scope.questionnaire.questionnairePage[pIdx].tabActive = false;

          if (!page.questionnaireQuestion) {
            continue;
          }

          // 各質問処理
          for (var qIdx = 0;
              qIdx < page.questionnaireQuestion.length;
              qIdx++) {
            var question = $scope.questionnaire.questionnairePage[pIdx].
                questionnaireQuestion[qIdx];

            // 各質問が日付・時刻のタイプならば、範囲設定があるかの確認
            $scope.questionnaire.questionnairePage[pIdx].
                questionnaireQuestion[qIdx].setDateTimeRange = false;

            if ($scope.isDateTimeType(question.questionTypeOption)) {
              $scope.questionnaire.questionnairePage[pIdx].
                  questionnaireQuestion[qIdx].min = $scope.Date(question.min);
              $scope.questionnaire.questionnairePage[pIdx].
                  questionnaireQuestion[qIdx].max = $scope.Date(question.max);
              if ($scope.questionnaire.questionnairePage[pIdx].
                  questionnaireQuestion[qIdx].min != null ||
                  $scope.questionnaire.questionnairePage[pIdx].
                  questionnaireQuestion[qIdx].max != null) {
                $scope.questionnaire.questionnairePage[pIdx].
                    questionnaireQuestion[qIdx].setDateTimeRange = true;
              }
            }
            // テキスト、１行テキスト、日付け型は集計結果を出さない設定
            if (question.questionType == variables.TYPE_TEXT ||
                question.questionType == variables.TYPE_TEXT_AREA ||
                question.questionType == variables.TYPE_DATE_AND_TIME) {
              $scope.questionnaire.questionnairePage[pIdx].
                  questionnaireQuestion[qIdx].isResultDisplay =
                  variables.EXPRESSION_NOT_SHOW;
            }
            // 日付け型の設定画面でカレンダーの開閉オプション
            $scope.questionnaire.questionnairePage[pIdx].
                questionnaireQuestion[qIdx].calendarOpened = [false, false];

            // この質問の中にエラーがあるか
            if (question.errorMessages) {
              $scope.questionnaire.questionnairePage[pIdx].
                  questionnaireQuestion[qIdx].hasError = true;
            }

            // 選択肢がないのならここでcontinue;
            if (!question.questionnaireChoice) {
              continue;
            }
            // 各質問の選択肢があればその選択肢の中に「その他」が入っているかの確認とフラグ設定
            // また質問の選択肢の中にエラーがあるかのフラグ設定
            for (var cIdx = 0;
                cIdx < question.questionnaireChoice.length;
                cIdx++) {
              var choice = question.questionnaireChoice[cIdx];
              if (choice.otherChoiceType !=
                  variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
                $scope.questionnaire.questionnairePage[pIdx].
                    questionnaireQuestion[qIdx].hasAnotherChoice = true;
              }
              if (choice.errorMessages) {
                $scope.questionnaire.questionnairePage[pIdx].
                    questionnaireQuestion[qIdx].hasError = true;
              }
            }

          }
        }
        $scope.questionnaire.questionnairePage[0].tabActive = true;

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
        page['pageTitle'] =
            $scope.newPageLabel +
            ($scope.questionnaire.questionnairePage.length + 1);
        page['pageSequence'] =
            $scope.questionnaire.questionnairePage.length;
        page['originId'] = 0;
        page['questionnaireQuestion'] = new Array();
        $scope.questionnaire.questionnairePage.push(page);

        $scope.addQuestion($event,
            $scope.questionnaire.questionnairePage.length - 1);

        $scope.questionnaire.questionnairePage[$scope.questionnaire.
            questionnairePage.length - 1].tabActive = true;
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
        if ($scope.questionnaire.questionnairePage.length < 2) {
          // 残り１ページは削除させない
          return;
        }
        if (confirm(message)) {
          $scope.questionnaire.questionnairePage.splice(idx, 1);
          $scope._resetQuestionnairePageSequence();
        }
      };

      /**
         * Questionnaire Page Sequence reset
         *
         * @return {void}
         */
      $scope._resetQuestionnairePageSequence = function() {
        for (var i = 0;
            i < $scope.questionnaire.questionnairePage.length; i++) {
          $scope.questionnaire.questionnairePage[i].pageSequence = i;
        }
      };

      /**
         * Add Questionnaire Question
         *
         * @return {void}
         */
      $scope.addQuestion = function($event, pageIndex) {
        var question = new Object();
        if (!$scope.questionnaire.questionnairePage[pageIndex].
            questionnaireQuestion) {
          $scope.questionnaire.questionnairePage[pageIndex].
              questionnaireQuestion = new Array();
        }
        var newIndex =
            $scope.questionnaire.questionnairePage[pageIndex].
                questionnaireQuestion.length;
        question['questionValue'] = $scope.newQuestionLabel + (newIndex + 1);
        question['questionSequence'] = newIndex;
        question['questionType'] = variables.TYPE_SELECTION;
        question['originId'] = 0;
        question['isResultDisplay'] = 1;
        question['resultDisplayType'] =
            variables.RESULT_DISPLAY_TYPE_BAR_CHART;
        question['questionnaireChoice'] = new Array();
        question['isOpen'] = true;
        $scope.questionnaire.questionnairePage[pageIndex].
            questionnaireQuestion.push(question);

        $scope.addChoice($event,
            pageIndex,
            $scope.questionnaire.questionnairePage[pageIndex].
                questionnaireQuestion.length - 1,
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
          function($event, pageIndex, beforeIdxStr, afterIdxStr) {
        var beforeIdx = parseInt(beforeIdxStr);
        var afterIdx = parseInt(afterIdxStr);
        var beforeQ =
            $scope.questionnaire.questionnairePage[pageIndex].
                questionnaireQuestion[beforeIdx];
        if (beforeIdx < afterIdx) {
          for (var i = beforeIdx + 1; i <= afterIdx; i++) {
            var tmpQ =
                $scope.questionnaire.questionnairePage[pageIndex].
                    questionnaireQuestion[i];
            $scope.questionnaire.questionnairePage[pageIndex].
                questionnaireQuestion.splice(i - 1, 1, tmpQ);
          }
          $scope.questionnaire.questionnairePage[pageIndex].
              questionnaireQuestion.splice(afterIdx, 1, beforeQ);
        }
        else {
          for (var i = beforeIdx; i >= afterIdx; i--) {
            var tmpQ =
                $scope.questionnaire.questionnairePage[pageIndex].
                    questionnaireQuestion[i - 1];
            $scope.questionnaire.questionnairePage[pageIndex].
                questionnaireQuestion.splice(i, 1, tmpQ);
          }
          $scope.questionnaire.questionnairePage[pageIndex].
              questionnaireQuestion.splice(afterIdx, 1, beforeQ);
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
        var tmpQ =
            $scope.questionnaire.questionnairePage[pageIndex].
                questionnaireQuestion.splice(qIndex, 1);
        $scope.questionnaire.questionnairePage[movePageIndex].
            questionnaireQuestion.push(tmpQ[0]);

        $scope._resetQuestionnaireQuestionSequence(pageIndex);
        //$event.stopPropagation();
      };

      /**
       * Delete Questionnaire Question
       *
       * @return {void}
       */
      $scope.deleteQuestion = function($event, pageIndex, idx, message) {
        if ($scope.questionnaire.questionnairePage[pageIndex].
            questionnaireQuestion.length < 2) {
          return;
        }
        if (confirm(message)) {
          $scope.questionnaire.questionnairePage[pageIndex].
              questionnaireQuestion.splice(idx, 1);
          $scope._resetQuestionnaireQuestionSequence(pageIndex);
        }
        $event.stopPropagation();
      };

      /**
         * Questionnaire Question Sequence reset
         *
         * @return {void}
         */
      $scope._resetQuestionnaireQuestionSequence = function(pageIndex) {
        for (var i = 0; i < $scope.questionnaire.questionnairePage[pageIndex].
            questionnaireQuestion.length; i++) {
          $scope.questionnaire.questionnairePage[pageIndex].
              questionnaireQuestion[i].questionSequence = i;
        }
      };

      /**
         * Add Questionnaire Choice
         *
         * @return {void}
         */
      $scope.addChoice =
          function($event, pIdx, qIdx, choiceCount, otherType, matrixType) {
        var page = $scope.questionnaire.
            questionnairePage[pIdx];
        var question = $scope.questionnaire.
            questionnairePage[pIdx].questionnaireQuestion[qIdx];
        var choice = new Object();
        var choiceColorIdx = choiceCount % $scope.colorPickerPalette.length;

        if (!question.questionnaireChoice) {
          $scope.questionnaire.questionnairePage[pIdx].
              questionnaireQuestion[qIdx].questionnaireChoice = new Array();
        }
        var newIndex = question.questionnaireChoice.length;

        if (otherType != variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
          choice['choiceLabel'] = $scope.newChoiceOtherLabel;
        } else {
          if (matrixType == variables.MATRIX_TYPE_ROW_OR_NO_MATRIX) {
            choice['choiceLabel'] =
                $scope.newChoiceLabel + (choiceCount + 1);
          } else {
            choice['choiceLabel'] =
                $scope.newChoiceColumnLabel + (choiceCount + 1);
          }
        }
        // skipPageIndex仮設定
        if (pIdx == $scope.questionnaire.questionnairePage.length - 1) {
          choice['skipPageSequence'] = variables.SKIP_GO_TO_END;
        } else {
          choice['skipPageSequence'] = parseInt(page['pageSequence']) + 1;
        }

        // その他選択肢は必ず最後にするためにいったん取りのけておく
        var otherChoice = null;
        for (var i = 0;
            i < question.questionnaireChoice.length; i++) {
          if (question.questionnaireChoice[i].otherChoiceType !=
              variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
            otherChoice = question.questionnaireChoice[i];
            $scope.questionnaire.questionnairePage[pIdx].
                questionnaireQuestion[qIdx].questionnaireChoice.splice(i, 1);
          }
        }

        if (otherChoice) {
          choice['choiceSequence'] = newIndex - 1;
          otherChoice['choiceSequence'] = newIndex;
        } else {
          choice['choiceSequence'] = newIndex;
        }

        choice['otherChoiceType'] = otherType;
        choice['matrixType'] = matrixType;
        choice['originId'] = 0;
        if (otherType != variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
          choiceColorIdx =
              choice['choiceSequence'] % $scope.colorPickerPalette.length;
        }
        choice['graphColor'] = $scope.colorPickerPalette[choiceColorIdx];

        // 指定された新しい選択肢を追加する
        $scope.questionnaire.questionnairePage[pIdx].
            questionnaireQuestion[qIdx].questionnaireChoice.push(choice);
        // 取りのけておいたその他選択肢を元通り最後に追加する
        if (otherChoice != null) {
          $scope.questionnaire.questionnairePage[pIdx].
              questionnaireQuestion[qIdx].questionnaireChoice.push(otherChoice);
        }

        if ($event != null) {
          $event.stopPropagation();
        }
      };
      /**
         * Change Another Choice
         *
         * @return {void}
         */
      $scope.changeAnotherChoice =
          function(pIdx, qIdx, otherType, matrixType) {

        var question = $scope.questionnaire.
            questionnairePage[pIdx].questionnaireQuestion[qIdx];

        //その他を持つように指示されている
        if (question.hasAnotherChoice) {
          $scope.addChoice(null, pIdx, qIdx, 0, otherType, matrixType);
        } else {
          // その他選択肢をなくすように指示されている
          for (var i = 0;
              i < question.questionnaireChoice.length; i++) {
            if (question.questionnaireChoice[i].otherChoiceType !=
                variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
              $scope.questionnaire.questionnairePage[pIdx].
                  questionnaireQuestion[qIdx].questionnaireChoice.splice(i, 1);
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
        if ($scope.questionnaire.questionnairePage.length - 1 >=
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

        var question = $scope.questionnaire.
            questionnairePage[pIdx].questionnaireQuestion[qIdx];

        if (question.questionnaireChoice.length < 2) {
          return;
        }
        for (var i = 0;
            i < question.questionnaireChoice.length; i++) {
          if (question.questionnaireChoice[i].choiceSequence == seq) {
            $scope.questionnaire.questionnairePage[pIdx].
                questionnaireQuestion[qIdx].questionnaireChoice.splice(i, 1);
          }
        }
        $scope._resetQuestionnaireChoiceSequence(pIdx, qIdx);

        if ($event) {
          $event.stopPropagation();
        }
      };
      /**
         * Questionnaire Choice Sequence reset
         *
         * @return {void}
         */
      $scope._resetQuestionnaireChoiceSequence = function(pageIndex, qIndex) {
        for (var i = 0; i <
            $scope.questionnaire.questionnairePage[pageIndex].
            questionnaireQuestion[qIndex].questionnaireChoice.length; i++) {
          $scope.questionnaire.questionnairePage[pageIndex].
              questionnaireQuestion[qIndex].questionnaireChoice[i].
              choiceSequence = i;
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
        $scope.questionnaire.questionnairePage[pIdx].
            questionnaireQuestion[qIdx].calendarOpened[opt] = true;
      };
      /**
       * Questionnaire Judgment sentence greater than
       *
       * @return {bool}
       */
      $scope.greaterThan = function(prop, tgt2) {
        return function(item) {
          return item[prop] > tgt2[prop];
        }
      };

    });
