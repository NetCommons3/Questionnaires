/**
 * Created by りか on 2015/01/26.
 */
/**
 * Questionnaires.edit Javascript
 *
 * @param {string} Controller name
 */

NetCommonsApp.requires.push('QuestionnaireCommon');

NetCommonsApp.controller('Questionnaires.edit',
    function($scope, NetCommonsBase, NetCommonsWysiwyg,
             NetCommonsUser, $timeout, $attrs) {


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
         * Initialize
         *
         * @return {void}
         */
      $scope.initialize = function(frameId, questionnaires) {
        $scope.frameId = frameId;
        $scope.questionnaires = questionnaires;
        $scope.filter = questionnaires.QuestionnairesSettingList.filter;
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
NetCommonsApp.controller('Questionnaires.add',
    function($scope, NetCommonsBase, NetCommonsWysiwyg,
             NetCommonsUser) {

      /**
         * show user information method
         *
         * @param {number} users.id
         * @return {string}
         */
      $scope.user = NetCommonsUser.new();

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
             * Relative path to Questionnaires.bootstrap
             *
             * @const
             */
        QUESTIONNAIRE_CREATE_OPT_NEW: 'create',
        QUESTIONNAIRE_CREATE_OPT_REUSE: 'reuse'
      };

      /**
         * Initialize
         *
         * @return {void}
         */
      $scope.initialize = function(frameId, questionnaires) {
        $scope.frameId = frameId;
        $scope.questionnaires = questionnaires;
        if ($scope.questionnaires.items.length == 0) {
          $scope.createOption = variables.QUESTIONNAIRE_CREATE_OPT_NEW;
        }
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
    function($scope, NetCommonsBase, NetCommonsWysiwyg,
             NetCommonsUser) {

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
         * Initialize
         *
         * @return {void}
         */
      $scope.initialize = function(frameId, questionnaires) {
        $scope.frameId = frameId;
        $scope.questionnaires = questionnaires;
        $scope.total_period_flag = false;
        if (Date.parse($scope.questionnaires.Questionnaire.start_period)) {
          $scope.questionnaires.Questionnaire.start_period =
              new Date($scope.questionnaires.Questionnaire.start_period);
        } else {
          $scope.questionnaires.Questionnaire.start_period = new Date();
        }
        if (Date.parse($scope.questionnaires.Questionnaire.end_period)) {
          $scope.questionnaires.Questionnaire.end_period =
              new Date($scope.questionnaires.Questionnaire.end_period);
        } else {
          $scope.questionnaires.Questionnaire.end_period = new Date();
        }
        if (Date.parse(
            $scope.questionnaires.Questionnaire.total_show_start_period)) {
          $scope.questionnaires.Questionnaire.total_show_start_period =
              new Date(
                  $scope.questionnaires.Questionnaire.total_show_start_period);
        } else {
          $scope.questionnaires.Questionnaire.total_show_start_period =
              new Date();
        }
        $scope.minDate = new Date();
        $scope.calendar_opened = [false, false, false];
      };
      $scope.openCal = function($event, calNo) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.calendar_opened[calNo] = true;
      };
      $scope.open = function($event) {
        $event.preventDefault();
        $event.stopPropagation();

        $scope.opened = true;
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
    });
