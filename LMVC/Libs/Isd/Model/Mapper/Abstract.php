<?php

abstract class Isd_Model_Mapper_Abstract
{
    protected $_db;

    public function __construct(Zend_Db_Adapter_Abstract $dbAdapter = null) {
        $this->setDbAdapter($dbAdapter);
    }

    public function setDbAdapter(Zend_Db_Adapter_Abstract $dbAdapter = null) {
        if ($dbAdapter === null) {
            $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        }
        $this->_db = $dbAdapter;
        return $this;
    }
    public function getDbAdapter() {
        if (null === $this->_db) {
            $this->setDbAdapter();
        }
        return $this->_db;
    }

    protected function setLimit(& $q, $start = NULL, $limit = NULL) {
        if ($start !== NULL) {
            $start = (int) $start;
            if ($start < 0) {
                throw new Exception("Query parameter START out of range: ".$start);
            }

            if ($limit !== NULL) {
                $limit = (int) $limit;
                if ($limit < 0) {
                    throw new Exception("Query parameter LIMIT out of range:".$limit);
                }
                $q .= " limit ".$start.",".$limit;
            } else { // uznajemy ze start == 0 oraz limit == $start
                $q .= " limit 0,".$start;
            }
        }

        return $q;
    }

    public abstract function find($id);

    public abstract function save(Isd_Model_Interface $model);
}

?>