<?php

namespace Bigfiche\Model;
       
use Silex\Application;
use RedBeanPHP\Facade as R;
use Bigfiche as Bigfiche;

class Member extends Bigfiche\RedBeanModel {

    /**
     * 
     * Add one record in the table
     * 
     * @param array $params
     * 
     * @return integer or false
     * 
     */
    public function add(array $params, $creationDateFieldName = "created", $activeFieldName = "active") {
        // add tests before add if () {
        // }else{
            return $this->__add($params, $creationDateFieldName, $activeFieldName);
        //}
    }
        
    /**
     * 
     * Update one record in the table
     * 
     * @param integer $id
     * @param array $params
     * 
     * @return boolean
     * 
     */
    public function update($id, array $params, $updateDateFieldName = "updated") {
        // add tests before update if () {
        // }else{
            return $this->__update($id, $params, $updateDateFieldName);
        //}
    }
        
    /**
     * 
     * delete
     * 
     * @param integer $id
     * @return boolean
     */
    public function delete($id) {
        // add tests before delete if () {
        // }else{
            return $this->__delete($id);
        //}
    }
        
}
