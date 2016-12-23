<?php
/**
 * QuestionnaireFrameDisplayQuestionnaireFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * Summary for QuestionnaireFrameDisplayQuestionnaireFixture
 */
class QuestionnaireFrameDisplayQuestionnaireFixture extends CakeTestFixture {

/**
 * Records
 *
 * @var array
 */
	public $records = array();

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		require_once App::pluginPath('Questionnaires') . 'Config' . DS . 'Schema' . DS . 'schema.php';
		$this->fields = (new QuestionnairesSchema())->tables[Inflector::tableize($this->name)];

		for ($i = 1; $i <= 50; $i = $i + 2) {
			$this->records[] = array(
				'id' => strval($i),
				'frame_key' => 'frame_3',
				'questionnaire_key' => 'questionnaire_' . strval($i + 1),
			);
			$this->records[] = array(
				'id' => strval($i + 1),
				'frame_key' => 'frame_3',
				'questionnaire_key' => 'questionnaire_' . strval($i + 1),
			);
		}
		parent::init();
	}
}
