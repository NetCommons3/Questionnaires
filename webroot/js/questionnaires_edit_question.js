/**
 * Created by りか on 2015/02/18.
 */

NetCommonsApp.constant('moment', moment);
NetCommonsApp.controller('Questionnaires.edit.question',
    function($scope, NetCommonsBase, NetCommonsWysiwyg,
             $timeout, moment) {

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

      $scope.isTrue = '1';

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
        USES_USE: '1',

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

          // 質問アコーディオンクローズ
          //$scope.questionnaire.questionnairePage[pIdx].isOpen = false;

          // このページの中にエラーがあるか
          $scope.questionnaire.questionnairePage[pIdx].hasError = false;
          if (page.errorMessages) {
            $scope.questionnaire.questionnairePage[pIdx].hasError = true;
          }

          if (!page.questionnaireQuestion) {
            continue;
          }

          // 各質問処理
          for (var qIdx = 0;
              qIdx < page.questionnaireQuestion.length;
              qIdx++) {
            var question = $scope.questionnaire.questionnairePage[pIdx].
                questionnaireQuestion[qIdx];

            // テキスト、１行テキスト、日付け型は集計結果を出さない設定
            if (question.questionType == variables.TYPE_TEXT ||
                question.questionType == variables.TYPE_TEXT_AREA ||
                question.questionType == variables.TYPE_DATE_AND_TIME) {
              $scope.questionnaire.questionnairePage[pIdx].
                  questionnaireQuestion[qIdx].isResultDisplay =
                  variables.EXPRESSION_NOT_SHOW;
            }
            // この質問の中にエラーがあるか
            if (question.errorMessages) {
              $scope.questionnaire.questionnairePage[pIdx].
                  questionnaireQuestion[qIdx].hasError = true;
              $scope.questionnaire.questionnairePage[pIdx].
                  hasError = true;
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
                $scope.questionnaire.questionnairePage[pIdx].
                    hasError = true;
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
       * get Date String
       *
       * @return {String}
       */
      $scope.getDateStr = function(dateStr, format) {
        // もしも時刻表示の場合は本日の日付文字列を付加して日時文字列扱いにする
        var regTime = /^\d{2}:\d{2}:\d{2}$/;
        var regTime2 = /^\d{2}:\d{2}$/;
        if (dateStr.match(regTime) || dateStr.match(regTime2)) {
          var today = new Date();
          dateStr = today.getFullYear() +
              '-' + (today.getMonth() + 1) +
              '-' + today.getDate() +
              ' ' + dateStr;
        }
        // もしも年月日表示の場合は00：00を付加して日時文字列扱いにする
        var regTime3 = /^\d{2}-\d{2}-\d{2}$/;
        if (dateStr.match(regTime3)) {
          dateStr += '00:00';
        }

        if (format) {
          var d = new moment(dateStr);
          dateStr = d.format(format);
        }

        return dateStr;
      };

      /**
       * get Date Object
       *
       * @return {Date}
       */
      $scope.Date = function(dateStr) {
        dateStr = $scope.getDateStr(dateStr);
        if (Date.parse(dateStr)) {
          return new Date(dateStr);
        } else {
          return null;
        }
      };

      /**
       * change DateTimePickerType
       *
       * @return {void}
       */
      $scope.changeDatetimepickerType = function(pIdx, qIdx) {
        var type = $scope.questionnaire.questionnairePage[pIdx].
            questionnaireQuestion[qIdx].questionTypeOption;
        var format;
        if (type == variables.TYPE_OPTION_DATE) {
          format = 'YYYY-MM-DD';
        } else if (type == variables.TYPE_OPTION_TIME) {
          format = 'HH:mm';
        } else if (type == variables.TYPE_OPTION_DATE_TIME) {
          format = 'YYYY-MM-DD HH:mm';
        }
        $scope.questionnaire.questionnairePage[pIdx].
            questionnaireQuestion[qIdx].min =
            $scope.getDateStr($scope.questionnaire.questionnairePage[pIdx].
                questionnaireQuestion[qIdx].min, format);
        $scope.questionnaire.questionnairePage[pIdx].
            questionnaireQuestion[qIdx].max =
            $scope.getDateStr($scope.questionnaire.questionnairePage[pIdx].
                questionnaireQuestion[qIdx].max, format);
      };

      /**
       * focus DateTimePicker
       *
       * @return {void}
       */
      $scope.setMinMaxDate = function(ev, pIdx, qIdx) {
        // 自分のタイプがMinかMaxかを知る
        var curEl = ev.currentTarget;
        var elId = curEl.id;

        var typeMinMax;
        typeMinMax = elId.substr(elId.lastIndexOf('.') + 1);
        var targetEl;
        var targetElId;

        // 相方のデータを取り出す
        if (typeMinMax == 'min') {
          targetElId = elId.substring(0, elId.lastIndexOf('.')) + '.max';
        } else {
          targetElId = elId.substring(0, elId.lastIndexOf('.')) + '.min';
        }
        var targetEl = document.getElementById(targetElId);
        var limitDate = $(targetEl).val();

        // 自分のMinまたはMaxを設定する
        var el = document.getElementById(elId);
        if (typeMinMax == 'min') {
          $(el).data('DateTimePicker').maxDate(limitDate);
        } else {
          $(el).data('DateTimePicker').minDate(limitDate);
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
        page['nextPageSequence'] = page['pageSequence'] + 1;
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
          // 削除された場合は１枚目のタブを選択するようにする
          $scope.questionnaire.questionnairePage[0].tabActive = true;
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
        question['isRequire'] = 0;
        question['isSkip'] = 0;
        question['isChoiceRandom'] = 0;
        question['isRange'] = 0;
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
      $scope.copyQuestionToAnotherPage =
          function($event, pageIndex, qIndex, copyPageIndex) {
        var tmpQ = angular.copy(
            $scope.questionnaire.questionnairePage[pageIndex].
                questionnaireQuestion[qIndex]);
        $scope.questionnaire.questionnairePage[copyPageIndex].
            questionnaireQuestion.push(tmpQ);

        $scope._resetQuestionnaireQuestionSequence(copyPageIndex);
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
        // ここでやってはいけない！ページの再読み込みが走る
        //$event.stopPropagation();
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
         * change Question Type
         *
         * @return {void}
         */
      $scope.changeQuestionType = function($event, pIdx, qIdx) {
        var questionType = $scope.questionnaire.questionnairePage[pIdx].
            questionnaireQuestion[qIdx].questionType;
        if (questionType != variables.TYPE_SELECTION &&
            questionType != variables.TYPE_SINGLE_SELECT_BOX) {
          $scope.questionnaire.questionnairePage[pIdx].
              questionnaireQuestion[qIdx].isSkip = 0;
          $scope.questionnaire.questionnairePage[pIdx].
              questionnaireQuestion[qIdx].isChoiceRandom = 0;
        }
        if (!$scope.questionnaire.questionnairePage[pIdx].
            questionnaireQuestion[qIdx].questionnaireChoice ||
            $scope.questionnaire.questionnairePage[pIdx].
            questionnaireQuestion[qIdx].questionnaireChoice.length == 0) {
          $scope.addChoice($event,
              pIdx,
              $scope.questionnaire.questionnairePage[pIdx].
                  questionnaireQuestion.length - 1,
              0,
              variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED,
              variables.MATRIX_TYPE_ROW_OR_NO_MATRIX);
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
      /**
       * Questionnaire is able set jump page and skip page
       *
       * @return {bool}
       */
      $scope.isDisabledSetSkip = function(page, question) {
        // ページの中の質問をチェック
        for (var i = 0; i < page.questionnaireQuestion.length; i++) {
          // もしも質問が引数で指定されているものである場合はチェックしない（continue)
          if (question && page.questionnaireQuestion[i] == question) {
            continue;
          }
          // スキップが設定されている？
          if (page.questionnaireQuestion[i].isSkip == variables.USES_USE) {
            // スキップが設定されている場合はtrueを返す
            // return true is disabled
            return true;
          }
        }
        return false;
      };
      /**
       * Questionnaire type is able display result ?
       *
       * @return {bool}
       */
      $scope.isDisabledDisplayResult = function(questionType) {
        if (questionType == variables.TYPE_TEST ||
            questionType == variables.TYPE_TEST_AREA ||
            questionType == variables.TYPE_DATE_AND_TIME) {
          return true;
        }
        return false;
      };

    });
