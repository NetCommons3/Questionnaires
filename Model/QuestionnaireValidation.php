<?php
/**
 * QuestionnaireValidation Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');

/**
 * Questionnaire utilty validation Model
 */
class QuestionnaireValidation extends QuestionnairesAppModel {

/**
 * Use database config
 *
 * @var string
 */
	public $useDbConfig = 'master';

/**
 * Use table config
 *
 * @var bool
 */
	public $useTable = false;

/**
 * checkPage
 *
 * @param array $questionnaire questionnaire
 * @return array error message
 */
	public function checkPage($questionnaire) {
		$this->loadModels([
			'QuestionnairePage' => 'Questionnaires.QuestionnairePage',
		]);

		// 少なくとも１ページは存在すること
		if (empty($questionnaire['QuestionnairePage'])) {
			$this->validationErrors['Questionnaire'][] = __d('questionnaires', 'please set at least one page.');
			return false;
		}
		$pageSeq = 0;
		foreach ($questionnaire['QuestionnairePage'] as $pageIndex => $page) {
			// それぞれのページのフィールド確認
			$this->QuestionnairePage->set($page);
			$this->QuestionnairePage->validates(array(
				'fieldList' => array(
					'questionnaire_id',
					'page_title',
					'page_sequence',
				)
			));
			if ($this->QuestionnairePage->validationErrors) {
				$this->validationErrors['QuestionnairePage'][$pageIndex] = $this->QuestionnairePage->validationErrors;
			}
			// ページシーケンスが０始まりで連番になっているか
			if ($pageSeq != $page['page_sequence']) {
				$this->validationErrors['QuestionnairePage'][$pageIndex][] = __d('questionnaires', 'Invalid page sequence set. Please try again from the beginning.');
				return false;
			}

			// ページの中の質問についてチェック
			$this->checkQuestion($questionnaire, $page, $this->validationErrors['QuestionnairePage'][$pageIndex]);
			$pageSeq++;
		}
		$this->validationErrors = Hash::filter($this->validationErrors);
		if (!empty($this->validationErrors)) {
			return false;
		} else {
			return true;
		}
	}

/**
 * checkQuestion
 *
 * @param array $questionnaire questionnaire
 * @param array $page questionnaire page
 * @param array &$errors error message array
 * @return array error message
 */
	public function checkQuestion($questionnaire, $page, &$errors) {
		$this->loadModels([
			'QuestionnaireQuestion' => 'Questionnaires.QuestionnaireQuestion',
		]);

		// 少なくとも１質問は存在すること
		if (empty($page['QuestionnaireQuestion'])) {
			$errors[] = __d('questionnaires', 'please set at least one question.');
			return false;
		}
		$qSeq = 0;
		foreach ($page['QuestionnaireQuestion'] as $qIndex => $question) {
			// それぞれの質問のフィールド確認
			$this->QuestionnaireQuestion->set($question);
			$this->QuestionnaireQuestion->validates(array(
				'fieldList' => array(
					'question_sequence',
					'question_value',
					'question_type',
					'description',
					'is_require',
					'question_type_option',
					'is_choice_random',
					'is_skip',
					'min',
					'max',
					'questionnaire_page_id',
				)
			));
			if ($this->QuestionnaireQuestion->validationErrors) {
				$errors['QuestionnaireQuestion'][$qIndex] = $this->QuestionnaireQuestion->validationErrors;
			}
			// 質問のシーケンスが０始まりで連番になっているか
			if ($qSeq != $question['question_sequence']) {
				$errors['QuestionnaireQuestion'][$qIndex][] = __d('questionnaires', 'Invalid question sequence set. Please try again from the beginning.');
				return false;
			}

			// 質問中の選択肢についてチェック
			if ($question['question_type'] != QuestionnairesComponent::TYPE_TEXT
			&& $question['question_type'] != QuestionnairesComponent::TYPE_TEXT_AREA
			&& $question['question_type'] != QuestionnairesComponent::TYPE_DATE_AND_TIME) {
				$this->checkChoice($questionnaire, $page, $question, $errors['QuestionnaireQuestion'][$qIndex]);
			}
			$qSeq++;
		}
		//
		if (!empty($this->validationErrors)) {
			return false;
		} else {
			return true;
		}
	}

/**
 * checkChoice
 *
 * @param array $questionnaire questionnaire
 * @param array $page questionnaire page
 * @param array $question questionnaire question
 * @param array &$errors error message array
 * @return array error message
 */
	public function checkChoice($questionnaire, $page, $question, &$errors) {
		$this->loadModels([
			'QuestionnaireChoice' => 'Questionnaires.QuestionnaireChoice',
		]);

		// 少なくとも１選択肢は存在すること
		if (empty($question['QuestionnaireChoice'])) {
			$errors[] = __d('questionnaires', 'please set at least one choice.');
			return false;
		}
		$cSeq = 0;
		foreach ($question['QuestionnaireChoice'] as $cIndex => $choice) {
			// それぞれの選択肢のフィールド確認
			$this->QuestionnaireChoice->set($choice);
			$this->QuestionnaireChoice->validates(array(
				'fieldList' => array(
					'matrix_type',
					'other_choice_type',
					'choice_sequence',
					'choice_label',
					'choice_value',
					'skip_page_sequence',
					'questionnaire_question_id',
				)
			));
			if ($this->QuestionnaireChoice->validationErrors) {
				$errors['QuestionnaireChoice'][$cIndex]	= $this->QuestionnaireChoice->validationErrors;
			}
			// 選択肢のシーケンスが０始まりで連番になっているか
			if ($cSeq != $choice['choice_sequence']) {
				$errors['QuestionnaireChoice'][$cIndex][] = __d('questionnaires', 'Invalid choice sequence set. Please try again from the beginning.');
				return false;
			}

			$this->__checkSkip($questionnaire, $page, $question, $choice, $errors['QuestionnaireChoice'][$cIndex]);
			$cSeq++;
		}
		if (!empty($this->validationErrors)) {
			return false;
		} else {
			return true;
		}
	}

/**
 * __checkSkip
 *
 * @param array $questionnaire questionnaire
 * @param array $page questionnaire page
 * @param array $question questionnaire question
 * @param array $choice choice
 * @param array &$errors error message array
 * @return void
 */
	private function __checkSkip($questionnaire, $page, $question, $choice, &$errors) {
		// 質問がスキップ質問である場合
		if ($question['is_skip'] == QuestionnairesComponent::SKIP_FLAGS_SKIP) {
			// 選択肢にジャンプ先の設定があり、かつ、最後ページへの指定ではない場合（未設定時はデフォルトの次ページ移動となります）
			if (!empty($choice['skip_page_sequence']) && $choice['skip_page_sequence'] != QuestionnairesComponent::SKIP_GO_TO_END) {
				// そのジャンプ先は現在ページから戻っていないか
				if ($choice['skip_page_sequence'] < $page['page_sequence']) {
					$errors[] = __d('questionnaires', 'Invalid skip page. Please set forward page.');
				}
				// そのジャンプ先は存在するページシーケンスか
				$page = Hash::extract($questionnaire['QuestionnairePage'], '{n}[page_sequence=' . $choice['skip_page_sequence'] . ']');
				if (!$page) {
					$errors[] = __d('questionnaires', 'Invalid skip page. page does not exist.');
				}
			}
		}
	}
}
