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
<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <label class="checkbox-inline">
                <?php echo $this->Form->checkbox('QuestionnairePage.' . $pageIndex . '.QuestionnaireQuestion.' . $qIndex . '.question_type_option',
                array(
                'value' => QuestionnairesComponent::TYPE_OPTION_NUMERIC,
                'disabled' => 'disabled',
                'ng-model' => 'question.question_type_option',
                'ng-checked' => 'question.question_type_option == ' . QuestionnairesComponent::TYPE_OPTION_NUMERIC
                ));
                ?>
                <?php echo __d('questionnaires', 'Please check if you want to limit the input to the numerical value.'); ?>
            </label>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-inline" ng-if="question.question_type_option == <?php echo QuestionnairesComponent::TYPE_OPTION_NUMERIC; ?>">
            <?php echo $this->Form->input('QuestionnairePage.' . $pageIndex . '.QuestionnaireQuestion.' . $qIndex . '.min',
            array(
            'div' => array('class' => 'form-group'),
            'disabled' => 'disabled',
            'label' => __d('questionnaires', 'Minimum'),
            'class' => 'form-control',
            'ng-model' => 'question.min'
            ));
            ?>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-inline" ng-if="question.question_type_option == <?php echo QuestionnairesComponent::TYPE_OPTION_NUMERIC; ?>">
            <?php echo $this->Form->input('QuestionnairePage.' . $pageIndex . '.QuestionnaireQuestion.' . $qIndex . '.max',
            array(
            'div' => array('class' => 'form-group'),
            'disabled' => 'disabled',
            'label' => __d('questionnaires', 'Maximum'),
            'class' => 'form-control',
            'ng-model' => 'question.max'
            ));
            ?>
        </div>
    </div>
</div>