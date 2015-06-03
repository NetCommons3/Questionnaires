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
    function($scope, $sce, $timeout, $log, NetCommonsBase, NetCommonsFlash) {

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

      $scope.initialize = function(frameId, questionnaire, questions) {
        $scope.frameId = frameId;
        $scope.questionnaire = questionnaire;
        $scope.questions = questions;

        // config オブジェクト生成
        $scope.config = new Object();

        // data オブジェクト生成
        $scope.data = new Object();

        // question毎に定義を作成
        for (var key in $scope.questions) {
          $scope.config[key] = new Object();

          if ($scope.questions[key].result_display_type ==
              variables.RESULT_DISPLAY_TYPE_BAR_CHART) {

            $scope.data[key] = new Array();

            if ($scope.questions[key].question_type ==
                variables.TYPE_MATRIX_MULTIPLE ||
                $scope.questions[key].question_type ==
                variables.TYPE_MATRIX_SELECTION_LIST) {
              $scope.config[key]['chart'] =
                  $scope.getMatrixBarConf($scope.questions[key]);
              $scope.data[key] = $scope.getMatrixBarData($scope.questions[key]);
            } else {
              $scope.config[key]['chart'] =
                  $scope.getBarConf($scope.questions[key]);
              $scope.data[key].push($scope.getBarData($scope.questions[key]));
            }
          } else {

            if ($scope.questions[key].question_type ==
                variables.TYPE_MATRIX_MULTIPLE ||
                $scope.questions[key].question_type ==
                variables.TYPE_MATRIX_SELECTION_LIST) {

              $scope.data[key] = new Object();

              for (var choiceId in
                  $scope.questions[key]['QuestionnaireChoice']) {
                var choice =
                    $scope.questions[key]['QuestionnaireChoice'][choiceId];
                if (choice.matrix_type ==
                    variables.MATRIX_TYPE_ROW_OR_NO_MATRIX) {
                  $scope.config[key][choice.origin_id] =
                      $scope.getMatrixPieConf($scope.questions[key], choice);
                  $scope.data[key][choice.origin_id] =
                      $scope.getMatrixPieData($scope.questions[key], choice);
                }
              }
            } else {

              $scope.data[key] = new Array();

              $scope.config[key]['chart'] =
                  $scope.getPieConf($scope.questions[key]);
              $scope.data[key] = $scope.getPieData($scope.questions[key]);
            }
          }
        }
      };
      $scope.getBarConf = function(question) {
        var obj = new Object();
        obj = {
          type: 'discreteBarChart',
          height: 450,
          margin: {
            top: 20,
            right: 20,
            bottom: 60,
            left: 55
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
          color: $scope.getColorArray(question)
        };
        return obj;
      };
      $scope.getMatrixBarConf = function(question) {
        var obj = new Object();
        obj = {
          type: 'multiBarChart',
          height: 450,
          margin: {
            top: 20,
            right: 20,
            bottom: 60,
            left: 55
          },
          transitionDuration: 500,
          showValues: true,
          clipEdge: true,
          staggerLabels: true,
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
            text: choice.choice_label
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
        for (var choiceKey in question.QuestionnaireChoice) {
          var choice = question.QuestionnaireChoice[choiceKey];
          if (question.question_type == variables.TYPE_MATRIX_MULTIPLE ||
              question.question_type == variables.TYPE_MATRIX_SELECTION_LIST) {
            if (choice.matrix_type == variables.MATRIX_TYPE_ROW_OR_NO_MATRIX) {
              continue;
            }
          }
          colorArray.push(choice.graph_color);
        }
        return colorArray;
      };
      $scope.getBarData = function(question) {
        var dataObj = new Object();
        dataObj['values'] = new Array();
        for (var choiceKey in question.QuestionnaireChoice) {
          var choice = question.QuestionnaireChoice[choiceKey];
          var y = choice.aggrigate_total.aggrigate_not_matrix;
          dataObj['values'].push(
              {label: $scope.getChartLabelText(choice.choice_label),
                value: y});
        }
        return dataObj;
      };
      $scope.getPieData = function(question) {
        var dataArray = new Array();
        for (var choiceKey in question.QuestionnaireChoice) {
          var choice = question.QuestionnaireChoice[choiceKey];
          var y = choice.aggrigate_total.aggrigate_not_matrix;
          dataArray.push(
              {label: $scope.getChartLabelText(choice.choice_label),
                value: y});
        }
        return dataArray;
      };
      $scope.getMatrixBarData = function(question) {
        // まず行データのみ、列データのオブジェクト配列作成（この後使う
        var rowChoice = new Object();
        var colChoice = new Object();
        for (var choiceKey in question.QuestionnaireChoice) {
          var choice = question.QuestionnaireChoice[choiceKey];
          if (choice.matrix_type == variables.MATRIX_TYPE_ROW_OR_NO_MATRIX) {
            rowChoice[choiceKey] = choice;
          } else {
            colChoice[choiceKey] = choice;
          }
        }

        var dataObjArr = new Array();
        for (var choiceKey in colChoice) {
          var choice = colChoice[choiceKey];
          var dataObj = new Object();
          dataObj['key'] = $scope.getChartLabelText(choice.choice_label);
          dataObj['values'] = new Array();
          for (var rowChoiceKey in rowChoice) {
            var rowC = rowChoice[rowChoiceKey];
            dataObj['values'].push(
                {
                  label: $scope.getChartLabelText(rowC.choice_label),
                  value: rowC.aggrigate_total[choice.origin_id]
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
        for (var choiceKey in question.QuestionnaireChoice) {
          if (question.QuestionnaireChoice[choiceKey].matrix_type !=
              variables.MATRIX_TYPE_ROW_OR_NO_MATRIX) {
            colChoice[question.QuestionnaireChoice[choiceKey].origin_id] =
                question.QuestionnaireChoice[choiceKey];
          }
        }

        var dataArr = new Array();
        for (var resultKey in choice.aggrigate_total) {
          var y = choice.aggrigate_total[resultKey];
          var lbl = $scope.getChartLabelText(colChoice[resultKey].choice_label);
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
    }
);
