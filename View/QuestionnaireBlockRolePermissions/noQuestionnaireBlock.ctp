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

<div class="modal-body">
    <?php echo $this->element('NetCommons.setting_tabs', $settingTabs); ?>

    <div class="tab-content">
        <?php echo __d('questionnaires', 'There is no questionnaire. First, please create a  questionnaire.'); ?>
    </div>
</div>