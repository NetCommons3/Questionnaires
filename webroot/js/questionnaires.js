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
NetCommonsApp.requires.push('datetimepicker');



NetCommonsApp.controller('Questionnaires',
    function($scope, $sce, $timeout, $log, $attrs,
             NetCommonsBase, NetCommonsWorkflow, NetCommonsFlash) {

      //$attrsと$evalを使い、ng-initディレクティブの評価をcontrollerの最初に行う.
      $scope.$eval($attrs.ngInit);


      /**
       * Questionnaire
       *
       * @type {Object.<string>}
       */
      $scope.questionnaire = {};

      /**
       * Initialize
       *
       * @return {void}
       */
      $scope.initialize = function(frameId, questionnaire) {
        $scope.frameId = frameId;
        $scope.questionnaire = questionnaire;
      };
    });
NetCommonsApp.controller('QuestionnairesAnswer',
    function($scope, $sce, $timeout, $log, NetCommonsBase, NetCommonsFlash) {
      $scope.Date = function(arg) {
        if (!arg || arg.length == 0) {
          return null;
        }
        return new Date(arg);
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
        $scope.allCheck = false;
      };
      /**
       * Questionnaire Frame Setting AllCheckbox clicked
       *
       * @return {void}
       */
      $scope.allCheckClicked = function() {
        for (var i = 0; i < questionnaires.length; i++) {
          var originId = questionnaires[i].origin_id;
          if (!displayQuestionnaire[originId]) {
            displayQuestionnaire.originId = originId;
          }
        }
      };

    }
);


