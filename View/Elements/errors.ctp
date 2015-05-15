<?php
/**
 * questionnaire error template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="has-error" ng-if="<?php echo $errorArrayName; ?>">
	<div class="help-block" ng-repeat="errorMessage in <?php echo $errorArrayName; ?>">
		{{errorMessage}}
	</div>
</div>
