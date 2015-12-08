<?php
/**
 * questionnaire page setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<div class="row">

<div class="col-sm-6">
    <h5><?php echo __d('questionnaires', 'Line choices'); ?></h5>
    <button type="button" class="btn btn-default"
            ng-click="addChoice($event, pageIndex, qIndex, matrixRows.length, '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>');">
        <span class="glyphicon glyphicon-plus"></span>
        <?php echo __d('questionnaires', 'Add line choices'); ?>
    </button>
    <ul class="list-group ">
        <li class="list-group-item" ng-repeat="(cIndex, choice) in matrixRows = (question.questionnaireChoice | toArray | filter: {matrixType:<?php echo QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>})" >
            <button class="btn btn-default pull-right" type="button"
                    ng-disabled="matrixRows.length < 2"
                    ng-click="deleteChoice($event, pageIndex, qIndex, choice.choiceSequence)"
                    ng-if="choice.otherChoiceType == <?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED; ?>">
                <span class="glyphicon glyphicon-remove"> </span>
            </button>
            <div class="form-inline">
                <?php echo $this->element('Questionnaires.QuestionnaireEdit/EditQuestion/options_before_published/choice'); ?>
            </div>
        </li>
    </ul>
    <button type="button" class="btn btn-default"
            ng-show="matrixRows.length > 2"
            ng-click="addChoice($event, pageIndex, qIndex, matrixRows.length, '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>');">
        <span class="glyphicon glyphicon-plus"></span>
        <?php echo __d('questionnaires', 'Add line choices'); ?>
    </button>
</div>

<div class="col-sm-6">
    <h5><?php echo __d('questionnaires', 'Column choices'); ?></h5>
    <button type="button" class="btn btn-default" ng-click="addChoice($event, pageIndex, qIndex, matrixColumns.length, '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_COLUMN; ?>');">
        <span class="glyphicon glyphicon-plus"></span>
        <?php echo __d('questionnaires', 'Add column choices'); ?>
    </button>

    <ul class="list-group">
        <li class="list-group-item" ng-repeat="(cIndex, choice) in matrixColumns = (question.questionnaireChoice | toArray | filter: {matrixType:<?php echo QuestionnairesComponent::MATRIX_TYPE_COLUMN; ?>})" >
            <button class="btn btn-default pull-right" type="button"
                    ng-disabled="matrixColumns.length < 2"
                    ng-click="deleteChoice($event, pageIndex, qIndex, choice.choiceSequence, '<?php echo __d('questionnaires', 'Do you want to delete this choice ?'); ?>')">
                <span class="glyphicon glyphicon-remove"> </span>
            </button>
            <div class="form-inline">
                <?php echo $this->element('Questionnaires.QuestionnaireEdit/EditQuestion/options_before_published/choice'); ?>
            </div>
        </li>
    </ul>

    <button type="button" class="btn btn-default"
            ng-show="matrixColumns.length > 2"
            ng-click="addChoice($event, pageIndex, qIndex, matrixColumns.length, '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_NO_OTHER_FILED ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_COLUMN; ?>');">
        <span class="glyphicon glyphicon-plus"></span>
        <?php echo __d('questionnaires', 'Add column choices'); ?>
    </button>
</div><!-- col-sm-6 -->

</div>

<div class="row">
    <div class="col-sm-12">
        <div class="checkbox">
            <label>
                <input type="checkbox"
                       ng-model="question.hasAnotherChoice"
                       ng-change="changeAnotherChoice(pageIndex, qIndex, '<?php echo QuestionnairesComponent::OTHER_CHOICE_TYPE_OTHER_FIELD_WITH_TEXT ?>', '<?php echo QuestionnairesComponent::MATRIX_TYPE_ROW_OR_NO_MATRIX; ?>')">
                <?php echo __d('questionnaires', 'add another choice'); ?>
            </label>
        </div>
    </div>
</div>