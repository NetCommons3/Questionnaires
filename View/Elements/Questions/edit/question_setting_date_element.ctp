<?php
/**
 * 日・時範囲の設定部品
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php
/*
 * filed
 * model
 * calOpenId
 * min
 * max
 * error
 */
?>

<div class="input-group"
     ng-show="question.questionTypeOption == <?php echo QuestionnairesComponent::TYPE_OPTION_DATE; ?>
					|| question.questionTypeOption == <?php echo QuestionnairesComponent::TYPE_OPTION_DATE_TIME; ?>">
    <input type="text"
           class="form-control"
           datepicker-popup
           ng-model="<?php echo $model; ?>"
           show-weeks="false"
           is-open="question.calendarOpened[<?php echo $calOpenId; ?>]"
           min="<?php echo $min; ?>"
           max="<?php echo $max; ?>" />
    <span class="input-group-btn">
        <button type="button" class="btn btn-default" ng-click="openCal($event, pageIndex, qIndex, <?php echo $calOpenId; ?>)">
            <i class="glyphicon glyphicon-calendar"></i>
        </button>
    </span>
</div>

<div ng-model="<?php echo $model; ?>"
     ng-show="question.questionTypeOption == <?php echo QuestionnairesComponent::TYPE_OPTION_DATE_TIME; ?>
					|| question.questionTypeOption == <?php echo QuestionnairesComponent::TYPE_OPTION_TIME; ?>">
    <timepicker hour-step="1" minute-step="15" ></timepicker>
</div>

<?php echo $this->Form->input($field,
array(
'type' => 'hidden',
'label' => false,
'ng-if' => 'question.questionTypeOption == ' . QuestionnairesComponent::TYPE_OPTION_DATE,
'ng-value' => $model . " | date : 'yyyy-MM-dd'"
));
?>
<?php echo $this->Form->input($field,
array(
'type' => 'hidden',
'label' => false,
'ng-if' => 'question.questionTypeOption == ' . QuestionnairesComponent::TYPE_OPTION_TIME,
'ng-value' => $model . " | date : 'HH:mm:ss'"
));
?>
<?php echo $this->Form->input($field,
array(
'type' => 'hidden',
'label' => false,
'ng-if' => 'question.questionTypeOption == ' . QuestionnairesComponent::TYPE_OPTION_DATE_TIME,
'ng-value' => $model . " | date : 'yyyy-MM-dd HH:mm:ss'"
));
?>
<?php echo $this->element(
'Questionnaires.errors', array(
'errorArrayName' => $error,
));