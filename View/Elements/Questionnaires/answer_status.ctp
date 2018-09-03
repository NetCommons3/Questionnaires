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

$list = array(
	QuestionnairesComponent::QUESTIONNAIRE_ANSWER_VIEW_ALL => __d('questionnaires', 'View All'),
	QuestionnairesComponent::QUESTIONNAIRE_ANSWER_UNANSWERED => __d('questionnaires', 'Unanswered'),
	QuestionnairesComponent::QUESTIONNAIRE_ANSWER_ANSWERED => __d('questionnaires', 'Answered'),
);
if (Current::permission('content_creatable')) {
	$list[QuestionnairesComponent::QUESTIONNAIRE_ANSWER_TEST] = __d('questionnaires', 'Test');
}
$urlParams = array_merge(array(
	'controller' => 'questionnaires',
	'action' => 'index'),
	$this->params['named']);
if (isset($this->params['named']['answer_status']) && array_key_exists($this->params['named']['answer_status'], $list)) {
	$currentStatus = $this->params['named']['answer_status'];
} else {
	$currentStatus = QuestionnairesComponent::QUESTIONNAIRE_ANSWER_VIEW_ALL;
}
?>

<div class="form-group">

	<label><?php echo __d('questionnaires', 'Answer status'); ?></label>

	<span uib-dropdown>
		<button type="button" class="btn btn-default"  uib-dropdown-toggle>
			<?php echo $list[$currentStatus]; ?>
			<span class="caret"></span>
		</button>
		<ul class="dropdown-menu" uib-dropdown-menu role="menu"  aria-labelledby="false">
			<?php foreach ($list as $key => $status) : ?>
				<li>
					<?php echo $this->NetCommonsHtml->link($status,
						array_merge($urlParams, array('answer_status' => $key))
					); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</span>
</div>
