<?php
namespace common\components;

class DbManager extends \yii\rbac\DbManager
{
    private $_assignments = [];

    public function getAssignments($userId)
    {
        // Avoid multiple queries per request
        if(!isset($this->_assignments[$userId]))
            $this->_assignments[$userId] = parent::getAssignments($userId);
        return $this->_assignments[$userId];
    }

}
