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

<header>
    <h1>
        <?php echo $questionnaire['Questionnaire']['title']; ?>
        <?php if (isset($questionnaire['Questionnaire']['sub_title'])): ?>
        <small><?php echo $questionnaire['Questionnaire']['sub_title'];?></small>
        <?php endif ?>
    </h1>
</header>