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
<?php echo $this->element('Questionnaires.scripts'); ?>

<section id="nc-questionnaires-answer-<?php echo (int)$frameId; ?>"
         ng-controller="QuestionnairesAnswer">

    <?php echo $this->element('Questionnaires.answer_test_mode_header'); ?>


    <header>
        <h3>
            <?php echo $questionnaire['QuestionnaireEntity']['title']; ?>
            <?php if (isset($questionnaire['QuestionnaireEntity']['sub_title'])): ?>
            <small><?php echo $questionnaire['QuestionnaireEntity']['sub_title'];?></small>
            <?php endif ?>
        </h3>
    </header>


    <?php if ($questionPage['page_sequence'] > 0): ?>
    <?php $progress = (($questionPage['page_sequence']+1) / count($questionnaire['QuestionnairePage'])) * 100; ?>
    <div class="row">
        <div class="col-sm-8">
        </div>
        <div class="col-sm-4">
            <div class="progress">
                <progressbar class="progress-striped" value="<?php echo $progress ?>" type="warning"><?php echo $progress ?></progressbar>
            </div>
        </div>
    </div>
    <?php endif ?>

    <?php if (count($errors) > 0): ?>
        <div class="alert alert-danger" role="alert"><?php echo __d('questionnaires', 'Error occurred. Please check your answer.'); ?></div>
    <?php endif ?>

    <?php echo $this->Form->create('QuestionnaireAnswer', array(
    'name' => 'questionnaire_form_answer',
    'type' => 'post',
    'novalidate' => true,
    )); ?>
    <?php echo $this->Form->hidden('Frame.id', array('value' => $frameId)); ?>
    <?php echo $this->Form->hidden('Block.id', array('value' => $blockId,)); ?>
    <?php echo $this->Form->hidden('Questionnaire.id', array('value' => $questionnaire['Questionnaire']['id'])); ?>
    <?php echo $this->Form->hidden('QuestionnairePage.id', array('value' => $questionPage['id'])); ?>
    <?php echo $this->Form->hidden('QuestionnairePage.page_sequence', array('value' => $questionPage['page_sequence'])); ?>


    <?php foreach($questionPage['QuestionnaireQuestion'] as $index=>$question): ?>
        <div class="form-group <?php if (isset($errors[$question['id']]['answer_value'])): ?>has-error<?php endif ?>">
            <?php if ($question['require_flag'] == QuestionnairesComponent::REQUIRES_REQUIRE): ?>
            <div class="pull-left">
                <?php echo $this->element('NetCommons.required'); ?>
            </div>
            <?php endif ?>
            <label class="control-label">
                <?php echo $question['question_value']; ?>
            </label>
            <div class="help-block">
                <?php echo $question['description']; ?>
            </div>
            <?php if ($question['question_type'] == QuestionnairesComponent::TYPE_TEXT): ?>
            <?php $elementName = 'Questionnaires.question_text'; ?>
            <?php elseif ($question['question_type'] == QuestionnairesComponent::TYPE_SELECTION): ?>
            <?php $elementName = 'Questionnaires.question_selection'; ?>
            <?php elseif ($question['question_type'] == QuestionnairesComponent::TYPE_MULTIPLE_SELECTION): ?>
            <?php $elementName = 'Questionnaires.question_multiple_selection'; ?>
            <?php elseif ($question['question_type'] == QuestionnairesComponent::TYPE_TEXT_AREA): ?>
            <?php $elementName = 'Questionnaires.question_text_area'; ?>
            <?php elseif ($question['question_type'] == QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST): ?>
            <?php $elementName = 'Questionnaires.question_matrix_selection_list'; ?>
            <?php elseif ($question['question_type'] == QuestionnairesComponent::TYPE_MATRIX_MULTIPLE): ?>
            <?php $elementName = 'Questionnaires.question_matrix_multiple'; ?>
            <?php elseif ($question['question_type'] == QuestionnairesComponent::TYPE_DATE_AND_TIME): ?>
            <?php $elementName = 'Questionnaires.question_date_and_time'; ?>
            <?php elseif ($question['question_type'] == QuestionnairesComponent::TYPE_SINGLE_SELECT_BOX): ?>
            <?php $elementName = 'Questionnaires.question_single_select_box'; ?>
            <?php endif ?>

            <?php echo $this->element($elementName,
            array(  'index' => $question['id'],
            'question' => $question,
            'answer' => isset($questionPage['QuestionnaireAnswer'][$question['id']]) ? $questionPage['QuestionnaireAnswer'][$question['id']] : null,
            'readonly' => false)); ?>

            <?php if ($question['question_type'] != QuestionnairesComponent::TYPE_MATRIX_SELECTION_LIST &&$question['question_type'] != QuestionnairesComponent::TYPE_MATRIX_MULTIPLE): ?>
            <?php echo $this->Form->hidden('QuestionnaireAnswer.'.$question['id'].'.questionnaire_question_id', array(
            'value' => $question['id']
            ));?>
            <?php echo $this->Form->hidden('QuestionnaireAnswer.'.$question['id'].'.id', array(
            'value' => isset($questionPage['QuestionnaireAnswer'][$question['id']]['id']) ? $questionPage['QuestionnaireAnswer'][$question['id']]['id'] : null,
            ));?>
            <?php endif ?>


            <?php if (isset($errors[$question['id']]['answer_value'])): ?>
                <?php foreach ($errors[$question['id']]['answer_value'] as $message): ?>
                    <div class="has-error">
                        <div class="help-block">
                            <?php echo $message ?>
                        </div>
                    </div>
                <?php endforeach ?>
            <?php endif ?>
        </div>
    <?php endforeach; ?>





    <div class="text-center">
        <?php echo $this->Form->button(
        __d('net_commons', 'NEXT') . ' <span class="glyphicon glyphicon-chevron-right"></span>',
        array(
        'class' => 'btn btn-default',
        'name' => 'next_' . '',
        )) ?>
    </div>
    <?php echo $this->Form->end(); ?>

</section>
