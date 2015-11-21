<?php
/**
 * questionnaire add question template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<div class="form-group text-right"
     ng-if="isPublished == 0"
     ng-show="page.questionnaireQuestion.length > 0">
    <button class="btn btn-success" type="button" ng-click="addQuestion($event, pageIndex)">
        <span class="glyphicon glyphicon-plus"></span>
        <?php echo __d('questionnaires', 'Add Question'); ?>
    </button>
</div>
