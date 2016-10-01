<?php
/**
 * questionnaires page edit view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<article>
    <?php echo __d('questionnaires', 'not found this questionnaire.'); ?>

    <div class="text-center">
        <?php echo $this->BackTo->linkButton(__d('net_commons', 'Cancel'), $cancelUrl); ?>
    </div>
</article>
