<?php

namespace Bigfiche;

use Silex\Application;
use RedBeanPHP\Facade as R;
use RedBeanPHP as RedBeanPHP;

class RedBeanModel implements ModelInterface 
{

    protected $app = null;
    protected $table = null;
    protected $_creationDateFieldName = null;
    protected $_updateDateFieldName = null;
    protected $_activeFieldName = null;
    protected $_deactivationDateFieldName = null;
    
    /**
     * Instantiate a new Model from a table
     * 
     * table is set from the last part of class namespace definition with substracting 'Model'
     *
     * @param Application $app Silex application reference
     * @param string $creationDateFieldName Name of the field used for date of record creation (default = "created", 0 if no field)
     * @param string $updateDateFieldName Name of the field used for date of record update (default = "updated", 0 if no field)
     * @param string $activeFieldName Name of the field used for boolean active record information (default = "active", 0 if no field)
     * @param string $deactivationDateFieldName Name of the field used for date of deactivation (default = "deactivated", 0 if no field)
     *      
     */
    public function __construct(Application $app, $creationDateFieldName = "created", $updateDateFieldName = "updated", $activeFieldName = "active", $deactivationDateFieldName = "deactivated") {
        $this->app = $app;
	$class_explode = explode('\\',get_class($this));
        $this->table = lcfirst($class_explode[count($class_explode,0)-1]);
        if ($creationDateFieldName !== 0) $this->_creationDateFieldName = $creationDateFieldName;
        if ($updateDateFieldName !== 0) $this->_updateDateFieldName = $updateDateFieldName;
        if ($activeFieldName !== 0) $this->_activeFieldName = $activeFieldName;
        if ($deactivationDateFieldName !== 0) $this->_deactivationDateFieldName = $deactivationDateFieldName;
    }

    /**
     * Get the number of records in table
     * 
     * @param string $whereClause SQL where clause add to query
     * 
     * @return integer or boolean
     * 
     */
    public function getCount($whereClause = null) {
        try {
            return R::count($this->table, $whereClause);
        } catch (RedBeanPHP\RedException\SQL $res) {
            new RedBeanPHP\RedException\SQL('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $res);
            return false;
        } catch (Exception $e) {
            new \Exception('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 
     * Get all records of table
     *
     * @param array $targetFields list of fields to retrieve
     * @param array $args list of query clauses (orderby, limit, offset)
     * 
     * @return array
     * 
     */
    public function getAll($targetFields = null, $args = []) {
        try {
            $query = 'SELECT ';
            $query .= implode(', ', is_array($targetFields) ? $targetFields : $this->getColumnList());
            $query .= ' FROM ' . $this->table;
            //$query .= $this->table->getJoinSQL(); // non migré: à voir...
            // order by
            if (isset($args['orderBy'])) {
                $query .= ' ORDER BY ' . $args['orderBy'];
            }
            // Limit
            if (isset($args['limit'])) {
                $query .= ' LIMIT ' . $args['limit'];
                if (isset($args['offset'])) {
                    $query .= ' OFFSET ' . $args['offset'];
                }
            }
            $result = R::getAll($query);
            return count($result)>0 ? $result[0] : false;
        } catch (RedBeanPHP\RedException\SQL $res) {
            new RedBeanPHP\RedException\SQL('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $res);
            return false;
        } catch (Exception $e) {
            new \Exception('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 
     * Get a record from table by id
     * 
     * @param string $id id of record in table
     * @param array $targetFields list of fields to retrieve, if not array retrive all table fields
     *
     * @return array
     * 
     */
    public function getById($id, $targetFields = null) {
        try {
            $query = 'SELECT ';
            $query .= implode(', ', is_array($targetFields) ? $targetFields : $this->getColumnList());
            $query .= ' FROM ' . $this->table;
            $query .= ' WHERE id = ' . $id;
            $result = R::getAll($query);
            return count($result)>0 ? $result[0] : false;
        } catch (RedBeanPHP\RedException\SQL $res) {
            new RedBeanPHP\RedException\SQL('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $res);
            return false;
        } catch (Exception $e) {
            new \Exception('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 
     * Get a set of joined data from one-to-many list
     * 
     * @param array $data source table data to join
     * @param string $targetJoinTable table name to get data
     * @param array $targetFields table name to get data
     *
     * @return array
     * 
     */
    public function getJoinList(array $data, $targetJoinTable, $targetFields = null) {
        try {
            $result = null;
            if (isset($data)) {
                $beanArray = R::convertToBeans($this->table, $data);
                $function = "own" . ucfirst($targetJoinTable) . "List";
                foreach ($beanArray as $id => $bean){
                    $result[$id] = $bean->$function;
                }
            }
            return $result;
        } catch (RedBeanPHP\RedException\SQL $res) {
            new RedBeanPHP\RedException\SQL('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $res);
            return false;
        } catch (Exception $e) {
            new \Exception('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 
     * Add one record in the table
     * 
     * @param array $params
     * @return integer or false
     * 
     */
    public function __add(array $params) {
        if (!is_array($params) || !isset($params['data']) || sizeof($params['data']) < 1) {
            return false;
        }
        try {
            $bean = R::dispense($this->table);
            foreach ($params['data'] as $name => $value) {
                if ($this->isExistColumn($name)) {
                    $bean->$name = $value;
                }
            }
            $creationDateFieldName = $this->_creationDateFieldName;
            $activeFieldName = $this->_activeFieldName;
            if ($creationDateFieldName !== null) $bean->$creationDateFieldName = new \DateTime( 'now' );
            if ($activeFieldName !== null) $bean->$activeFieldName = 1;
            return R::store($bean);
        } catch (RedBeanPHP\RedException\SQL $res) {
            new RedBeanPHP\RedException\SQL('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $res);
            return false;
        } catch (Exception $e) {
            new \Exception('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $e->getMessage());
            return false;
        }
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
    public function __update($id, array $params) {
        if (!is_numeric($id) || !is_array($params) || !isset($params['data']) || sizeof($params['data']) < 1) {
            return false;
        }
        try {
            $bean = R::load($this->table, $id);
            if ($bean->id === 0) return false;
            foreach ($params['data'] as $name => $value) {
                if ($this->isExistColumn($name)) {
                    $bean->$name = $value;
                }
            }
            $updateDateFieldName = $this->_updateDateFieldName;
            if ($updateDateFieldName !== null) $bean->$updateDateFieldName = new \DateTime( 'now' );
            return R::store($bean);
        } catch (RedBeanPHP\RedException\SQL $res) {
            new RedBeanPHP\RedException\SQL('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $res);
            return false;
        } catch (Exception $e) {
            new \Exception('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 
     * delete
     * 
     * @param integer $id
     * @return boolean
     */
    public function __delete($id) {
        if (!is_numeric($id)) {
            return false;
        }
        try {
            $bean = R::load($this->table, $id);
            if ($bean->id === 0) return false;
            R::trash($bean);
            return true;
        } catch (RedBeanPHP\RedException\SQL $res) {
            new RedBeanPHP\RedException\SQL('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $res);
            return false;
        } catch (Exception $e) {
            new \Exception('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 
     * Deactivate one record in the table
     * 
     * @param integer $id
     * 
     * @return boolean
     * 
     */
    public function deactivate($id) {
        if (!is_numeric($id)) {
            return false;
        }
        try {
            $bean = R::load($this->table, $id);
            if ($bean->id === 0) return false;
            $activeFieldName = $this->_activeFieldName;
            $deactivationDateFieldName = $this->_deactivationDateFieldName;
            if($activeFieldName !== null) $bean->$activeFieldName = 0;
            if($deactivationDateFieldName !== null) $bean->$deactivationDateFieldName = new \DateTime( 'now' );
            return R::store($bean);
        } catch (RedBeanPHP\RedException\SQL $res) {
            new RedBeanPHP\RedException\SQL('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $res);
            return false;
        } catch (Exception $e) {
            new \Exception('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 
     * Reactivate one record in the table
     * 
     * @param integer $id
     * 
     * @return boolean
     * 
     */
    public function reactivate($id) {
        if (!is_numeric($id)) {
            return false;
        }
        try {
            $bean = R::load($this->table, $id);
            if ($bean->id === 0) return false;
            $activeFieldName = $this->_activeFieldName;
            $deactivationDateFieldName = $this->_deactivationDateFieldName;
            if($activeFieldName !== null) $bean->$activeFieldName = 1;
            if($deactivationDateFieldName !== null) $bean->$deactivationDateFieldName = null;
            return R::store($bean);
        } catch (RedBeanPHP\RedException\SQL $res) {
            new RedBeanPHP\RedException\SQL('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $res);
            return false;
        } catch (Exception $e) {
            new \Exception('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify if column exist in table
     * 
     * @return boolean
     * 
     */
    public function isExistColumn($columnName) {
        try {
            return isset(R::inspect($this->table)[$columnName]);
        } catch (Exception $e) {
            new \Exception('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $e->getMessage());
            return false;
        }
    }
            
    /**
     * Get the column list of table
     * 
     * @return array
     * 
     */
    public function getColumnList() {
        try {
            return array_keys(R::inspect($this->table));
        } catch (Exception $e) {
            new \Exception('Error ' . __CLASS__ . '::' . __METHOD__ . ' > ' . $e->getMessage());
            return false;
        }
    }
            
}
