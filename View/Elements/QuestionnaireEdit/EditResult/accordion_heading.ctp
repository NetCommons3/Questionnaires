<?php
/**
 * questionnaire edit reesult accordion heading template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<span class="glyphicon"
    ng-class="{
    'glyphicon-eye-open': question.isResultDisplay == <?php echo QuestionnairesComponent::EXPRESSION_SHOW; ?>,
    'glyphicon-eye-close': question.isResultDisplay == <?php echo QuestionnairesComponent::EXPRESSION_NOT_SHOW; ?>}">
</span>

<span class="questionnaire-accordion-header-title">
    {{question.questionValue}}
</span>

<span ng-if="question.hasError">
    <span class="glyphicon glyphicon-exclamation-sign text-danger"></span>
    <?php echo __d('questionnaires', 'There is an error'); ?>
</span>
