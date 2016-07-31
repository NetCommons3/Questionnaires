<?php
/**
 * Questionnaires routes configuration
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Current', 'NetCommons.Utility');

$params = array(
	'plugin' => 'questionnaires',
	'controller' => 'questionnaire_edit'
);
$options = array(
	'block_id' => '[0-9]+',
	'key' => '[a-zA-Z0-9_]+', //_は、UnitTestで使用するため
);

if (Current::isSettingMode()) {
	Router::connect(
		'/' . Current::SETTING_MODE_WORD .
			'/' . $params['plugin'] . '/' . $params['controller'] . '/:action/:block_id/:key/*',
		$params,
		$options
	);
	Router::connect(
		'/' . Current::SETTING_MODE_WORD .
			'/' . $params['plugin'] . '/' . $params['controller'] . '/:action/:block_id/*',
		$params,
		$options
	);
}

Router::connect(
	'/' . $params['plugin'] . '/' . $params['controller'] . '/:action/:block_id/:key/*',
	$params,
	$options
);
Router::connect(
	'/' . $params['plugin'] . '/' . $params['controller'] . '/:action/:block_id/*',
	$params,
	$options
);
