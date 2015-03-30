<?php
/**
 * NetCommons bootstrap
 *
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

define('QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE', 10);
define('QUESTIONNAIRE_DEFAULT_DISPLAY_PAGE_SIZE', 5);

/*
 * アンケート作成方式
 * 新規作成
 * 過去からの再利用
 */
define('QUESTIONNAIRE_CREATE_OPT_NEW', 'create');
define('QUESTIONNAIRE_CREATE_OPT_REUSE', 'reuse');

/*
 * アンケート回答状況
 * 回答状況
 * 全て表示
 * 未回答
 * 回答済
 * テスト
 *
 */
define('QUESTIONNAIRE_ANSEWER_VIEW_ALL',                'viewall');
define('QUESTIONNAIRE_ANSEWER_UNANSERERED',             'unanswered');
define('QUESTIONNAIRE_ANSEWER_ANSWERED',                'answered');
define('QUESTIONNAIRE_ANSEWER_TEST',                    'test');

/*
 * アンケート・デバッグ
 */
define('QUESTIONNAIRE_DEBUG',		'debug');

/*
 * 表示抑止
 */
define('QUESTIONNAIRE_DISABLED',		'disabled');

