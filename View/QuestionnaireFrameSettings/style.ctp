<?php
/**
 * questionnaire setting list view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
* @author Allcreator <info@allcreator.net>
* @link http://www.netcommons.org NetCommons Project
* @license http://www.netcommons.org/license.txt NetCommons License
* @copyright Copyright 2014, NetCommons Project
*/
?>
<?php echo $this->element('Questionnaires.scripts'); ?>
<?php echo $this->Html->script('Questionnaires.questionnaires_edit.js');?>
<div id="nc-questionnaire-frame-settings-content-list-<?php echo (int)$frameId; ?>"
        >

    <?php $this->start('title'); ?>
    <?php echo __d('questionnaires', 'plugin_name'); ?>
    <?php $this->end(); ?>

    <div class="modal-header">
        <?php $title = $this->fetch('title'); ?>
        <?php if ($title) : ?>
        <?php echo $title; ?>
        <?php else : ?>
        <br />
        <?php endif; ?>
    </div>

    <div class="modal-body">

        <ul class="nav nav-tabs">
            <?php foreach ($tabLists as $tab): ?>
            <li role="presentation" class="<?php echo $tab['class'] ?>">
                <a href="<?php echo $tab['href']; ?>">
                    <?php echo $tab['tabTitle']; ?>
                </a>
            </li>
            <?php endforeach ?>
        </ul>
        <div class="tab-body has-feedback" >

フレームスタイルをここで設定？

        </div>
    </div>
</div>