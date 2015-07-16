<?php
/**
 * Common code of Questionnaire model test
 *
 * @property Questionnaire $Questionnaire
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireModelTestBase', 'Questionnaires.Test/Case/Model');

/**
 * Common code of Questionnaire model test
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Questionnaires\Test\Case\Model
 */
class QuestionnaireValidationTestBase extends QuestionnaireModelTestBase {

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Questionnaire = ClassRegistry::init('Questionnaires.Questionnaire');
		$this->QuestionnairePage = ClassRegistry::init('Questionnaires.QuestionnairePage');
		$this->QuestionnaireQuestion = ClassRegistry::init('Questionnaires.QuestionnaireQuestion');
		$this->QuestionnaireChoice = ClassRegistry::init('Questionnaires.QuestionnaireChoice');
		$this->QuestionnaireFrameSetting = ClassRegistry::init('Questionnaires.QuestionnaireFrameSetting');
		$this->QuestionnaireFrameDisplayQuestionnaire = ClassRegistry::init('Questionnaires.QuestionnaireFrameDisplayQuestionnaire');
		$this->QuestionnaireBlocksSetting = ClassRegistry::init('Questionnaires.QuestionnaireBlocksSetting');
		$this->QuestionnaireAnswerSummary = ClassRegistry::init('Questionnaires.QuestionnaireAnswerSummary');
		$this->QuestionnaireAnswer = ClassRegistry::init('Questionnaires.QuestionnaireAnswer');
		//↑ここまでがModelの各クラス
		$this->Block = ClassRegistry::init('Blocks.Block');
		$this->Frame = ClassRegistry::init('Frames.Frame');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Questionnaire);
		unset($this->QuestionnairePage);
		unset($this->QuestionnaireQuestion);
		unset($this->QuestionnaireChoice);
		unset($this->QuestionnaireFrameSetting);
		unset($this->QuestionnaireFrameDisplayQuestionnaire);
		unset($this->QuestionnaireBlocksSetting);
		unset($this->QuestionnaireAnswerSummary);
		unset($this->QuestionnaireAnswer);
		unset($this->Block);
		unset($this->Frame);
		parent::tearDown();
	}

}
