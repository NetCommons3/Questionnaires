<?php

App::uses('AppModel', 'Model');

class QuestionnairesAppModel extends AppModel {

    /**
     * use behaviors
     *
     * @var array
     */
    public $actsAs = array(
        'NetCommons.Trackable',
        'NetCommons.Publishable'
    );
}
