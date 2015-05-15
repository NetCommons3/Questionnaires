<?php
/**
 * questionnaire comment template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
マトリックス用棒グラフ（実装中）
<!--
{{hoge}}
<div ui-chart="aggrigateData" chart-options="chartOptions"></div>
-->
<!--
<div ui-chart="someData" chart-options="myChartOpts"></div>
<script>
  angular.module('QuestionnaireCommon')
    .value('charting', {
      pieChartOptions: {
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
      }
    })
    .controller('Questionnaires', function ($scope, charting) {
      $scope.someData = [[
        ['Heavy Industry', 12],['Retail', 9], ['Light Industry', 14],
        ['Out of home', 16],['Commuting', 7], ['Orientation', 9]
      ]];
 
      $scope.myChartOpts = charting.pieChartOptions;
    });
</script>
-->
{{fuga}}
