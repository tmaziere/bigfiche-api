<?php

namespace Bigfiche;

use Silex\Application;

interface ModelInterface
{

    public function __construct(Application $app, $creationDateFieldName, $updateDateFieldName, $activeFieldName, $deactivationDateFieldName);
    
    public function getCount($whereClause = null);
    
    public function getAll($targetFields = null, $args = []);
    
    public function getById($id, $targetFields = null);
    
    //public function getBy($field, $value = null, $args = array('operator' => '=', 'targetFields' => null));
            
    public function getJoinList(array $data, $targetJoinTable, $targetFields = null);

    public function __add(array $params);
    
    public function __update($id, array $params);
    
    public function __delete($id);
    
    public function deactivate($id);

    public function reactivate($id);

    public function isExistColumn($columnName);
    
    public function getColumnList();
    
}