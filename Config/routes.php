<?php
/**
 * Pages routes configuration
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Kohei Teraguchi <kteraguchi@commonsnet.org>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('Current', 'NetCommons.Utility');

$params = array(
	'controller' => 'questionnaire_edit'
);
if (! Current::isSettingMode()) {
	$params[Current::SETTING_MODE_WORD] = false;
}

$options = array(
	'plugin' => 'questionnaires',
	'block_id' => '[0-9]+',
	'key' => '[a-zA-Z0-9_]+', //_は、UnitTestで使用するため
);

Router::connect(
	'/' . Current::SETTING_MODE_WORD . '/:plugin/' . $params['controller'] . '/:action/:block_id/:key/*',
	$params,
	$options
);
Router::connect(
	'/' . Current::SETTING_MODE_WORD . '/:plugin/' . $params['controller'] . '/:action/:block_id/*',
	$params,
	$options
);

Router::connect(
	'/:plugin/' . $params['controller'] . '/:action/:block_id/:key/*',
	$params,
	$options
);
Router::connect(
	'/:plugin/' . $params['controller'] . '/:action/:block_id/*',
	$params,
	$options
);
