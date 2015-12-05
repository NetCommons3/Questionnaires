/**
 * Created by りか on 2015/01/26.
 */
/**
 * Questionnaires.edit Javascript
 *
 * @param {string} Controller name
 */

NetCommonsApp.controller('Questionnaires.add',
    function($scope, NetCommonsBase) {
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
    });

NetCommonsApp.controller('Questionnaires.setting',
    function($scope, NetCommonsBase, NetCommonsWysiwyg) {

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
       * tinymce
       *
       * @type {object}
       */
      $scope.tinymce = NetCommonsWysiwyg.new();

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
        var minDate = $('#publish_start').val();
        // maxの制限は
        var maxDate = $('#publish_end').val();

        if (elId == 'publish_start') {
          $('#publish_start').data('DateTimePicker').maxDate(maxDate);
        } else {
          $('#publish_end').data('DateTimePicker').minDate(minDate);
        }
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
