/**
 * @fileoverview Questionnaire graph Javascript
 * @author info@allcreator.net (Allcreator co.)
 */

NetCommonsApp.requires.push('nvd3');


/**
 * Questionnaire Graph Javascript
 *
 * @param {string} Controller name
 * @param {function($scope)} Controller
 */
NetCommonsApp.controller('QuestionnairesAnswerSummary',
    ['$scope', '$window', '$sce', '$timeout', '$log',
      function($scope, $window, $sce, $timeout, $log) {

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
          RESULT_DISPLAY_TYPE_BAR_CHART: '0',
          RESULT_DISPLAY_TYPE_PIE_CHART: '1',
          TYPE_SELECTION: '1',
          TYPE_MULTIPLE_SELECTION: '2',
          TYPE_TEXT: '3',
          TYPE_TEXT_AREA: '4',
          TYPE_MATRIX_SELECTION_LIST: '5',
          TYPE_MATRIX_MULTIPLE: '6',
          TYPE_DATE_AND_TIME: '7',
          TYPE_SINGLE_SELECT_BOX: '8',
          MATRIX_TYPE_ROW_OR_NO_MATRIX: '0',
          MATRIX_TYPE_COLUMN: '1'
        };

        $scope.initialize = function(questions) {
          $scope.questions = questions;

          // config オブジェクト生成
          $scope.config = new Object();

          // data オブジェクト生成
          $scope.data = new Object();

          // question毎に定義を作成
          for (var key in $scope.questions) {

            $scope.config[key] = new Object();

            if ($scope.questions[key].resultDisplayType ==
                variables.RESULT_DISPLAY_TYPE_BAR_CHART) {

              $scope.data[key] = new Array();

              if ($scope.questions[key].questionType == variables.TYPE_MATRIX_MULTIPLE ||
                  $scope.questions[key].questionType == variables.TYPE_MATRIX_SELECTION_LIST) {
                $scope.config[key]['chart'] = $scope.getMatrixBarConf($scope.questions[key]);
                $scope.data[key] = $scope.getMatrixBarData($scope.questions[key]);
              } else {
                $scope.config[key]['chart'] = $scope.getBarConf($scope.questions[key]);
                $scope.data[key].push($scope.getBarData($scope.questions[key]));
              }
            } else {

              if ($scope.questions[key].questionType == variables.TYPE_MATRIX_MULTIPLE ||
                  $scope.questions[key].questionType == variables.TYPE_MATRIX_SELECTION_LIST) {

                $scope.data[key] = new Object();

                for (var choiceId in $scope.questions[key]['questionnaireChoice']) {
                  var choice = $scope.questions[key]['questionnaireChoice'][choiceId];
                  if (choice.matrixType == variables.MATRIX_TYPE_ROW_OR_NO_MATRIX) {
                    $scope.config[key][choice.key] =
                        $scope.getMatrixPieConf($scope.questions[key], choice);
                    $scope.data[key][choice.key] =
                        $scope.getMatrixPieData($scope.questions[key], choice);
                  }
                }
              } else {

                $scope.data[key] = new Array();

                $scope.config[key]['chart'] = $scope.getPieConf($scope.questions[key]);
                $scope.data[key] = $scope.getPieData($scope.questions[key]);
              }
            }
          }
        };
        $scope.getBarConf = function(question) {
          var obj = new Object();
          var colors = $scope.getColorArray(question);

          var maxChoiceLen = 0;
          for (var choiceKey in question.questionnaireChoice) {
            var choice = question.questionnaireChoice[choiceKey];
            var len = choice.choiceLabel.length;
            if (len > maxChoiceLen) {
              maxChoiceLen = len;
            }
          }
          var bottomHeight = maxChoiceLen * 10;
          // 選択肢が長いとき、グラフ内に収まりきらないという問題に対応するため、
          // デフォルトラベルを傾けることにした
          obj = {
            type: 'discreteBarChart',
            height: 450,
            margin: {
              bottom: bottomHeight,
              left: 0
            },
            transitionDuration: 500,
            showValues: true,
            x: function(d) { return d.label; },
            y: function(d) { return d.value; },
            valueFormat: function(d) {
              return d3.format(',f')(d);
            },
            xAxis: {
              //axisLabel: 'Time (ms)',
              rotateLabels: 50,
              showMaxMin: false
              //tickFormat: function(d) {
              //  return d3.format(',f')(d);
              //}
            },
            yAxis: {
              //axisLabel: 'Y Axis',
              axisLabelDistance: 40,
              tickFormat: function(d) {
                return d3.format(',f')(d);
              }
            },
            color: colors
          };
          return obj;
        };
        $scope.getMatrixBarConf = function(question) {
          var obj = new Object();
          var maxChoiceLen = 0;
          for (var choiceKey in question.questionnaireChoice) {
            var choice = question.questionnaireChoice[choiceKey];
            if (choice.matrixType == '0') {
              var len = choice.choiceLabel.length;
              if (len > maxChoiceLen) {
                maxChoiceLen = len;
              }
            }
          }
          var bottomHeight = maxChoiceLen * 10;
          obj = {
            type: 'multiBarChart',
            height: 450,
            margin: {
              bottom: bottomHeight,
              left: 0
            },
            transitionDuration: 500,
            showValues: true,
            rotateLabels: 50,
            stacked: true,
            x: function(d) { return d.label; },
            y: function(d) { return d.value; },
            valueFormat: function(d) {
              return d3.format(',f')(d);
            },
            xAxis: {
              showMaxMin: false
            },
            yAxis: {
              axisLabelDistance: 40,
              tickFormat: function(d) {
                return d3.format(',f')(d);
              }
            },
            color: $scope.getColorArray(question)
          };
          return obj;
        };
        $scope.getPieConf = function(question) {
          var obj = new Object();
          obj = {
            type: 'pieChart',
            height: 500,
            x: function(d) {return d.label;},
            y: function(d) {return d.value;},
            showLabels: true,
            labelType: 'percent',
            transitionDuration: 500,
            labelThreshold: 0.05,
            labelSunbeamLayout: true,
            color: $scope.getColorArray(question)
          };
          return obj;
        };
        $scope.getMatrixPieConf = function(question, choice) {
          var obj = new Object();
          obj = {
            title: {
              enable: true,
              text: choice.choiceLabel
            },
            chart: {
              type: 'pieChart',
              height: 200,
              x: function(d) {return d.label;},
              y: function(d) {return d.value;},
              showLabels: true,
              labelType: 'percent',
              transitionDuration: 500,
              labelThreshold: 0.05,
              labelSunbeamLayout: true,
              color: $scope.getColorArray(question)
            }
          };
          return obj;
        };
        $scope.getColorArray = function(question) {
          var colorArray = new Array();
          for (var choiceKey in question.questionnaireChoice) {
            var choice = question.questionnaireChoice[choiceKey];
            if (question.questionType == variables.TYPE_MATRIX_MULTIPLE ||
                question.questionType == variables.TYPE_MATRIX_SELECTION_LIST) {
              if (choice.matrixType == variables.MATRIX_TYPE_ROW_OR_NO_MATRIX) {
                continue;
              }
            }
            colorArray.push(choice.graphColor);
          }
          return colorArray;
        };
        $scope.getBarData = function(question) {
          var dataObj = new Object();
          dataObj['values'] = new Array();
          for (var choiceKey in question.questionnaireChoice) {
            var choice = question.questionnaireChoice[choiceKey];
            var y = choice.aggregateTotal.aggregateNotMatrix;
            dataObj['values'].push(
                {label: $scope.getChartLabelText(choice.choiceLabel),
                  value: y});
          }
          return dataObj;
        };
        $scope.getPieData = function(question) {
          var dataArray = new Array();
          for (var choiceKey in question.questionnaireChoice) {
            var choice = question.questionnaireChoice[choiceKey];
            var y = choice.aggregateTotal.aggregateNotMatrix;
            dataArray.push(
                {label: $scope.getChartLabelText(choice.choiceLabel),
                  value: y});
          }
          dataArray.sort(function(d1, d2) {return d2.value - d1.value});
          return dataArray;
        };
        $scope.getMatrixBarData = function(question) {
          // まず行データのみ、列データのオブジェクト配列作成（この後使う
          var rowChoice = new Object();
          var colChoice = new Object();
          for (var choiceKey in question.questionnaireChoice) {
            var choice = question.questionnaireChoice[choiceKey];
            if (choice.matrixType == variables.MATRIX_TYPE_ROW_OR_NO_MATRIX) {
              rowChoice[choiceKey] = choice;
            } else {
              colChoice[choiceKey] = choice;
            }
          }

          var dataObjArr = new Array();
          for (var choiceKey in colChoice) {
            var choice = colChoice[choiceKey];
            var dataObj = new Object();
            dataObj['key'] = $scope.getChartLabelText(choice.choiceLabel);
            dataObj['values'] = new Array();
            for (var rowChoiceKey in rowChoice) {
              var rowC = rowChoice[rowChoiceKey];
              dataObj['values'].push(
                  {
                    label: $scope.getChartLabelText(rowC.choiceLabel),
                    value: rowC.aggregateTotal[choice.key]
                  }
              );
            }
            dataObjArr.push(dataObj);
          }
          return dataObjArr;
        };
        $scope.getMatrixPieData = function(question, choice) {
          // まず行データのみ、列データのオブジェクト配列作成（この後使う
          var colChoice = new Object();
          for (var choiceKey in question.questionnaireChoice) {
            if (question.questionnaireChoice[choiceKey].matrixType !=
                variables.MATRIX_TYPE_ROW_OR_NO_MATRIX) {
              colChoice[question.questionnaireChoice[choiceKey].key] =
                  question.questionnaireChoice[choiceKey];
            }
          }

          var dataArr = new Array();
          for (var resultKey in choice.aggregateTotal) {
            var y = choice.aggregateTotal[resultKey];
            var lbl = $scope.getChartLabelText(colChoice[resultKey].choiceLabel);
            dataArr.push({label: lbl, value: y});
          }
          return dataArr;
        };
        $scope.getChartLabelText = function(tx) {
          // マルチバイト文字列は幅がうまく計算できなくてオーバーラップが発生したりしてた
          // しかし！なぜか両端に半角空白を２バイト置くときれいに計算して表示してくれる！
          // svgの闇は深い
          return '  ' + tx + '  ';
        };
      }]
);
