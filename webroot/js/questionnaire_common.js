/**
 * Created by AllCreator on 2015/02/13.
 */
//var QuestionnaireCommon = angular.module('QuestionnaireCommon', ['ui.chart']);
var QuestionnaireCommon = angular.module('QuestionnaireCommon', []);

/**
 * questionnaire directive
 */
QuestionnaireCommon.directive('selectChange', function (){
    var DDO = {
        restrict : 'A',
        link: function (scope, elm, attr) {
            elm.on('change', function (ev){
                scope.selectChangeElement = elm;
                scope.$eval(attr.selectChange);
            });
        }
    };
    return DDO;
});

QuestionnaireCommon.directive( 'commentPopoverOld', [ '$compile', function ($compile) {
    return {
        restrict: 'A',
        template: '<span>{{label}}</span>',
        link: function (scope, el, attrs) {
            var linkFn = $compile(attrs.popoverHtml);
            var output = linkFn(scope);
            scope.label = attrs.popoverLabel;
            $(el).popover({
                trigger: 'click',
                html: true,
                content: output,
                placement: attrs.popoverPlacement,
            });
        }
    };
}]);

QuestionnaireCommon.directive( 'commentPopover', ['$compile','$templateCache', function ($compile,$templateCache) {
    var getTemplate = function (num) {
        var templateId = 'templateId.html';
        var template = '';
        template = $templateCache.get(templateId);
        return template;
    }
    return {
        scope: {
            templateNumber: '=',
            placement: '@',
            popoverPlacement: '@',
            comments: '='
        },
        restrict: "AE",
        link: function (scope, element, attrs) {
            var popOverContent;
            var html = getTemplate(scope.templateNumber);
            popOverContent = $compile(html)(scope);
            var options = {
                content: popOverContent,
                placement: scope.popoverPlacement,
                html: true,
            };
            $(element).popover(options);
        },
    };
}]);


QuestionnaireCommon.directive('colorPalettePicker', [ function() {
    var defaultColors = ['#f38631', '#e0e4cd', '#69d2e7', '#68e2a7', '#f64649', '#4d5361', '#47bfbd', '#7c4f6c', '#23313c', '#9c9b7f', '#be5945', '#cccccc'];

    return {
        scope: {
            selected: '=',
            customizedColors: '=colors',
            ngModel: '=',
            name: '@'
        },
        restrict: 'AE',
        template: '<div class="input-group input-group-sm"><span class="input-group-btn"><div class="btn" style="background-color:{{selected}};">&nbsp;</div></span><input name={{name}} type="text" value="{{selected}}" class="form-control"><div class="input-group-btn"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button><ul class="dropdown-menu pull-right"><li><div ng-repeat="color in colors" class="questionnaire-color-palette" style="background-color:{{color}};" ng-click="pick(color)"></div></li></ul></div></div>',
        link: function (scope, element, attr) {
            scope.colors = scope.customizedColors || defaultColors;
            scope.selected = scope.ngModel || scope.colors[0];

            scope.pick = function(color) {
                scope.selected = color;
                scope.ngModel = color;
            };
        }
    }

}]);
/**
 * questionnaire filter
 */
/**
 * html tag strip
 */
QuestionnaireCommon.filter('htmlToPlaintext', function() {
        return function(text) {
            return String(text).replace(/<[^>]+>/gm, '');
        }
    }
);
/**
 * ServerDatetime filter
 *
 * @param {string} filter name
 * @param {Array} use service
 */
QuestionnaireCommon.filter('ncDatetime', ['$filter', function($filter) {
    return function(input) {
        var d = new Date(input);
        return $filter('date')(d, 'yyyy/MM/dd HH:mm');
    };
}]);
