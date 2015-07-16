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

<?php if ($question['is_range'] == QuestionnairesComponent::USES_USE): ?>
    <?php if ($question['question_type'] == QuestionnairesComponent::TYPE_TEXT): ?>
        <?php if ($question['question_type_option'] == QuestionnairesComponent::TYPE_OPTION_NUMERIC): ?>
            <span class="help-block"><?php echo sprintf(__d('questionnaires', 'Please enter a number between %s and %s'), $question['min'], $question['max']); ?></span>
        <?php else: ?>
            <span class="help-block"><?php echo sprintf(__d('questionnaires', 'Please enter between %s letters and %s letters'), $question['min'], $question['max']); ?></span>
        <?php endif; ?>
    <?php elseif ($question['question_type'] == QuestionnairesComponent::TYPE_DATE_AND_TIME): ?>
        <span class="help-block"><?php echo sprintf(__d('questionnaires', 'Please enter at %s to %s'), $question['min'], $question['max']); ?></span>
    <?php endif; ?>
<?php endif;