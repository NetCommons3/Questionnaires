<?php
/**
 * Questionnaires Component
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Component', 'Controller');

/**
 * QuestionnairesComponent
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuestionnairesComponent extends Component {

/**
 * default display questionnaire item count
 *
 * @var int
 */
	const	QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE = 10;

/**
 * csv download item count handling unit
 *
 * @var int
 */
	const	QUESTIONNAIRE_CSV_UNIT_NUMBER = 1000;

/**
 * questionnaire create options
 *
 * @var string
 */
	const QUESTIONNAIRE_CREATE_OPT_NEW = 'create';
	const QUESTIONNAIRE_CREATE_OPT_REUSE = 'reuse';

/**
 * questionnaire view filter
 *
 * @var string
 */
	const QUESTIONNAIRE_ANSWER_VIEW_ALL = 'viewall';
	const QUESTIONNAIRE_ANSWER_UNANSWERED = 'unanswered';
	const QUESTIONNAIRE_ANSWER_ANSWERED = 'answered';
	const QUESTIONNAIRE_ANSWER_TEST = 'test';

/**
 * status questionnaire status started
 *
 * @var string
 */
	const STATUS_STARTED = '0';

/**
 * status questionnaire status not start
 *
 * @var string
 */
	const STATUS_NOT_START = '1';

/**
 * status questionnaire status stopped
 *
 * @var string
 */
	const STATUS_STOPPED = '2';

/**
 * permission. not permit
 *
 * @var string
 */
	const PERMISSION_NOT_PERMIT = '0';

/**
 * permission. permit
 *
 * @var string
 */
	const PERMISSION_PERMIT = '1';

/**
 * anonymity. not anonymouse
 *
 * @var string
 */
	const ANONYMITY_NOT_ANONYMOUSE = '0';

/**
 * anonymity. anonymouse
 *
 * @var string
 */
	const ANONYMITY_ANONYMOUSE = '1';

/**
 * uses. not use
 *
 * @var string
 */
	const USES_NOT_USE = '0';

/**
 * uses. use
 *
 * @var string
 */
	const USES_USE = '1';

/**
 * expression. not show
 *
 * @var string
 */
	const EXPRESSION_NOT_SHOW = '0';

/**
 * expression. show
 *
 * @var string
 */
	const EXPRESSION_SHOW = '1';

/**
 * action. not act
 *
 * @var string
 */
	const ACTION_NOT_ACT = '0';

/**
 * action. act
 *
 * @var string
 */
	const ACTION_ACT = '1';

/**
 * type. selection
 *
 * @var string
 */
	const TYPE_SELECTION = '1';

/**
 * type. multiple selection
 *
 * @var string
 */
	const TYPE_MULTIPLE_SELECTION = '2';

/**
 * type. text
 *
 * @var string
 */
	const TYPE_TEXT = '3';

/**
 * type. text area
 *
 * @var string
 */
	const TYPE_TEXT_AREA = '4';

/**
 * type. Matrix (selection list)
 *
 * @var string
 */
	const TYPE_MATRIX_SELECTION_LIST = '5';

/**
 * type. Matrix (multiple)
 *
 * @var string
 */
	const TYPE_MATRIX_MULTIPLE = '6';

/**
 * type. date and time
 *
 * @var string
 */
	const TYPE_DATE_AND_TIME = '7';

/**
 * type. single select box
 *
 * @var string
 */
	const TYPE_SINGLE_SELECT_BOX = '8';

/**
 * types list
 *
 * @var array
 */
	static public $typesList = array(
		self::TYPE_SELECTION,
		self::TYPE_MULTIPLE_SELECTION,
		self::TYPE_TEXT,
		self::TYPE_TEXT_AREA,
		self::TYPE_MATRIX_SELECTION_LIST,
		self::TYPE_MATRIX_MULTIPLE,
		self::TYPE_DATE_AND_TIME,
		self::TYPE_SINGLE_SELECT_BOX
	);

/**
 * requires. not require
 *
 * @var string
 */
	const REQUIRES_NOT_REQUIRE = '0';

/**
 * requires. require
 *
 * @var string
 */
	const REQUIRES_REQUIRE = '1';

/**
 * type option. numeric value
 *
 * @var string
 */
	const TYPE_OPTION_NUMERIC = '1';

/**
 * type option. date
 *
 * @var string
 */
	const TYPE_OPTION_DATE = '2';

/**
 * type option. time
 *
 * @var string
 */
	const TYPE_OPTION_TIME = '3';

/**
 * type option. email
 *
 * @var string
 */
	const TYPE_OPTION_EMAIL = '4';

/**
 * type option. url
 *
 * @var string
 */
	const TYPE_OPTION_URL = '5';

/**
 * type option. phone number
 *
 * @var string
 */
	const TYPE_OPTION_PHONE_NUMBER = '6';

/**
 * type option. time
 *
 * @var string
 */
	const TYPE_OPTION_DATE_TIME = '7';

/**
 * type options list
 *
 * @var array
 */
	static public $typeOptionsList = array(
		self::TYPE_OPTION_NUMERIC,
		self::TYPE_OPTION_DATE,
		self::TYPE_OPTION_TIME,
		self::TYPE_OPTION_EMAIL,
		self::TYPE_OPTION_URL,
		self::TYPE_OPTION_PHONE_NUMBER
	);

/**
 * result display type. bar chart
 *
 * @var string
 */
	const RESULT_DISPLAY_TYPE_BAR_CHART = '0';

/**
 * result display type. pie chart
 *
 * @var string
 */
	const RESULT_DISPLAY_TYPE_PIE_CHART = '1';

/**
 * result display type. table
 *
 * @var string
 */
	const RESULT_DISPLAY_TYPE_TABLE = '2';

/**
 * result display type list
 *
 * @var array
 */
	static public $resultDispTypesList = array(
		self::RESULT_DISPLAY_TYPE_BAR_CHART,
		self::RESULT_DISPLAY_TYPE_PIE_CHART,
		self::RESULT_DISPLAY_TYPE_TABLE
	);

/**
 * matrix type. row or no matrix
 *
 * @var string
 */
	const MATRIX_TYPE_ROW_OR_NO_MATRIX = '0';

/**
 * matrix type. column
 *
 * @var string
 */
	const MATRIX_TYPE_COLUMN = '1';

/**
 * other choice type. no other field
 *
 * @var string
 */
	const OTHER_CHOICE_TYPE_NO_OTHER_FILED = '0';

/**
 * other choice type. other field with text
 *
 * @var string
 */
	const OTHER_CHOICE_TYPE_OTHER_FIELD_WITH_TEXT = '1';

/**
 * other choice type. other field with textarea
 *
 * @var string
 */
	const OTHER_CHOICE_TYPE_OTHER_FIELD_WITH_TEXTAREA = '2';

/**
 * display type. single
 *
 * @var string
 */
	const DISPLAY_TYPE_SINGLE = '0';

/**
 * display type. list
 *
 * @var string
 */
	const DISPLAY_TYPE_LIST = '1';

/**
 * display_sort_type. new arrivals
 *
 * @var string
 */
	const DISPLAY_SORT_TYPE_NEW_ARRIVALS = '0';

/**
 * display_sort_type. response time (descending)
 *
 * @var string
 */
	const DISPLAY_SORT_TYPE_RESPONSE_TIME_DESC = '1';

/**
 * display_sort_type. survey status order (ascending)
 *
 * @var string
 */
	const DISPLAY_SORT_TYPE_SURVEY_STATUS_ORDER_ASC = '2';

/**
 * display_sort_type. by title (ascending order)
 *
 * @var string
 */
	const DISPLAY_SORT_TYPE_BY_TITLE_ASC = '3';

/**
 * display_sort_types list
 *
 * @var array
 */
	static public $displaySortTypesList = array(
		self::DISPLAY_SORT_TYPE_NEW_ARRIVALS,
		self::DISPLAY_SORT_TYPE_RESPONSE_TIME_DESC,
		self::DISPLAY_SORT_TYPE_SURVEY_STATUS_ORDER_ASC,
		self::DISPLAY_SORT_TYPE_BY_TITLE_ASC
	);

/**
 * skip_flag. no_skip
 *
 * @var string
 */
	const SKIP_FLAGS_NO_SKIP = '0';

/**
 * skip_flag. skip
 *
 * @var string
 */
	const SKIP_FLAGS_SKIP = '1';

/**
 * skip_flag. goto end
 *
 * @var integer
 */
	const SKIP_GO_TO_END = 99999;
/**
 * first page sequence
 *
 * @var integer
 */
	const FIRST_PAGE_SEQUENCE = 0;

/**
 * test answer status, peform( means on air or HONBAN )
 *
 * @var string
 */
	const TEST_ANSWER_STATUS_PEFORM = '0';

/**
 * test answer status, test
 *
 * @var string
 */
	const TEST_ANSWER_STATUS_TEST = '1';

/**
 * aggrigate not matrix
 * @var string
	 */
	const AGGRIGATE_NOT_MATRIX = 'aggrigate_not_matrix';	//matrixの場合数字がくるので、あえて非数字の文字列にしました。

/**
 * percentage unit
 * @var string
 */
	const PERCENTAGE_UNIT = '%';

/**
 * not operation(=nop) mark
 * @var string
 */
	const NOT_OPERATION_MARK = '--';

/**
 * answer delimiter
 *
 * @var string
 */
	const ANSWER_DELIMITER = '|';
	const ANSWER_VALUE_DELIMITER = ':';

/**
 * questionnaire period stat
 *
 * @var integer
 */
	const QUESTIONNAIRE_PERIOD_STAT_IN = 1;
	const QUESTIONNAIRE_PERIOD_STAT_BEFORE = 2;
	const QUESTIONNAIRE_PERIOD_STAT_END = 3;

/**
 * questionnaire sort type
 *
 * @var integer
 */
	const QUESTIONNAIRE_SORT_MODIFIED = 0;
	const QUESTIONNAIRE_SORT_CREATED = 1;
	const QUESTIONNAIRE_SORT_TITLE = 2;
	const QUESTIONNAIRE_SORT_END = 3;

/**
 * getDisplayNumberOptions
 *
 * @return array
 */
	public static function getDisplayNumberOptions() {
		return array(
			1 => 1 . __d('questionnaires', 'item'),
			5 => 5 . __d('questionnaires', 'item'),
			10 => 10 . __d('questionnaires', 'item'),
			20 => 20 . __d('questionnaires', 'item'),
			50 => 50 . __d('questionnaires', 'item'),
			100 => 100 . __d('questionnaires', 'item'),
		);
	}

/**
 * getSortOrders
 *
 * @return array
 */
	public static function getSortOrders() {
		return array(
			self::QUESTIONNAIRE_SORT_MODIFIED => __d('questionnaires', 'New Modified'),
			self::QUESTIONNAIRE_SORT_CREATED => __d('questionnaires', 'Registration order'),
			self::QUESTIONNAIRE_SORT_TITLE => __d('questionnaires', 'Title'),
			self::QUESTIONNAIRE_SORT_END => __d('questionnaires', 'End period'),
		);
	}

/**
 * アンケート質問タイプのデータ配列を返す
 *
 * @return array 質問タイプの定値とそれに相応するラベル
 */
	public function getQuestionTypeOptionsWithLabel() {
		return array(
			self::TYPE_SELECTION => __d('questionnaires', 'Single choice'),
			self::TYPE_MULTIPLE_SELECTION => __d('questionnaires', 'Multiple choice'),
			self::TYPE_TEXT => __d('questionnaires', 'Single text'),
			self::TYPE_TEXT_AREA => __d('questionnaires', 'Multiple text'),
			self::TYPE_MATRIX_SELECTION_LIST => __d('questionnaires', 'Single choice matrix'),
			self::TYPE_MATRIX_MULTIPLE => __d('questionnaires', 'Multiple choice matrix'),
			self::TYPE_DATE_AND_TIME => __d('questionnaires', 'Date and time'),
			self::TYPE_SINGLE_SELECT_BOX => __d('questionnaires', 'List select')
		);
	}

/**
 * isSingleInputType
 *
 * @param int $type questionnaire type
 * @return bool
 */
	public static function isOnlyInputType($type) {
		// text, textarea, date などの単純入力タイプであるか
		if ($type == self::TYPE_TEXT) {
			return true;
		}
		if ($type == self::TYPE_TEXT_AREA) {
			return true;
		}
		if ($type == self::TYPE_DATE_AND_TIME) {
			return true;
		}
		return false;
	}

/**
 * isSelectionInputType
 *
 * @param int $type questionnaire type
 * @return bool
 */
	public static function isSelectionInputType($type) {
		// 択一選択、複数選択、リスト選択 などの単純選択タイプであるか
		if ($type == self::TYPE_SELECTION) {
			return true;
		}
		if ($type == self::TYPE_MULTIPLE_SELECTION) {
			return true;
		}
		if ($type == self::TYPE_SINGLE_SELECT_BOX) {
			return true;
		}
		return false;
	}

/**
 * isMatrixInputType
 *
 * @param int $type questionnaire type
 * @return bool
 */
	public static function isMatrixInputType($type) {
		// マトリクス選択タイプであるか
		if ($type == self::TYPE_MATRIX_SELECTION_LIST) {
			return true;
		}
		if ($type == self::TYPE_MATRIX_MULTIPLE) {
			return true;
		}
		return false;
	}

/**
 * getChoiceValueFromAnswerForSelection
 *
 * @param string $answerStr answer_value
 * @return array
 */
	public static function getChoiceValueFromAnswerForSelection($answerStr) {
		$answerStr = trim($answerStr, self::ANSWER_DELIMITER);
		$answerArray = explode(self::ANSWER_DELIMITER, $answerStr);
		$retArray = array();
		foreach ($answerArray as $answer) {
			$retArray[] = explode(self::ANSWER_VALUE_DELIMITER, $answer);
		}
		return $retArray;
	}
}
