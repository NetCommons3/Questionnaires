<?php
/**
 * QuestionnaireValidate Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuestionnaireAnswerTextBehavior', 'Questionnaires.Model/Behavior');

/**
 * TextArea Behavior
 *
 * @package  Questionnaires\Questionnaires\Model\Befavior\Answer
 * @author Allcreator <info@allcreator.net>
 */
class QuestionnaireAnswerTextAreaBehavior extends QuestionnaireAnswerTextBehavior {

/**
 * this answer type
 *
 * @var int
 */
	protected $_myType = QuestionnairesComponent::TYPE_TEXT_AREA;

/**
 * this answer type
 * needs max length check
 *
 * @var int
 */
	protected $_isMaxLengthCheck = true;

}