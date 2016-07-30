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

NetCommonsApp.controller('Questionnaires',
    ['$scope', '$sce', '$timeout', '$log', '$attrs',
      function($scope, $sce, $timeout, $log, $attrs) {

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
     }]
);

NetCommonsApp.controller('QuestionnairesAnswer',
    ['$scope', '$sce', '$timeout', '$log', function($scope, $sce, $timeout, $log) {
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
        for (var qIdx = 0; qIdx < questionnairePage.questionnaireQuestion.length; qIdx++) {
          var question = questionnairePage.questionnaireQuestion[qIdx];
          // 各質問が日付・時刻のタイプならば
          if (question.questionType == variables.TYPE_DATE_AND_TIME) {
            if (answers[question.key]) {
              if (angular.isArray(answers[question.key])) {
                $scope.dateAnswer[question.key] = answers[question.key][0].answerValue;
              } else {
                $scope.dateAnswer[question.key] = answers[question.key].answerValue;
              }
            }
          }
        }
      };
    }]
);
NetCommonsApp.controller('QuestionnairesFrame',
    ['$scope', '$filter', '$sce', '$log', '$attrs', '$timeout',
      function($scope, $filter, $sce, $log, $attrs, $timeout) {
        /**
         * Initialize
         *
         * @return {void}
         */
        $scope.initialize = function(questionnaires, questionnaireFrameSettings) {
          $scope.questionnaires = questionnaires;
          $scope.questionnaireFrameSettings = questionnaireFrameSettings;
          $scope.WinBuf = {allCheck: false};
          $scope.isDisplay = new Array();
          for (var i = 0; i < $scope.questionnaires.length; i++) {
            if ($scope.questionnaires[i].questionnaireFrameDisplayQuestionnaire.id) {
              $scope.isDisplay[i] = true;
            } else {
              $scope.isDisplay[i] = false;
            }
          }
          $scope.status = false;
          $scope.title = false;
          $scope.answerStartPeriod = false;
          $scope.isTotalShow = false;
          $scope.modified = false;
        };
        /**
         * Questionnaire Frame Setting AllCheckbox clicked
         *
         * @return {void}
         */
        $scope.allCheckClicked = function() {
          for (var i = 0; i < $scope.questionnaires.length; i++) {
            if ($scope.WinBuf.allCheck == true) {
              $scope.isDisplay[i] = true;
            } else {
              $scope.isDisplay[i] = false;
            }
          }
        };
        /**
         * Questionnaire Frame Setting questionnaire list sort
         *
         * @return {void}
         */
        $scope.sort = function(fieldName, direction) {
          $scope.questionnaires =
              $filter('orderBy')($scope.questionnaires, fieldName, direction);
        };
     }]
);
