<?php
/**
 * answer header view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php if ($this->Workflow->canEdit('Questionnaire', $questionnaire)) : ?>

<div class="pull-right">
    <?php echo $this->Button->editLink('', array(
    'plugin' => 'questionnaires',
    'controller' => 'questionnaire_edit',
    'action' => 'edit_question',
    'key' => $questionnaire['Questionnaire']['key'])); ?>
</div>
<?php endif; ?>

<h1>
    <?php echo $this->Workflow->label($questionnaire['Questionnaire']['status']); ?>

    <?php echo $this->TitleIcon->titleIcon($questionnaire['Questionnaire']['title_icon']); ?>
    <?php echo $questionnaire['Questionnaire']['title']; ?>
    <small><?php echo $questionnaire['Questionnaire']['sub_title'];?></small>
</h1>
