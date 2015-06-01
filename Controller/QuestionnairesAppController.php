<?php
/**
 * Questionnaires AppController
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

class QuestionnairesAppController extends AppController {

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'Security',
		'Pages.PageLayout',
	);

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Questionnaires.QuestionnaireFrameSetting'
	);

	public $qValidationErrors = array();

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		// このルームにすでにアンケートブロックが存在した場合で、
		// かつ、現在フレームにまだブロックが結びついてない場合、
		// すでに存在するブロックと現在フレームを結びつける
		if (empty($this->viewVars['frameId'])) {
			return;
		}

		$this->QuestionnaireFrameSetting->prepareBlock($this->viewVars['frameId']);

		// フレームセッティング確認
		// まだ該当のフレームセッティングがない場合新たに作成しておく
		$this->QuestionnaireFrameSetting->prepareFrameSetting($this->viewVars['frameKey']);
	}

/**
 * _getComments method
 * 指定されたアンケートデータに該当するコメントを取得する
 *
 * @param array $questionnaire アンケート
 * @return array $c コメントデータ
 */
	protected function _getComments($questionnaire) {
		$comment = $this->Comment->getComments(
			array(
				'plugin_key' => 'questionnaires',
				'content_key' => isset($questionnaire['key']) ? $questionnaire['key'] : null,
			)
		);
		$comment = $this->camelizeKeyRecursive($comment);
		return $comment;
	}

/**
 * _getNowTime method
 * 現在時刻を取得する
 *
 * @return string 現在時刻
 */
	protected function _getNowTime() {
		return date('Y-m-d H:i:s');
	}

/**
 * _sorted method
 * to sort a given array by key
 *
 * @param array $obj data array
 * @return array ソート後配列
 */
	protected function _sorted($obj) {
		// シーケンス順に並び替え、かつ、インデックス値は０オリジン連番に変更
		$path = 'QuestionnairePage.{n}';

		// ページ配列もないのでそのまま戻す
		if (!Hash::check($obj, $path)) {
			return $obj;
		}
		$obj = Hash::sort($obj, $path . '.page_sequence', 'asc', 'numeric');

		foreach ($obj['QuestionnairePage'] as &$page) {

			if (isset($page['QuestionnaireQuestion'])) {
				$page['QuestionnaireQuestion'] = Hash::sort($page['QuestionnaireQuestion'], '{n}.question_sequence', 'asc', 'numeric');

				foreach ($page['QuestionnaireQuestion'] as &$question) {
					if (isset($question['QuestionnaireChoice'])) {
						$question['QuestionnaireChoice'] = Hash::sort($question['QuestionnaireChoice'], '{n}.choice_sequence', 'asc', 'numeric');
					}
				}
			}
		}
		return $obj;
	}

/**
 * _changeBooleansToNumbers method
 * to change the Boolean value of a given array to 0,1
 *
 * @param array $data data array
 * @return array
 */
	protected function _changeBooleansToNumbers(Array $data) {
		// Note the order of arguments and the & in front of $value
		array_walk_recursive($data, array($this, '__converter'));
		return $data;
	}

/**
 * __converter method
 * to change the Boolean value to 0,1
 *
 * @param array &$value value
 * @param string $key key
 * @return void
 * @SuppressWarnings("unused")
 */
	private function __converter(&$value, $key) {
		if (is_bool($value)) {
			$value = ($value ? 1 : 0);
		}
	}

/**
 * get index sql condition method
 *
 * @param array $addConditions 追加条件
 * @return array
 */
	public function getCondition($addConditions = array()) {
		$conditions = $this->getConditionForAnswer($addConditions);
		$conditions['NOT'] = array('QuestionnaireFrameDisplayQuestionnaires.id' => null);
		$conditions['QuestionnaireFrameDisplayQuestionnaires.frame_key'] = $this->viewVars['frameKey'];

		if ($addConditions) {
			$conditions = array_merge($conditions, $addConditions);
		}
		return $conditions;
	}

/**
 * get index sql condition method
 *
 * @param array $addConditions 追加条件
 * @return array
 */
	public function getConditionForAnswer($addConditions = array()) {
		$answerStatus = isset($this->params['named']['answer_status']) ? $this->params['named']['answer_status'] : QUESTIONNAIRE_ANSEWER_VIEW_ALL;

		if ($answerStatus == QUESTIONNAIRE_ANSEWER_UNANSERERED) {
			$filter = array(
				'OR' => array(
					array('answer_summary_count' => null),
					array('answer_summary_count' => 0)
				)
			);
		} elseif ($answerStatus == QUESTIONNAIRE_ANSEWER_ANSWERED) {
			$filter = array(
				'answer_summary_count >' => 0
			);
		} elseif ($answerStatus == QUESTIONNAIRE_ANSEWER_TEST) {
			$filter = array(
				'status !=' => NetCommonsBlockComponent::STATUS_PUBLISHED
			);
		} else {
			$filter = array();
		}

		$conditions = array_merge($filter, array(
			'block_id' => $this->viewVars['blockId'],
		));
		if (!$this->viewVars['contentEditable']) {
			$conditions['is_active'] = true;
			$conditions['OR'] = array(
				'start_period <' => $this->_getNowTime(),
				'is_period' => false,
			);
		} else {
			$conditions['is_latest'] = true;
		}
		if ($this->viewVars['roomRoleKey'] == NetCommonsRoomRoleComponent::DEFAULT_ROOM_ROLE_KEY) {
			$conditions['is_no_member_allow'] = QuestionnairesComponent::PERMISSION_PERMIT;
		}

		if ($addConditions) {
			$conditions = array_merge($conditions, $addConditions);
		}
		return $conditions;
	}

/**
 * isAbleTo
 * Whether access to survey of the specified ID
 * Forced URL hack guard
 * And against the authority of the state of the specified questionnaire respondents put a guard
 * It is not in the public state
 * Out of period
 * Stopped
 * Repeatedly answer
 * You are not logged in to not forgive other than member
 *
 * @param array $questionnaire 対象となるアンケートデータ
 * @return bool
 */
	public function isAbleTo($questionnaire) {
		// 指定のアンケートの状態と回答者の権限を照らし合わせてガードをかける
		// 編集権限を持っていない場合
		//   公開状態にない
		//   期間外
		//   停止中
		//   繰り返し回答
		//   会員以外には許してないのに未ログインである

		// 編集権限を持っている場合
		//   公開状態にない場合はALL_OK
		//
		// 　公開状態になっている場合は
		//   期間外
		//   停止中
		//   繰り返し回答
		//   会員以外には許してないのに未ログインである

		// 公開状態が「公開」になっている場合は編集権限の有無にかかわらず共通だ
		// なのでまずは公開状態だけを確認する

		if ($questionnaire['Questionnaire']['status'] != NetCommonsBlockComponent::STATUS_PUBLISHED) {
			if (!$this->viewVars['contentEditable']) {
				return false;
			} else {
				return true;
			}
		}

		// 期間外
		if ($questionnaire['Questionnaire']['is_period'] == QuestionnairesComponent::USES_USE
			&& $questionnaire['Questionnaire']['period_range_stat'] != QuestionnairesComponent::QUESTIONNAIRE_PERIOD_STAT_IN) {
			return false;
		}

		// 会員以外には許していないのに未ログイン
		if ($questionnaire['Questionnaire']['is_no_member_allow'] == QuestionnairesComponent::PERMISSION_NOT_PERMIT) {
			if ($this->viewVars['roomRoleKey'] == NetCommonsRoomRoleComponent::DEFAULT_ROOM_ROLE_KEY) {
				return false;
			}
		}

		return true;
	}

/**
 * _isDuringTest
 * is this questionnaire under the test mode
 *
 * @param array $questionnaire Questionnaire data
 * @return bool
 */
	protected function _isDuringTest($questionnaire) {
		return $questionnaire['Questionnaire']['status'] == NetCommonsBlockComponent::STATUS_PUBLISHED ? false : true;
	}

}
