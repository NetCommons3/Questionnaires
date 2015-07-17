/**
 * Created by りか on 2015/01/26.
 */
/**
 * Questionnaires.edit Javascript
 *
 * @param {string} Controller name
 */

NetCommonsApp.requires.push('QuestionnaireCommon');

NetCommonsApp.controller('Questionnaires.add',
    function($scope, NetCommonsBase, NetCommonsWysiwyg) {

      /**
         * Initialize
         *
         * @return {void}
         */
      $scope.initialize = function(frameId, questionnaires, createOption) {
        $scope.frameId = frameId;
        $scope.questionnaires = questionnaires;
        $scope.createOption = createOption;
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

    });

NetCommonsApp.controller('Questionnaires.setting',
    function($scope, NetCommonsBase, NetCommonsWysiwyg) {

      /**
         * tinymce
         *
         * @type {object}
         */
      $scope.tinymce = NetCommonsWysiwyg.new();

      /**
         * Initialize
         *
         * @return {void}
         */
      $scope.initialize = function(frameId, questionnaires) {
        $scope.frameId = frameId;
        $scope.questionnaires = questionnaires;

        $scope.questionnaires.questionnaire.startPeriod =
            $scope.Date($scope.questionnaires.questionnaire.startPeriod);
        $scope.questionnaires.questionnaire.endPeriod =
            $scope.Date($scope.questionnaires.questionnaire.endPeriod);
        $scope.questionnaires.questionnaire.totalShowStartPeriod =
            $scope.Date(
                $scope.questionnaires.questionnaire.totalShowStartPeriod);
        $scope.minDate = new Date();
      };
      $scope.publishQuestionnaire = function(e, isPublished, message) {
        if (isPublished == 0) {
          if (!confirm(message)) {
            e.stopPropagation();
            return false;
          }
        }
        angular.element('#questionnairePublishedForm-' +
            $scope.frameId).submit();
        return true;
      };
      $scope.deleteQuestionnaire = function(e, message) {
        if (confirm(message)) {
          angular.element('#questionnaireDeleteForm-' +
              $scope.frameId).submit();
          return true;
        }
        e.stopPropagation();
        return false;
      };
      $scope.Date = function(dateStr) {
        if (Date.parse(dateStr)) {
          return new Date(dateStr);
        } else {
          return new Date();
        }
      };
    });
