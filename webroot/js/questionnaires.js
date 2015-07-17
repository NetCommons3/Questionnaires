/**
 * @fileoverview Questionnaire Javascript
 * @author info@allcreator.net (Allcreator Co.)
 */
/**
 * The following features are still outstanding: popup delay, animation as a
 * function, placement as a function, inside, support for more triggers than
 * just mouse enter/leave, html popovers, and selector delegatation.
 */
/**
 * Questionnaires Javascript
 *
 * @param {string} Controller name
 * @param {function($scope, $sce)} Controller
 */

NetCommonsApp.requires.push('QuestionnaireCommon');


NetCommonsApp.controller('Questionnaires',
    function($scope, $sce, $timeout, $log, $attrs,
             NetCommonsBase, NetCommonsWorkflow, NetCommonsFlash) {

      //$attrsと$evalを使い、ng-initディレクティブの評価をcontrollerの最初に行う.
      $scope.$eval($attrs.ngInit);

      /**
       * Initialize
       *
       * @return {void}
       */
      $scope.initialize = function(frameId, questionnaire) {
        $scope.frameId = frameId;
        $scope.questionnaire = questionnaire;
      };
    }
);


NetCommonsApp.controller('QuestionnairesAnswer',
    function($scope, $sce, $timeout, $log, NetCommonsBase, NetCommonsFlash) {
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
        TYPE_DATE_AND_TIME: '7'
      };
      /**
       * Initialize
       *
       * @return {void}
       */
      $scope.initialize = function(questionnairePage, answers) {
        $scope.dateAnswer = new Object();
        for (var qIdx = 0;
             qIdx < questionnairePage.questionnaireQuestion.length;
             qIdx++) {
          var question = questionnairePage.questionnaireQuestion[qIdx];
          // 各質問が日付・時刻のタイプならば
          if (question.questionType == variables.TYPE_DATE_AND_TIME) {
            if (answers[question.originId]) {
              if (angular.isArray(answers[question.originId])) {
                $scope.dateAnswer[question.originId] =
                    $scope.questionnaireDate(
                    answers[question.originId][0].answerValue);
              } else {
                $scope.dateAnswer[question.originId] =
                    $scope.questionnaireDate(
                    answers[question.originId].answerValue);
              }
            }
          }
        }
      };

      $scope.questionnaireDate = function(arg) {
        /*
        if (!arg || arg.length == 0) {
          return null;
        }
        return new Date(arg);
        */
        // もしも時刻表示の場合は本日の日付文字列を付加して日時文字列扱いにする
        if (!arg || arg.length == 0) {
          return null;
        }
        var dateStr = arg;
        var regTime1 = /^\d{2}:\d{2}$/;
        var regTime2 = /^\d{2}:\d{2}:\d{2}$/;
        if (dateStr.match(regTime1) || dateStr.match(regTime2)) {
          var today = new Date();
          dateStr = today.getFullYear() +
              '-' + (today.getMonth() + 1) +
              '-' + today.getDate() +
              ' ' + dateStr;
        }
        if (Date.parse(dateStr)) {
          return new Date(dateStr);
        } else {
          return null;
        }

      };
    }
);


NetCommonsApp.controller('QuestionnairesFrame',
    function($scope, $sce, $log, NetCommonsBase, NetCommonsFlash,
    NetCommonsUser, $attrs, $timeout) {
      /**
         * Initialize
         *
         * @return {void}
         */
      $scope.initialize = function(frameId,
                                   questionnaires,
                                   questionnaireFrameSettings,
                                   displayQuestionnaire) {
        $scope.frameId = frameId;
        $scope.questionnaires = questionnaires;
        $scope.questionnaireFrameSettings = questionnaireFrameSettings;
        $scope.displayQuestionnaire = displayQuestionnaire;
        $scope.WinBuf = {allCheck: false};
      };
      /**
       * Questionnaire Frame Setting AllCheckbox clicked
       *
       * @return {void}
       */
      $scope.allCheckClicked = function() {
        for (var i = 0; i < $scope.questionnaires.length; i++) {
          var originId = $scope.questionnaires[i].Questionnaire.origin_id;
          if ($scope.WinBuf.allCheck == true) {
            $scope.displayQuestionnaire[originId] = originId;
          } else {
            $scope.displayQuestionnaire[originId] = false;
          }
        }
      };

    }
);


