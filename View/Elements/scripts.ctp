<?php

echo $this->Html->script('http://rawgit.com/angular/bower-angular-sanitize/v1.2.25/angular-sanitize.js', false);
echo $this->Html->script('/net_commons/js/workflow.js', false);
echo $this->Html->script('/net_commons/js/wysiwyg.js', false);
echo $this->Html->script('Questionnaires.questionnaire_common.js');
echo $this->Html->script('Questionnaires.questionnaires.js');


echo $this->Html->css('Questionnaires.questionnaire.css');

