<?php

namespace Bigfiche;

use Silex\Application;

interface ModelInterface
{

    public function __construct(Application $app);
    
    public function getCount($whereClause = null);
    
    public function getAll($targetFields = null, $args = []);
    
    public function getById($id, $targetFields = null);
    
    //public function getBy($field, $value = null, $args = array('operator' => '=', 'targetFields' => null));
            
    public function getJoinList(array $data, $targetJoinTable, $targetFields = null);

    public function _add(array $params);
    
    public function _update($id, array $params);
    
    public function _delete($id);
    
    public function isExistColumn($columnName);
    
    public function getColumnList();
    
}