<?php
echo $this->NetCommonsHtml->script(array(
	'/questionnaires/js/questionnaires.js'
));
$maxQuestionWarningMsg = sprintf(
	__d('questionnaires', 'Number of questions that can be created is up %d . Already it has become %d .'),
	QuestionnairesComponent::MAX_QUESTION_COUNT,
	QuestionnairesComponent::MAX_QUESTION_COUNT
);
$maxChoiceWarningMsg = sprintf(
	__d('questionnaires', 'Number of choices that can be created is up %d per question. Already it has become %d .'),
	QuestionnairesComponent::MAX_CHOICE_COUNT,
	QuestionnairesComponent::MAX_CHOICE_COUNT
);

echo $this->NetCommonsHtml->scriptBlock(
	'NetCommonsApp.constant("questionnairesMessages", {' .
		'"newPageLabel": "' . __d('questionnaires', 'page') . '",' .
		'"newQuestionLabel": "' . __d('questionnaires', 'New Question') . '",' .
		'"newChoiceLabel": "' . __d('questionnaires', 'new choice') . '",' .
		'"newChoiceColumnLabel": "' . __d('questionnaires', 'new column choice') . '",' .
		'"newChoiceOtherLabel": "' . __d('questionnaires', 'other choice') . '",' .
		'"maxQuestionWarningMsg": "' . $maxQuestionWarningMsg . '",' .
		'"maxChoiceWarningMsg": "' . $maxChoiceWarningMsg . '",' .
	'});'
);
echo $this->NetCommonsHtml->css('/questionnaires/css/questionnaire.css');

