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
<?php if ($readonly): ?>
    <?php echo $this->Form->input('QuestionnaireAnswer.'.$index.'.answer_value', array(
    'div' => 'form-inline',
    'type' => 'text',
    'label' => false,
    'class' => 'form-control',
    'value' => $answer[0]['answer_value'],
    'disabled' => $readonly,
    ));?>
<?php else: ?>
<div class="row" ng-init="QuestionnaireDateTimeAnswer<?php echo $question['id']; ?> = Date('<?php echo $answer[0]['answer_value']; ?>')">

    <?php if ($question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_DATE || $question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_DATE_TIME): ?>

    <div class="col-sm-3 form-group">
         <div class="input-group">
             <div class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></div>
             <input type="text" class="form-control" datepicker-popup="yyyy-MM-dd" ng-model="QuestionnaireDateTimeAnswer<?php echo $question['id']; ?>" show-weeks="false"  min="minDate" max="questionnaires.QuestionnaireEntity.end_period" />
         </div>
    </div>
    <?php endif ?>

    <?php if ($question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_TIME || $question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_DATE_TIME): ?>
    <div class="col-sm-3">
        <timepicker  ng-model="QuestionnaireDateTimeAnswer<?php echo $question['id']; ?>" hour-step="1" minute-step="15" ></timepicker>
    </div>
    <?php endif ?>

    <?php echo $this->Form->input('QuestionnaireAnswer.'.$index.'.answer_value', array(
        'type' => 'hidden',
        'ng-value' => "QuestionnaireDateTimeAnswer" . $question['id']. " | date : 'yyyy-MM-dd HH:mm:ss'"
    ));
    ?>
    <?php echo $this->Form->hidden('QuestionnaireAnswer.'.$index.'.matrix_choice_id', array(
    'value' => null
    ));?>
</div>
<?php endif ?>
