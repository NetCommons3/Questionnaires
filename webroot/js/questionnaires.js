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
NetCommonsApp.requires.push('dialogs.main');

NetCommonsApp.controller('Questionnaires',
    function($scope, $sce, $timeout, $log, $attrs,
             NetCommonsBase, NetCommonsWorkflow, NetCommonsFlash) {
//aaaa

		$scope.$eval($attrs.ngInit);	//$attrsと$evalを使い、ng-initディレクティブの評価をcontrollerの最初に行う.

  		$scope.maxSize = 1;
		//以下の$scopeのproperty値は、ctpの中のpaginationディレクティブの初期値で与える
		//$scope.totalItems = 180;
		//$scope.itemsPerPage = 10;
		//$scope.currentPage = 1;
/***
		$scope.hoge = 'aa';
		$scope.aggrigateData = [[
			['Heavy Industry', 12],['Retail', 9], ['Light Industry', 14], 
			['Out of home', 16],['Commuting', 7], ['Orientation', 9]
		]];

 		$scope.chartOptions = { 
      		seriesDefaults: {
        		// Make this a pie chart.
        		renderer: jQuery.jqplot.PieRenderer, 
        		rendererOptions: {
          		// Put data labels on the pie slices.
          		// By default, labels show the percentage of the slice.
          		showDataLabels: true
        		}
      		}, 
      		legend: { show:true, location: 'e' }
    	};
		$scope.fuga = 'zz';
***/
      /**
       * plugin
       *
       * @type {object}
       */
      $scope.plugin = NetCommonsBase.initUrl('questionnaires', 'questionnaires');

      /**
       * workflow
       *
       * @type {object}
       */
      $scope.workflow = NetCommonsWorkflow.new($scope);

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


        /**
         *
         */
        $scope.showInit = function() {

        };
        /**
         *
         */
        $scope.goAnswer = function() {
            NetCommonsBase.get(
                $scope.plugin.getUrl('answer', [$scope.frameId, 3 + '.json'])
            )
            .success(function(data) {
            })
            .error(function(data) {
            });
        };

		/**
		 * Questionnaire Answer Status Changed
		 *
		 * @return {void}
		 */
		$scope.answerStatusChange = function(elm) {
			var promise = $timeout(function() {
				angular.element(elm.form).submit();
			});
		};

		/**
 		 * 改ページ検出方式 1
		 *
		 * @return {void}
		 */
		$scope.pageChanged = function(page,frameId){
			//回答方式selectの変化では、paginationのonSelectPageイベントは発動されないので、よりgood.
			//
    		console.log('DBG: paginationディレクティブのonSelectPageイベント補足方式 newPage: ' + page);
    		console.log('DBG: frameId:'+frameId);
			var promise = $timeout(function() {
				document.getElementById('QuestionnairePage'+frameId).value = page;		//ok
				angular.element(document.getElementById('questionnare_answer_list_paginator_'+frameId)).submit(); //ok
			});
		};
		
		/**
 		 * 改ページ検出方式 2
		 *
		 * @return {void}
		 */
		$scope.$watch('currentPage',function(newPage,oldPage){
			if (newPage === oldPage){
				//回答方式selectの変化でも$watchは発動されるので、currentPageの新旧値に変化なければ、無視する。
				return;
			}
    		console.log('$scopwのcurrentPageプロパティ監視方式 newPage: ' + newPage + ' oldPage: ' + oldPage);

			//現在はここでは処理はしていない。
		});

});
NetCommonsApp.controller('QuestionnairesAnswer',
    function($scope, $sce, $timeout, $log, NetCommonsBase, NetCommonsFlash) {
        $scope.Date = function(arg){
            if (arg.length == 0) {
                return null;
            }
            return new Date(arg);
        };
    }
);
NetCommonsApp.controller('QuestionnairesFrame',
    function($scope, $sce, $log, NetCommonsBase, NetCommonsFlash, NetCommonsUser, $attrs, $timeout, dialogs) {
        /**
         * Initialize
         *
         * @return {void}
         */
        $scope.initialize = function(frameId, questionnaires) {
            $scope.frameId = frameId;
            $scope.questionnaires = questionnaires;
        };

    }
);


