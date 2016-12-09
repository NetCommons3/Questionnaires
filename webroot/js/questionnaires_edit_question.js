/**
 * Created by りか on 2015/02/18.
 */
NetCommonsApp.constant('moment', moment);
//NetCommonsApp.requires.push('ngSanitize');

NetCommonsApp.controller('Questionnaires.edit.question',
    ['$scope', '$http', '$q', '$timeout', 'NetCommonsWysiwyg', 'moment',
      'questionnairesMessages', 'NC3_URL',
      function($scope, $http, $q, $timeout, NetCommonsWysiwyg, moment,
               questionnairesMessages, NC3_URL) {

        /**
         * tinymce
         *
         * @type {object}
         */
        $scope.tinymce = NetCommonsWysiwyg.new();

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
          MATRIX_TYPE_COLUMN: '1',

          SKIP_GO_TO_END: '99999',

          MAX_QUESTION_COUNT: 50,
          MAX_CHOICE_COUNT: 50
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
            function(postUrl, postData, questionnaire) {
          $scope.postUrl = postUrl;
          $scope.postData = postData;
          $scope.questionnaire = questionnaire;
          $scope.questionnaire.questionnairePage =
              $scope.toArray(questionnaire.questionnairePage);
          $scope.activeTabIndex = 0;

          // 各ページ処理
          for (var pIdx = 0; pIdx < $scope.questionnaire.questionnairePage.length; pIdx++) {

            var page = $scope.questionnaire.questionnairePage[pIdx];

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
            for (var qIdx = 0; qIdx < page.questionnaireQuestion.length; qIdx++) {

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
                $scope.questionnaire.questionnairePage[pIdx].hasError = true;
              }

              // 選択肢がないのならここでcontinue;
              if (!question.questionnaireChoice) {
                continue;
              }
              // 各質問の選択肢があればその選択肢の中に「その他」が入っているかの確認とフラグ設定
              // また質問の選択肢の中にエラーがあるかのフラグ設定
              for (var cIdx = 0; cIdx < question.questionnaireChoice.length; cIdx++) {

                var choice = question.questionnaireChoice[cIdx];
                if (choice.otherChoiceType != variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
                  $scope.questionnaire.questionnairePage[pIdx].
                      questionnaireQuestion[qIdx].hasAnotherChoice = true;
                }
                if (choice.errorMessages) {
                  $scope.questionnaire.questionnairePage[pIdx].
                      questionnaireQuestion[qIdx].hasError = true;
                  $scope.questionnaire.questionnairePage[pIdx].hasError = true;
                }
              }

            }
          }
        };
        /**
         * toArray
         *
         * 配列型のはずの変数がなぜかObject扱いになる場合があるので念のための変換
         * @return {Array}
         */
        $scope.toArray = function(src) {
          var dst = new Array();
          angular.forEach(src, function(obj, key) {
            obj = $scope._toArray(obj);
            dst[key] = obj;
          });
          return dst;
        };
        /**
         * _toArray
         *
         * toArrayの再帰関数
         * @return {Object}
         */
        $scope._toArray = function(src) {
          var dst = new Object();
          angular.forEach(src, function(obj, key) {
            if (key == 'questionnaireQuestion' || key == 'questionnaireChoice') {
              obj = $scope.toArray(obj);
            }
            dst[key] = obj;
          });
          return dst;
        };
        /**
         * アコーディオンヘッダの中のドロップダウンメニューボタンのクリックで
         * アコーディオンが開閉するのを抑止するための
         *
         * @return {String}
         */
        $scope.deter = function($event) {
          $event.preventDefault();
          $event.stopPropagation();
        };
        /**
         * get Date String
         *
         * @return {String}
         */
        $scope.getDateStr = function(dateStr, format) {
          if (! dateStr) {
            return '';
          }
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
            var d = moment(dateStr);
            dateStr = d.format(format);
          }

          return dateStr;
        };

        /**
         * change DateTimePickerType
         *
         * @return {void}
         */
        $scope.changeDatetimepickerType = function(pIdx, qIdx) {
          var page = $scope.questionnaire.questionnairePage[pIdx];
          var question = page.questionnaireQuestion[qIdx];
          var type = question.questionTypeOption;
          var format;
          if (type == variables.TYPE_OPTION_DATE) {
            format = 'YYYY-MM-DD';
          } else if (type == variables.TYPE_OPTION_TIME) {
            format = 'HH:mm';
          } else if (type == variables.TYPE_OPTION_DATE_TIME) {
            format = 'YYYY-MM-DD HH:mm';
          }
          var min = question.min;
          var max = question.max;
          $scope.questionnaire.questionnairePage[pIdx].
              questionnaireQuestion[qIdx].min = $scope.getDateStr(min, format);
          $scope.questionnaire.questionnairePage[pIdx].
              questionnaireQuestion[qIdx].max = $scope.getDateStr(max, format);
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
          if (limitDate != '') {
            if (typeMinMax == 'min') {
              $(el).data('DateTimePicker').maxDate(limitDate);
            } else {
              $(el).data('DateTimePicker').minDate(limitDate);
            }
          }
        };
        /**
           * Add Questionnaire Page
           *
           * @return {void}
           */
        $scope.addPage = function($event) {
          if ($scope.checkMaxQuestion() == false) {
            alert(questionnairesMessages.maxQuestionWarningMsg);
            return;
          }
          var page = new Object();
          page['pageTitle'] = questionnairesMessages.newPageLabel +
              ($scope.questionnaire.questionnairePage.length + 1);
          page['pageSequence'] =
              $scope.questionnaire.questionnairePage.length;
          page['routeNumber'] = 0;
          page['key'] = '';
          page['questionnaireQuestion'] = new Array();
          $scope.questionnaire.questionnairePage.push(page);

          $scope.addQuestion($event,
              $scope.questionnaire.questionnairePage.length - 1);

          if ($event) {
            $event.stopPropagation();
          }
          /*$scope.activeTabIndex =
                $scope.questionnaire.questionnairePage.length - 1;
            console.log($scope.activeTabIndex);*/
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
            $scope._resetQuestionnairePageSequence(idx);
            // 削除された場合は１枚目のタブを選択するようにする
            $scope.questionnaire.questionnairePage[0].tabActive = true;
          }
        };

        /**
           * Questionnaire Page Sequence reset
           *
           * @return {void}
           */
        $scope._resetQuestionnairePageSequence = function(delPageIdx) {
          for (var i = 0; i < $scope.questionnaire.questionnairePage.length; i++) {
            $scope.questionnaire.questionnairePage[i].pageSequence = i;
            // skipページの調整
            var questions = $scope.questionnaire.questionnairePage[i].questionnaireQuestion;
            for (var qIdx = 0; qIdx < questions.length; qIdx++) {
              var choices = questions[qIdx].questionnaireChoice;
              for (var cIdx = 0; cIdx < choices.length; cIdx++) {
                if (choices[cIdx].skipPageSequence == variables.SKIP_GO_TO_END) {
                  continue;
                } else if (choices[cIdx].skipPageSequence == delPageIdx) {
                  // 削除ページが対象だったら
                  // 最後へ行くようにしておく
                  choices[cIdx].skipPageSequence = variables.SKIP_GO_TO_END;
                } else if (choices[cIdx].skipPageSequence > delPageIdx) {
                  // 削除ページより後ろの場合はー１しておく
                  var newSkipPage = parseInt(choices[cIdx].skipPageSequence) - 1;
                  choices[cIdx].skipPageSequence = newSkipPage.toString(10);
                }
              }
            }
          }
        };

        /**
           * Add Questionnaire Question
           *
           * @return {void}
           */
        $scope.addQuestion = function($event, pageIndex) {
          if ($scope.checkMaxQuestion() == false) {
            alert(questionnairesMessages.maxQuestionWarningMsg);
            return;
          }
          var question = new Object();
          if (!$scope.questionnaire.questionnairePage[pageIndex].questionnaireQuestion) {
            $scope.questionnaire.questionnairePage[pageIndex].
                questionnaireQuestion = new Array();
          }
          var newIndex =
              $scope.questionnaire.questionnairePage[pageIndex].
                  questionnaireQuestion.length;
          question['questionValue'] = questionnairesMessages.newQuestionLabel + (newIndex + 1);
          question['questionSequence'] = newIndex;
          question['questionType'] = variables.TYPE_SELECTION;
          question['key'] = '';
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
          $event.preventDefault();
          $event.stopPropagation();
        };

        /**
           * Move to another page Questionnaire Question
           *
           * @return {void}
           */
        $scope.copyQuestionToAnotherPage =
            function($event, pageIndex, qIndex, copyPageIndex) {
          if ($scope.checkMaxQuestion() == false) {
            alert(questionnairesMessages.maxQuestionWarningMsg);
            return;
          }
          var tmpQ = angular.copy(
              $scope.questionnaire.questionnairePage[pageIndex].questionnaireQuestion[qIndex]);
          $scope.questionnaire.questionnairePage[copyPageIndex].
              questionnaireQuestion.push(tmpQ);

          tmpQ.key = '';
          tmpQ.id = '';
          for (var i = 0; i < tmpQ.questionnaireChoice.length; i++) {
            tmpQ.questionnaireChoice[i].key = '';
            tmpQ.questionnaireChoice[i].id = '';
          }

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
          if (choiceCount == variables.MAX_CHOICE_COUNT) {
            alert(questionnairesMessages.maxChoiceWarningMsg);
            return;
          }

          var page = $scope.questionnaire.questionnairePage[pIdx];
          var question = page.questionnaireQuestion[qIdx];
          var choice = new Object();
          var choiceColorIdx = choiceCount % $scope.colorPickerPalette.length;
          var skipPage = parseInt(page['pageSequence']) + 1;

          if (!question.questionnaireChoice) {
            $scope.questionnaire.questionnairePage[pIdx].
                questionnaireQuestion[qIdx].questionnaireChoice = new Array();
          }
          var newIndex = question.questionnaireChoice.length;

          if (otherType != variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
            choice['choiceLabel'] = questionnairesMessages.newChoiceOtherLabel;
          } else {
            if (matrixType == variables.MATRIX_TYPE_ROW_OR_NO_MATRIX) {
              choice['choiceLabel'] = questionnairesMessages.newChoiceLabel + (choiceCount + 1);
            } else {
              choice['choiceLabel'] =
                 questionnairesMessages.newChoiceColumnLabel + (choiceCount + 1);
            }
          }
          // skipPageIndex仮設定
          if (pIdx == $scope.questionnaire.questionnairePage.length - 1) {
            choice['skipPageSequence'] = variables.SKIP_GO_TO_END;
          } else {
            choice['skipPageSequence'] = skipPage.toString(10);
          }

          // その他選択肢は必ず最後にするためにいったん取りのけておく
          var otherChoice = null;
          for (var i = 0; i < question.questionnaireChoice.length; i++) {
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
          choice['key'] = '';
          if (otherType != variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED) {
            choiceColorIdx = choice['choiceSequence'] % $scope.colorPickerPalette.length;
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
        $scope.changeAnotherChoice = function(pIdx, qIdx, otherType, matrixType) {

          var question = $scope.questionnaire.
              questionnairePage[pIdx].questionnaireQuestion[qIdx];

          //その他を持つように指示されている
          if (question.hasAnotherChoice) {
            $scope.addChoice(null, pIdx, qIdx, 0, otherType, matrixType);
          } else {
            // その他選択肢をなくすように指示されている
            for (var i = 0; i < question.questionnaireChoice.length; i++) {
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
          if ($scope.questionnaire.questionnairePage.length - 1 >= skipPageIndex) {
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
          var len = $scope.questionnaire.questionnairePage[pageIndex].
              questionnaireQuestion[qIndex].questionnaireChoice.length;
          for (var i = 0; i < len; i++) {
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
          // スキップロジックが使えない種類の質問になっていたら
          // スキップ設定をなくす
          if (questionType != variables.TYPE_SELECTION &&
              questionType != variables.TYPE_SINGLE_SELECT_BOX) {
            $scope.questionnaire.questionnairePage[pIdx].
                questionnaireQuestion[qIdx].isSkip = 0;
            $scope.questionnaire.questionnairePage[pIdx].
                questionnaireQuestion[qIdx].isChoiceRandom = 0;
          }
          // 集計結果表示ができない種類の質問になっていたら
          // 集計表示設定をなくす
          if ($scope.isDisabledDisplayResult(questionType)) {
            $scope.questionnaire.questionnairePage[pIdx].
                questionnaireQuestion[qIdx].isResultDisplay = 0;
          } else {
            // それ以外の時はとりあえず集計表示をONにしておく
            $scope.questionnaire.questionnairePage[pIdx].
                questionnaireQuestion[qIdx].isResultDisplay = 1;
          }
          // 日付タイプにされていたらオプション設定は「日付」にしておく
          if (questionType == variables.TYPE_DATE_AND_TIME) {
            $scope.questionnaire.questionnairePage[pIdx].
                questionnaireQuestion[qIdx].
                    questionTypeOption = variables.TYPE_OPTION_DATE;
          }
          // テキストなどのタイプから選択肢などに変更されたとき
          // 選択肢要素が一つもなくなっている場合があるので最低一つは存在するように
          if (!$scope.questionnaire.questionnairePage[pIdx].
              questionnaireQuestion[qIdx].questionnaireChoice ||
              $scope.questionnaire.questionnairePage[pIdx].
              questionnaireQuestion[qIdx].questionnaireChoice.length == 0) {
            $scope.addChoice($event, pIdx, qIdx, 0,
                variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED,
                variables.MATRIX_TYPE_ROW_OR_NO_MATRIX);
          }
          // マトリクス系に変更されたときは、少なくとも１つは列選択肢を作ること
          // 択一選択、複数選択、リスト選択のときは列選択をなくしておくこと
          if (questionType == variables.TYPE_MATRIX_SELECTION_LIST ||
             questionType == variables.TYPE_MATRIX_MULTIPLE) {
            $scope.checkMatrixColumn($event, 'add', pIdx, qIdx);
          } else {
            if (questionType == variables.TYPE_SELECTION ||
               questionType == variables.TYPE_SINGLE_SELECT_BOX ||
               questionType == variables.TYPE_MULTIPLE_SELECTION) {
              $scope.checkMatrixColumn($event, 'del', pIdx, qIdx);
            }

          }
        };
        /**
         * マトリクスの切り替えをしたときの列選択肢を足したり引いたりの処理
         *
         * @return {void}
         */
        $scope.checkMatrixColumn = function($event, ope, pIdx, qIdx) {
          // カラムタイプの選択肢を調べる
          // 指定されたオペレーションに従って消したり、追加したりする
          var question = $scope.questionnaire.questionnairePage[pIdx].
             questionnaireQuestion[qIdx];
          var cols = new Array();
          for (var i = 0; i < question.questionnaireChoice.length; i++) {
            var choice = question.questionnaireChoice[i];
            // カラムタイプ
            if (choice.matrixType != variables.MATRIX_TYPE_ROW_OR_NO_MATRIX) {
              cols.push(i);
            }
          }
          if (ope == 'add') {
            if (cols.length == 0) {
              $scope.addChoice($event, pIdx, qIdx,
                 question.questionnaireChoice.length - 1,
                 variables.OTHER_CHOICE_TYPE_NO_OTHER_FILED,
                 variables.MATRIX_TYPE_COLUMN);
            }
          } else {
            for (var delI = cols.length; delI > 0; delI--) {
              $scope.deleteChoice($event, pIdx, qIdx, cols[delI - 1]);
            }
          }
        };
        /**
         * Questionnaire Judgment sentence greater than
         *
         * @return {bool}
         */
        $scope.greaterThan = function(prop, tgt2) {
          return function(item) {
            return parseInt(item[prop]) > parseInt(tgt2[prop]);
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
          if (questionType == variables.TYPE_TEXT ||
              questionType == variables.TYPE_TEXT_AREA ||
              questionType == variables.TYPE_DATE_AND_TIME) {
            return true;
          }
          return false;
        };
        /**
         * 結果画面でアコーディオンの色を決定する
         *
         * @return {bool}
         */
        $scope.getResultAccordionClass = function(question) {
          if (question.isResultDisplay != variables.EXPRESSION_NOT_SHOW) {
            return 'panel panel-success';
          } else {
            return 'panel panel-default';
          }
        };
        /**
         * 現在の質問数に＋１したらMAXを超えてしまうかどうかのガード
         *
         * @return {bool}
         */
        $scope.checkMaxQuestion = function() {
          var ct = 0;
          var pageArr = $scope.questionnaire.questionnairePage;
          for (var i = 0; i < pageArr.length; i++) {
            ct += pageArr[i].questionnaireQuestion.length;
          }
          if (ct + 1 > variables.MAX_QUESTION_COUNT) {
            return false;
          }
          return true;
        };
        /**
         * １質問ずつの分割送信
         * JSで保持しているquestionnaireをそのまま送ると、
         * Angularが付け加えているハッシュ属性まで送ってしまうので明示的に送信データにコピーしている
         * 属性名はCakeで処理しやすいようにスネーク記法にしておく
         *
         * @return {void}
         */
        $scope.post = function(action) {
          var promises = new Array();
          var pageIndex = 0;

          $scope.$parent.sending = true;

          angular.forEach($scope.questionnaire.questionnairePage, function(page) {
            var qIndex = 0;
            angular.forEach(page.questionnaireQuestion, function(question) {
              var postPage = new Object();
              postPage.QuestionnairePage = new Object();
              postPage.QuestionnairePage[pageIndex] = new Object();
              postPage.QuestionnairePage[pageIndex].key = page.key;
              postPage.QuestionnairePage[pageIndex].page_sequence = pageIndex;

              postPage.QuestionnairePage[pageIndex].QuestionnaireQuestion = new Object();
              postPage.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex] = new Object();
              var postQ = postPage.QuestionnairePage[pageIndex].QuestionnaireQuestion[qIndex];

              postQ.key = question.key;
              postQ.question_sequence = qIndex;
              postQ.question_value = question.questionValue;
              postQ.question_type = question.questionType;
              postQ.description = question.description;
              postQ.is_require = question.isRequire;
              postQ.question_type_option = question.questionTypeOption;

              postQ.is_choice_random = question.isChoiceRandom;
              postQ.is_choice_horizon = question.isChoiceHorizon;
              postQ.is_skip = question.isSkip;
              postQ.is_range = question.isRange;
              postQ.min = question.min;
              postQ.max = question.max;
              postQ.is_result_display = question.isResultDisplay;
              postQ.result_display_type = question.resultDisplayType;

              if (question.questionnaireChoice) {
                postQ.QuestionnaireChoice = new Object();
                var cIndex = 0;
                angular.forEach(question.questionnaireChoice, function(choice) {
                  postQ.QuestionnaireChoice[cIndex] = new Object();
                  postQ.QuestionnaireChoice[cIndex].key = choice.key;
                  postQ.QuestionnaireChoice[cIndex].matrix_type = choice.matrixType;
                  postQ.QuestionnaireChoice[cIndex].other_choice_type = choice.otherChoiceType;
                  postQ.QuestionnaireChoice[cIndex].choice_sequence = cIndex;
                  postQ.QuestionnaireChoice[cIndex].choice_label = choice.choiceLabel;
                  postQ.QuestionnaireChoice[cIndex].choice_value = choice.choiceValue;
                  postQ.QuestionnaireChoice[cIndex].skip_page_sequence = choice.skipPageSequence;
                  postQ.QuestionnaireChoice[cIndex].graph_color = choice.graphColor;
                  cIndex++;
                });
              }

              promises.push($scope.postQuestionnaireElm(postPage));

              qIndex++;

            }, $scope);

            pageIndex++;

          }, $scope);

          $q.all(promises).then(
              function() {
                //$scope.postQuestionnaireElm(null);
                var fm = angular.element('#finallySubmitForm');
                fm[0].submit();
                // 送信に全て成功したときは画面がリダイレクトされるから何もしない
              },
              function() {
                // 送信が１回でも失敗したら送信中状態（sending）をfalseにしてエラー表示する
                $scope.$parent.sending = false;
                $scope.$parent.flashMessage(questionnairesMessages.sendingErrorMsg, 'danger', 5000);
              }
          );
        };
        /**
         * 送信処理実体
         *
         * @return {void}
         */
        $scope.postQuestionnaireElm = function(ajaxPost) {
          var deferred = $q.defer();
          var promise = deferred.promise;

          $http.get(NC3_URL + '/net_commons/net_commons/csrfToken.json')
             .then(function(response) {
                var token = response.data;
                var postData;
                postData = $scope.postData;
                postData._Token.key = token.data._Token.key;
                if (ajaxPost) {
                  postData.QuestionnairePage = ajaxPost.QuestionnairePage;
                } else {
                  postData.QuestionnairePage = new Object();
                }
                $http.post(NC3_URL + $scope.postUrl,
                    $.param({_method: 'POST', data: postData}),
                    {cache: false,
                      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    }
                ).then(function(response) {
                  var data = response.data;
                  deferred.resolve(data);
                }, function(response) {
                  var data = response.data;
                  var status = response.status;
                  deferred.reject(data, status);
                });
              },
              function(response) {
                var data = response.data;
                var status = response.status;
                deferred.reject(data, status);
              });

          promise.success = function(fn) {
            promise.then(fn);
            return promise;
          };
          promise.error = function(fn) {
            promise.then(null, fn);
            return promise;
          };
          return promise;
        };
     }]);
