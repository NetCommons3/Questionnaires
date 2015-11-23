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

/**
 * QuestionnairesAppController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuestionnairesAppController extends AppController {

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Questionnaires.Questionnaire',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'Security',
		'Pages.PageLayout',
		'Questionnaires.Questionnaires',
	);

/**
 * getNowTime method
 * 現在時刻を取得する
 *
 * @return string 現在時刻
 */
	public function getNowTime() {
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
		// ページ配列もないのでそのまま戻す
		if (!Hash::check($obj, 'QuestionnairePage.{n}')) {
			return $obj;
		}
		$obj['QuestionnairePage'] = Hash::sort($obj['QuestionnairePage'], '{n}.page_sequence', 'asc', 'numeric');

		foreach ($obj['QuestionnairePage'] as &$page) {
			if (!Hash::check($page, 'QuestionnaireQuestion.{n}')) {
				$page['QuestionnaireQuestion'] = Hash::sort($page['QuestionnaireQuestion'], '{n}.question_sequence', 'asc', 'numeric');

				foreach ($page['QuestionnaireQuestion'] as &$question) {
					if (!Hash::check($question, 'QuestionnaireChoice.{n}')) {
						$question['QuestionnaireChoice'] = Hash::sort($question['QuestionnaireChoice'], '{n}.choice_sequence', 'asc', 'numeric');
					}
				}
			}
		}
		return $obj;
	}

/**
 * changeBooleansToNumbers method
 * to change the Boolean value of a given array to 0,1
 *
 * @param array $data data array
 * @return array
 */
	public static function changeBooleansToNumbers(Array $data) {
		// Note the order of arguments and the & in front of $value
		array_walk_recursive($data, 'self::converter');
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
	public static function converter(&$value, $key) {
		if (is_bool($value)) {
			$value = ($value ? '1' : '0');
		}
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

		// 編集権限があればオールマイティＯＫなのでこの後の各種チェックは不要！
		if ($this->Questionnaire->canEditWorkflowContent($questionnaire)) {
			return true;
		}
		// 編集権限がない場合は、もろもろのチェックを行うこと

		// 読み取り権限がない？
		if (! $this->Questionnaire->canReadWorkflowContent()) {
			// それはだめだ
			return false;
		}

		// 基本、権限上、見ることができるコンテンツだ
		// しかし、アンケート独自の条件部分のチェックを行う必要がある
		// 期間外
		if ($questionnaire['Questionnaire']['public_type'] == WorkflowBehavior::PUBLIC_TYPE_LIMITED
			&& $questionnaire['Questionnaire']['period_range_stat'] != QuestionnairesComponent::QUESTIONNAIRE_PERIOD_STAT_IN) {
			return false;
		}

		// 会員以外には許していないのに未ログイン
		if ($questionnaire['Questionnaire']['is_no_member_allow'] == QuestionnairesComponent::PERMISSION_NOT_PERMIT) {
			if (empty(Current::read('User.id'))) {
				return false;
			}
		}

		return true;
	}
/**
 * isAbleToAnswer 指定されたIDに回答できるかどうか
 * 強制URLハックのガード
 * 指定のアンケートの状態と回答者の権限を照らし合わせてガードをかける
 * 公開状態にない
 * 期間外
 * 停止中
 * 繰り返し回答
 * 会員以外には許してないのに未ログインである
 *
 * @param array $questionnaire 対象となるアンケートデータ
 * @return bool
 */
	public function isAbleToAnswer($questionnaire) {
		$answerSummary = ClassRegistry::init('QuestionnaireAnswerSummary');

		if ($questionnaire['Questionnaire']['status'] != WorkflowComponent::STATUS_PUBLISHED) {
			return true;
		}
		// 繰り返し回答を許していないのにすでに回答済みか
		if ($questionnaire['Questionnaire']['is_repeat_allow'] == QuestionnairesComponent::PERMISSION_NOT_PERMIT) {
			$summary = $answerSummary->getNowSummaryOfThisUser(
				$questionnaire['Questionnaire']['key'],
				Current::read('User.id'),
				$this->Session->id());
			if ($summary) {
				return false;
			}
		}

		return true;
	}

/**
 * _getQuestionnaireKey
 *
 * @param array $questionnaire Questionnaire data
 * @return string
 */
	protected function _getQuestionnaireKey($questionnaire) {
		return $questionnaire['Questionnaire']['key'];
	}

}
