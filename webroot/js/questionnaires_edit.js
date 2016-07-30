/**
 * Created by りか on 2015/01/26.
 */
/**
 * Questionnaires.edit Javascript
 *
 * @param {string} Controller name
 */

NetCommonsApp.controller('Questionnaires.add',
    ['$scope', function($scope) {
      /**
       * Initialize
       *
       * @return {void}
       */
      $scope.initialize = function(questionnaires, createOption) {
        $scope.questionnaires = questionnaires;
        $scope.createOption = createOption;
        $scope.templateFile = '';
        $scope.pastQuestionnaireSelect = '';
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
    }]);

NetCommonsApp.controller('Questionnaires.setting',
    ['$scope', 'NetCommonsWysiwyg', function($scope, NetCommonsWysiwyg) {

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
        $scope.questionnaire = questionnaires;
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
        var minDate = $('#answer_start_period').val();
        // maxの制限は
        var maxDate = $('#answer_end_period').val();

        if (elId == 'answer_start_period') {
          $('#answer_start_period').data('DateTimePicker').maxDate(maxDate);
        } else {
          $('#answer_end_period').data('DateTimePicker').minDate(minDate);
        }
      };
      /**
       * delete button click
       *
       * @return {void}
       */
      $scope.deleteQuestionnaire = function(e, message) {
        if (confirm(message)) {
          angular.element('#questionnaireDeleteForm-' + $scope.frameId).submit();
          return true;
        }
        e.stopPropagation();
        return false;
      };
    }]);
