<?php
/**
 * Questionnares App Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

//App::uses('AppHelper', 'View/Helper');

/**
 * Questionnares Utility Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Questionnaires\View\Helper
 */
class QuestionnaireUtilHelper extends AppHelper {
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
	}

	public function getSubTitle($sub_title) {
		//$this->log("dbg:aaa",QUESTIONNAIRE_DEBUG);
    	if (!empty($sub_title)) {
        	return '<br /><small>'.h($sub_title).'</small>';
    	}
        return '';
	}

	public function transformPeriodYmd($period) {
		return str_replace('-','/', substr($period, 0, strpos($period,' ')));
	}

	public function getAnswerButtons($frameId, $answer_questionnaire, $editable) {
		//
		//回答ボタンの(回答済み|回答する|テスト)の決定
		//
		// satus != 公開状態 つまり編集者が見ている場合は「テスト」
		//
		// 公開状態の場合が枝分かれする
		// 公開時期にマッチしていない = 回答前＝回答する（disabled） 回答後＝回答済み（disabled）
		//
		// 公開期間中
		// 繰り返しの回答を許さない = 回答前＝回答する　回答後＝回答済み（Disabled）
		// 繰り返しの回答を許す = いずれの状態でも「回答する」
		if ($answer_questionnaire['LatestEntity']['status'] != NetCommonsBlockComponent::STATUS_PUBLISHED) {
			if (!$editable) {
				return '';
			}
			$answerButtonClass = 'info';
			$answerButtonLabel = __d('questionnaires', 'Test');
			$answerButtonDisabled = false;
		}
		else {
			$answerButtonClass = 'default';
			if ($answer_questionnaire['LatestEntity']['questionPeriodFlag'] == false) {
				$answerButtonDisabled = true;
				if (isset($answer_questionnaire['LatestAnswerSummary']) && isset($answer_questionnaire['LatestAnswerSummary']['answer_summary_id']) && intval($answer_questionnaire['LatestAnswerSummary']['answer_summary_id']) > 0) {
					$answerButtonLabel = __d('questionnaires', 'Finished');
				}
				else {
					$answerButtonLabel = __d('questionnaires', 'Answer');
				}
			}
			else {
				if ($answer_questionnaire['LatestEntity']['repeate_flag'] == QuestionnairesComponent::PERMISSION_NOT_PERMIT) { //厳密一致でなくてよい。
					if (isset($answer_questionnaire['LatestAnswerSummary']) && isset($answer_questionnaire['LatestAnswerSummary']['answer_summary_id']) && intval($answer_questionnaire['LatestAnswerSummary']['answer_summary_id']) > 0) {
						$answerButtonDisabled = true;
						$answerButtonLabel = __d('questionnaires', 'Finished');
					}
					else {
						$answerButtonDisabled = false;
						$answerButtonLabel = __d('questionnaires', 'Answer');
					}
				}
				else {
					$answerButtonDisabled = false;
					$answerButtonLabel = __d('questionnaires', 'Answer');
				}
			}
		}
		$to_answer_btn_disabled = $answerButtonDisabled ? 'disabled' : '';
		$id = $answer_questionnaire['Questionnaire']['id'];

		$html= <<<EOL
<a class="btn btn-{$answerButtonClass} questionnaire-listbtn {$to_answer_btn_disabled}" href="/questionnaires/questionnaire_questions/answer/$frameId/$id/" >{$answerButtonLabel}</a>
EOL;
		return $html;
	}
	public function getAggregateButtons($frameId, $answer_questionnaire, $editable)
	{
		//
		// 集計ボタン
		// 集計表示しない＝ボタン自体ださない
		// 集計表示する＝回答すみ、または回答期間終了　集計ボタン
		// 　　　　　　　アンケート自体が公開状態にない(not editor)
		//              未回答＆回答期間内　　　　　　　集計ボタン（disabled）
		if ($answer_questionnaire['LatestEntity']['total_show_flag'] == QuestionnairesComponent::EXPRESSION_NOT_SHOW) {
			return '';
		}
		if ($answer_questionnaire['LatestEntity']['status'] != NetCommonsBlockComponent::STATUS_PUBLISHED) {
			if (!$editable) {
				return '';
			}
			else {
				$aggregateButtonDisabled = false;
			}
		}
		else {
			// アンケート本体が始まってない
			$nowTime = time();
			if(isset($answer_questionnaire['LatestEntity']['start_period']) &&
				$nowTime < strtotime($answer_questionnaire['LatestEntity']['start_period'])) {
					$aggregateButtonDisabled = true;
			}
			// 始まっている
			else {
				// 集計結果公開期間外である
				if(isset($answer_questionnaire['LatestEntity']['total_show_start_peirod']) &&
					$nowTime < strtotime($answer_questionnaire['LatestEntity']['total_show_start_peirod'])) {
					$aggregateButtonDisabled = true;
				}
				// 集計結果公開期間内である
				else {
					// 一つでも回答している
					if (isset($answer_questionnaire['LatestAnswerSummary']) && array_key_exists('answer_summary_id', $answer_questionnaire['LatestAnswerSummary']) && intval($answer_questionnaire['LatestAnswerSummary']['answer_summary_id']) > 0) {
						$aggregateButtonDisabled = false;
					}
					// 未回答
					else {
						$aggregateButtonDisabled = true;
					}
				}

			}
		}
		$aggregateButtonLabel = __d('questionnaires', 'Aggregate');
		$aggregate_results_btn_disabled = $aggregateButtonDisabled ? 'disabled' : '';
		$id = $answer_questionnaire['Questionnaire']['id'];

		$html = <<<EOL
<a class="btn btn-success questionnaire-listbtn {$aggregate_results_btn_disabled}" href="/questionnaires/questionnaire_questions/total/$frameId/$id/">{$aggregateButtonLabel}</a>
EOL;
		return $html;
	}

}
