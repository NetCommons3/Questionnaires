<?php
/**
 * Faq edit template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->Form->hidden('QuestionnaireBlocksSetting.id', array(
'value' => isset($faqSetting['id']) ? (int)$faqSetting['id'] : null,
)); ?>

<?php echo $this->Form->hidden('QuestionnaireBlocksSetting.block_key', array(
'value' => isset($faqSetting['faqKey']) ? $faqSetting['faqKey'] : null,
)); ?>

<?php echo $this->Form->hidden('Block.id', array(
'value' => $blockId,
)); ?>

<?php echo $this->element('Blocks.block_role_setting', array(
'roles' => $roles,
'model' => 'QuestionnaireBlocksSetting',
'useWorkflow' => 'use_workflow',
'creatablePermissions' => array(
'contentCreatable' => __d('blocks', 'Content creatable roles'),
),
'options' => array(
Block::NEED_APPROVAL => __d('blocks', 'Need approval'),
Block::NOT_NEED_APPROVAL => __d('blocks', 'Not need approval'),
),
));
