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

<?php if (!empty($question['min']) && !empty($question['max'])): ?>
    <span class="help-block"><?php echo sprintf(__d('questionnaires', 'Please %s <= enter <= %s'), $question['min'], $question['max']); ?></span>
<?php elseif (!empty($question['min'])): ?>
    <span class="help-block"><?php echo sprintf(__d('questionnaires', 'Please %s <= enter'), $question['min']); ?></span>
<?php elseif (!empty($question['max'])): ?>
    <span class="help-block"><?php echo sprintf(__d('questionnaires', 'Please enter <= %s'), $question['max']); ?></span>
<?php endif;