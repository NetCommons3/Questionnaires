<?php

echo $this->Html->script('http://rawgit.com/angular/bower-angular-sanitize/v1.2.25/angular-sanitize.js', false);
echo $this->Html->script('http://rawgit.com/m-e-conroy/angular-dialog-service/v5.2.0/src/dialogs.js', false);
/*
echo $this->Html->script('/net_commons/angular-sanitize.js', false);
echo $this->Html->script('/net_commons/dialogs.js', false);
*/

echo $this->Html->script('/questionnaires/js/jquery.jqplot.js', false);

echo $this->Html->script('/net_commons/base/js/workflow.js', false);
echo $this->Html->script('/net_commons/base/js/wysiwyg.js', false);
echo $this->Html->script('Questionnaires.questionnaire_common.js');
echo $this->Html->script('Questionnaires.questionnaires.js');

echo $this->Html->css('/questionnaires/css/jquery.jqplot.css', false);

echo $this->Html->css('Questionnaires.questionnaire.css');
?>
