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

<section id="nc-questionnaires-confirm-<?php echo (int)$frameId; ?>"
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

    <p>
        <?php echo __d('questionnaires', 'Please confirm your answers.'); ?>
    </p>

    <?php echo $this->Form->create('QuestionnaireAnswer', array(
    'name' => 'questionnaire_form_answer',
    'type' => 'post',
    'novalidate' => true,
    )); ?>
    <?php echo $this->Form->hidden('Frame.id', array('value' => $frameId)); ?>
    <?php echo $this->Form->hidden('Block.id', array('value' => $blockId,)); ?>
    <?php echo $this->Form->hidden('Questionnaire.id', array('value' => $questionnaire['Questionnaire']['id'])); ?>

    <?php $index = 0; ?>
    <?php foreach($questionnaire['QuestionnairePage'] as $pIndex=>$page): ?>
        <?php foreach($page['QuestionnaireQuestion'] as $qIndex=>$question): ?>

            <?php if (isset($answers[$question['id']])): ?>

                <?php if ($question['require_flag'] == QuestionnairesComponent::REQUIRES_REQUIRE): ?>
                    <div class="pull-left">
                        <?php echo $this->element('NetCommons.required'); ?>
                    </div>
                <?php endif ?>

                <label>
                    <?php echo $question['question_value']; ?>
                </label>

                <?php $index++; ?>
                <div class="form-group">
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
                    array(  'index' => $index,
                    'question' => $question,
                    'answer' => $answers[$question['id']],
                    'readonly' => true)); ?>
                </div>
            <?php endif ?>
        <?php endforeach; ?>
    <?php endforeach; ?>


    <div class="text-center">

        <a class="btn btn-default" href="<?php echo '/questionnaires/questionnaire_questions/answer/'."$frameId".'/'."$questionnaireId"; ?>">
            <span class="glyphicon glyphicon-chevron-left"></span>
            <?php echo __d('questionnaires', 'Start over'); ?>
        </a>

        <?php echo $this->Form->button(
        __d('net_commons', 'Confirm'),
        array(
        'class' => 'btn btn-primary',
        'name' => 'confirm_' . 'questionnaire',
        )) ?>
    </div>
    <?php echo $this->Form->end(); ?>

</section>