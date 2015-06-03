<?php

echo $this->Html->script('http://rawgit.com/angular/bower-angular-sanitize/v1.2.25/angular-sanitize.js', false);
echo $this->Html->css(
	'/components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
	array(
		'plugin' => false,
		'inline' => false
	)
);
echo $this->Html->script(
	array(
		'/components/moment/min/moment.min.js',
		'/components/moment/min/moment-with-locales.min.js',
		'/components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
		'/components/angular-bootstrap-datetimepicker-directive/angular-bootstrap-datetimepicker-directive.js'),
	array(
		'plugin' => false,
		'inline' => false
	)
);

echo $this->Html->script('/net_commons/js/workflow.js', false);
echo $this->Html->script('/net_commons/js/wysiwyg.js', false);
echo $this->Html->script('Questionnaires.questionnaire_common.js');
echo $this->Html->script('Questionnaires.questionnaires.js');


echo $this->Html->css('Questionnaires.questionnaire.css');

