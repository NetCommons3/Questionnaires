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

<div class="form-group questionnaire-list-select">
<label><?php echo __d('questionnaires', 'Answer status'); ?></label>
<?php
	$list = array(
		QuestionnairesComponent::QUESTIONNAIRE_ANSWER_VIEW_ALL => __d('questionnaires', 'View All'),
	QuestionnairesComponent::QUESTIONNAIRE_ANSWER_UNANSWERED => __d('questionnaires', 'Unanswered'),
	QuestionnairesComponent::QUESTIONNAIRE_ANSWER_ANSWERED => __d('questionnaires', 'Answered'),
	);
	if ($contentEditable) {
		$list[QuestionnairesComponent::QUESTIONNAIRE_ANSWER_TEST] = __d('questionnaires', 'Test');
	}
	$url = Hash::merge(
	array('controller' => 'questionnaires', 'action' => 'index', $frameId),
	$this->params['named']
	);

	$currentStatus = isset($this->params['named']['answer_status']) ? $this->params['named']['answer_status'] : QuestionnairesComponent::QUESTIONNAIRE_ANSWER_VIEW_ALL;
?>
	<span class="btn-group">
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			<?php echo $list[$currentStatus]; ?>
			<span class="caret"></span>
		</button>
		<ul class="dropdown-menu" role="menu">
			<?php foreach ($list as $key => $status) : ?>
			<li<?php echo ($status === $currentStatus ? ' class="active"' : ''); ?>>
			<?php echo $this->Html->link($status,
			Hash::merge($url, array('answer_status' => $key))
			); ?>
			</li>
			<?php endforeach; ?>
		</ul>
	</span>

</div>