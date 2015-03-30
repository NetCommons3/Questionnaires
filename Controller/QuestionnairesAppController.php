<?php

App::uses('AppController', 'Controller', 'Comments.Comment');

class QuestionnairesAppController extends AppController {
    protected function array_merge_recursive_distinct(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach($array2 as $key => &$value) {
            if(is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->array_merge_recursive_distinct($merged[$key], $value, $key);
            }
            else {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }
    protected function questionnaire_array_merge_recursive_distinct(array &$array1, array &$array2)
    {
        $merged = array();

        if(isset($array2['Questionnaire'])) {
            $merged['Questionnaire'] = (isset($array1['Questionnaire'])) ? array_merge($array1['Questionnaire'], $array2['Questionnaire']) : $array2['Questionnaire'];
        }
        else {
            $merged['Questionnaire'] = $array1['Questionnaire'];
        }
        if(isset($array2['QuestionnaireEntity'])) {
            $merged['QuestionnaireEntity'] = (isset($array1['QuestionnaireEntity'])) ? array_merge($array1['QuestionnaireEntity'], $array2['QuestionnaireEntity']): $array2['QuestionnaireEntity'];
        }
        else {
            $merged['QuestionnaireEntity'] = $array1['QuestionnaireEntity'];
        }

        if(isset($array2['QuestionnairePage'])) {
            foreach($array2['QuestionnairePage'] as $key => $page) {
                $orgPage = $this->_getElementById($page, $key, $array1['QuestionnairePage']);
                $merged['QuestionnairePage'][$key] = ($orgPage) ? array_merge($orgPage, $page) : $page;

                if(isset($page['QuestionnaireQuestion'])) {
                    foreach($page['QuestionnaireQuestion'] as $qKey => $question) {
                        $orgQuestion = $this->_getElementById($question, $qKey, $orgPage);
                        $merged['QuestionnairePage'][$key]['QuestionnaireQuestion'][$qKey] = ($orgQuestion) ? array_merge($orgQuestion, $question) : $question;

                        if(isset($question['QuestionnaireChoice'])) {
                            foreach($question['QuestionnaireChoice'] as $cKey => $choice) {
                                $orgChoice = $this->_getElementById($choice, $cKey, $orgQuestion);
                                $merged['QuestionnairePage'][$key]['QuestionnaireQuestion'][$qKey]['QuestionnaireChoice'][$cKey] = ($orgChoice) ? array_merge($orgChoice, $choice) : $choice;
                            }
                        }
                    }
                }
            }
        }
        else {
            $merged['QuestionnairePage'] = array();
        }

        return $merged;

    }
    function _getElementById($el, $index, $heyStack) {
        if(is_array($heyStack)) {
            if(isset($el['id'])) {
                foreach($heyStack as $h) {
                    if(isset($h['id']) && $h['id'] == $el['id']) {
                        return $h;
                    }
                }
            }
            else {
                if(isset($heyStack[$index])) {
                    return $heyStack[$index];
                }
            }
        }
        return null;
    }
    protected function getComments($questionnaire) {
        $c = $this->Comment->getComments(
            array(
                'plugin_key' => 'questionnaires',
                'content_key' => isset($questionnaire['id']) ? $questionnaire['id'] : null,
            )
        );
        $c = $this->camelizeKeyRecursive($c);
        return $c;
    }
    protected function getNowTime() {
        return date('Y-m-d H:i:s');
    }
}
