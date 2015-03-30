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

<section>

    <?php echo $this->element('Questionnaires.answer_test_mode_header'); ?>


    <header>
        <h3>
            <?php echo $questionnaire['QuestionnaireEntity']['title']; ?>
            <?php if (isset($questionnaire['QuestionnaireEntity']['sub_title'])): ?>
            <small><?php echo $questionnaire['QuestionnaireEntity']['sub_title'];?></small>
            <?php endif ?>
        </h3>
    </header>

    <?php echo $this->Form->create('QuestionnaireAnswer', array(
    'name' => 'questionnaire_form_answer',
    'type' => 'post',
    'novalidate' => true,
    )); ?>
    <?php echo $this->Form->hidden('Frame.id', array('value' => $frameId)); ?>
    <?php echo $this->Form->hidden('Block.id', array('value' => $blockId,)); ?>
    <?php echo $this->Form->hidden('Questionnaire.id', array('value' => $questionnaire['Questionnaire']['id'])); ?>

    <p>
        <?php echo $questionnaire['QuestionnaireEntity']['thanks_content']; ?>
    </p>
    <hr>

    <div class="text-center">
        <a class="btn btn-default btn-lg" href="/<?php echo $topUrl; ?>" target="_self"><?php echo __d('questionnaires', 'Back to top'); ?></a>
        <?php if ($questionnaire['QuestionnaireEntity']['total_show_flag'] == QuestionnairesComponent::EXPRESSION_SHOW &&
            (!isset($questionnaire['QuestionnaireEntity']['total_show_start_peirod']) || time() > $questionnaire['QuestionnaireEntity']['total_show_start_peirod'])): ?>
        <a class="btn btn-primary btn-lg" href="/questionnaires/questionnaire_questions/total/<?php echo $frameId; ?>/<?php echo $questionnaire['Questionnaire']['id']; ?>/"  target="_self"><?php echo __d('questionnaires', 'Aggreagate'); ?></a>
        <?php endif ?>
    </div>
</section>