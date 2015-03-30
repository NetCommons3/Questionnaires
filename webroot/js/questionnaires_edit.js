/**
 * Created by りか on 2015/01/26.
 */
/**
 * Questionnaires.edit Javascript
 *
 * @param {string} Controller name
 */

NetCommonsApp.requires.push('QuestionnaireCommon');
NetCommonsApp.requires.push('dialogs.main');

NetCommonsApp.controller('Questionnaires.edit',
    function($scope, NetCommonsBase, NetCommonsWysiwyg,
             NetCommonsUser, $timeout, $attrs, dialogs) {


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
            $scope.currentPageNumber = questionnaires.QuestionnairesSettingList.page.currentPageNumber;
            $scope.totalCount = questionnaires.QuestionnairesSettingList.page.totalCount;
            $scope.displayNumPerPage = questionnaires.QuestionnairesSettingList.page.displayNumPerPage;
            $scope.QuestionnaireEntity = questionnaires.questionnaire.QuestionnaireEntity;
        };
        /**
         * Questionnaire Status Filter Changed
         *
         * @return {void}
         */
        $scope.statusChange = function(elm) {
            //var elm = scp.selectChangeElement;
            $timeout(function() {
//                angular.element(elm.form).triggerHandler('submit');
                angular.element(elm.form).submit();
            });
        };
        /**
         * Questionnaire Edit List Page Changed
         *
         * @return {void}
         */
        $scope.pageChanged = function(page) {
            $timeout(function() {
                angular.element(document.getElementById('questionnaire_edit_list_pagenation_' + $scope.frameId)).submit();
            });
        };
    });
NetCommonsApp.controller('Questionnaires.create',
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
            $scope.createOption = questionnaires.QuestionnairesSettingList.createOption;
            $scope.newTitle = questionnaires.QuestionnairesSettingList.newTitle;
            $scope.pastQuestionnaireSelect =  questionnaires.pastQuestionnaireSelect;
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

            if($scope.questionnaires.QuestionnaireEntity.start_period != null) {
                $scope.questionnaires.QuestionnaireEntity.start_period = new Date($scope.questionnaires.QuestionnaireEntity.start_period);
            }
            if($scope.questionnaires.QuestionnaireEntity.end_period != null) {
                $scope.questionnaires.QuestionnaireEntity.end_period = new Date($scope.questionnaires.QuestionnaireEntity.end_period);
            }
            if($scope.questionnaires.QuestionnaireEntity.total_show_start_peirod != null) {
                $scope.questionnaires.QuestionnaireEntity.total_show_start_peirod = new Date($scope.questionnaires.QuestionnaireEntity.total_show_start_peirod);
            }
            $scope.minDate = new Date();
            $scope.calendar_opened = [false,false,false];
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
    });
