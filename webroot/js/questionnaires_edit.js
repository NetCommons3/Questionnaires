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
        QUESTIONNAIRE_CREATE_OPT_NEW: 'create',
        QUESTIONNAIRE_CREATE_OPT_REUSE: 'reuse',
        QUESTIONNAIRE_CREATE_OPT_TEMPLATE: 'template'
      };

      /**
         * Initialize
         *
         * @return {void}
         */
      $scope.initialize = function(frameId, questionnaires, createOption) {
        $scope.frameId = frameId;
        $scope.questionnaires = questionnaires;
        $scope.createOption = createOption;
        $scope.newTitle = '';
        $scope.templateFile = '';
        $scope.pastQuestionnaireSelect = '';
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
       * Questionnaire be disable to goto next
       *
       * @return {bool}
       */
      $scope.templateFileSet = function() {
        var el = jQuery('#templateFile');
        $scope.templateFile = el[0].value;
      };
      /**
       * Questionnaire be disable to goto next
       *
       * @return {bool}
       */
      $scope.isDisableToGoNext = function() {
        if ($scope.createOption ==
            variables.QUESTIONNAIRE_CREATE_OPT_NEW) {
          if ($scope.newTitle == '') {
            return true;
          }
        } else if ($scope.createOption ==
            variables.QUESTIONNAIRE_CREATE_OPT_TEMPLATE) {
          if ($scope.templateFile == '') {
            return true;
          }
        } else if ($scope.createOption ==
            variables.QUESTIONNAIRE_CREATE_OPT_REUSE) {
          if ($scope.pastQuestionnaireSelect == '') {
            return true;
          }
        }
        return false;
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
      };

      /**
       * focus DateTimePicker
       *
       * @return {void}
       */
      $scope.setMinMaxDate = function(ev, min, max) {
        // 自分
        var curEl = ev.currentTarget;
        var elId = curEl.id;

        // minの制限は
        var minDate = $('#start_period').val();
        // maxの制限は
        var maxDate = $('#end_period').val();

        if (elId == 'start_period') {
          $('#start_period').data('DateTimePicker').maxDate(maxDate);
        } else {
          $('#end_period').data('DateTimePicker').minDate(minDate);
        }
      };

      /**
       * publish button click
       *
       * @return {void}
       */
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

      /**
       * delete button click
       *
       * @return {void}
       */
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
