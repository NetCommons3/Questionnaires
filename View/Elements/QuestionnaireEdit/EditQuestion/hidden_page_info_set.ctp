<?php
/**
 * questionnaire hidden page info set template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php
	echo $this->NetCommonsForm->hidden('QuestionnairePage.{{pageIndex}}.page_sequence',
	array('ng-value' => 'page.pageSequence'));
	echo $this->NetCommonsForm->hidden('QuestionnairePage.{{pageIndex}}.id',
	array('ng-value' => 'page.id'));
	echo $this->NetCommonsForm->hidden('QuestionnairePage.{{pageIndex}}.key',
	array('ng-value' => 'page.key'));
	$this->NetCommonsForm->hidden('QuestionnairePage.{{pageIndex}}.page_title',
	array('ng-value' => 'page.pageTitle'));
