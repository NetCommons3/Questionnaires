<?php
/**
 * questionnaire questionnaire edit title template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
	<h1 class="questionnaire-setting-ttl">
		<?php if (isset($this->data['Questionnaire']['title_icon'])) {
			echo $this->TitleIcon->titleIcon($this->data['Questionnaire']['title_icon']);
		}
		?>
		{{questionnaire.questionnaire.title}}
	</h1>
