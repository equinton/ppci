<?php
namespace Ppci\Models;

use CodeIgniter\Database\Query;
use CodeIgniter\Model;

class PpciModel extends Model
{
    protected array $fields;
    protected array $numericFields;
    protected array $dateFields;
    protected array $datetimeFields;
    protected array $geomFields;
    protected array $defaultValues;
    protected string $parentKeyName;
    /**
     * If true, the date and datetime fields are formated into the locale language, and reverse to write into the database
     *
     * @var boolean
     */
    public bool $autoFormatDate = true;
    public string $dateFormat = 'd/m/Y';
    public string $datetimeFormat = 'd/m/Y h:i:s';
    /**
     * If true, for numbers, the comma is transformed in point before write in database
     *
     * @var boolean
     */
    public $transformComma = true;
    /**
     * SRID used to generate geom fields
     *
     * @var integer
     */
    public int $srid = 4326;
    /**
     * character used to surround the column names, if necessary
     *
     * @var string
     */
    public $qi = '"';
    public $message;
    /**
     * Populate data for use with model
     * Define the date, datetime and geom types
     * Define the defaults values if a record don't exists
     *
     * @return void
     */
    protected function initialize()
    {
        foreach ($this->fields as $fieldName => $field) {
            $this->allowedFields[] = $fieldName;
            if ($field["type"] == 1) {
                $this->numericFields[] = $fieldName;
            } elseif ($field["type"] == 2) {
                $this->dateFields[] = $fieldName;
            } elseif ($field["type"] == 3) {
                $this->datetimeFields[] = $fieldName;
            } elseif ($field["type"] == 4) {
                $this->geomFields[] = $fieldName;
            }
            if ($field["key"] == 1) {
                $this->primaryKey = $fieldName;
            }
            if (isset($field["defaultValue"])) {
                $this->defaultValues[$fieldName] = $field["defaultValue"];
            }
            if (isset($field["parentAttrib"])) {
                $this->parentKeyName = $field["parentAttrib"];
            }
        }
        /**
         * Add messages to user and syslog
         */
        $this->message = service('MessagePpci');
    }
    /**
     * Execute the requests to the database, with generate error exceptions
     *
     * @param string $sql
     * @param array|null $data
     * @return 
     */
    protected function executeQuery(string $sql, array $data = null)
    {
        if (isset($data)) {
            $query = $this->db->query($sql, $data);
        } else {
            $query = $this->db->query($sql);
        }
        if ($query->hasError()) {
            $this->message->set($query->getErrorMessage(), true);
            $this->message->setSyslog($query->getErrorMessage());
            throw new \Exception($query->getErrorMessage(), $query->getErrorCode());
        } else {
            return $query;
        }
    }

    /*******************
     * Write functions *
     ******************/

    /**
     * Write a row into the database
     *
     * @param array $row
     * @return integer
     */
    public function save(array $row): int
    {
        $isInsert = false;
        if ($row[$this->primaryKey] == 0) {
            unset($row[$this->primaryKey]);
            $isInsert = true;
        } else {
            $id = $row[$this->primaryKey];
        }
        if ($this->autoFormatDate) {
            $row = $this->formatDatesToDB($row);
        }
        if ($this->transformComma) {
            foreach ($this->numericFields as $field) {
                $row[$field] = str_replace(",", ".", $row[$field]);
            }
        }
        if (parent::save($row) && $isInsert) {
            $id = $this->getInsertID();
        }
        return $id;
    }
    public function ecrire(array $row): int
    {
        return $this->save($row);
    }

    /**
     * update a table which contains only 2 fields,
     * each field is a parent key (tables n-n)
     *
     * @param string $tablename : the name of the table to update
     * @param string $firstKey : main key name
     * @param string $secondKey : key of the related table from main key name
     * @param integer $id : value of the main key
     * @param array $data : array which contains all values of the secondary key
     * @return void
     */
    function writeTableNN(string $tablename, string $firstKey, string $secondKey, int $id, $data = array()): void
    {
        if (!$id > 0) {
            throw new \Exception(sprintf(_("La clé principale %s n'est pas renseignée ou vaut zéro"), $firstKey));
        }
        foreach ($data as $value) {
            if (!is_numeric($value)) {
                throw new \Exception(sprintf(_("Une valeur fournie n'est pas numérique (%s)"), $value));
            }
        }
        $tablename = $this->qi . $tablename . $this->qi;
        $k1 = $this->qi . $firstKey . $this->qi;
        $k2 = $this->qi . $secondKey . $this->qi;
        /** 
         * get the current content of the table
         */
        $sql = "select " . $k2 . " from " . $tablename . " where " . $k1 . " = :id";
        $origin = array();
        $query = $this->executeQuery($sql, ["id" => $id]);
        foreach ($query->getResultArray() as $row) {
            $origin[] = $row[$secondKey];
        }

        /**
         * Get the values presents in the two arrays
         */
        $intersect = array_intersect($origin, $data);
        $delete = array_diff($origin, $intersect);
        $create = array_diff($data, $intersect);
        $param = array("id" => $id);
        if (count($delete) > 0) {
            $sql = "delete from " . $tablename . " where " . $k1 . " = :id and " . $k2 . "= :key2";
            foreach ($delete as $key2) {
                $param["key2"] = $key2;
                $this->executeQuery($sql, $param);
            }
        }
        if (count($create) > 0) {
            $sql = "insert into " . $tablename . "(" . $k1 . "," . $k2 . ") values ( :key1, :key2)";
            foreach ($create as $key2) {
                $param["key2"] = $key2;
                $this->executeQuery($sql, $param);
            }
        }
    }
    function ecrireTableNN(string $tablename, string $firstKey, string $secondKey, int $id, $data = array()): void
    {
        $this->writeTableNN($tablename, $firstKey, $secondKey, $id, $data);
    }
    /******************
     * Read functions *
     ******************/

    /**
     * Read a row, and return the data formatted
     * If the id is 0, return the default values
     *
     * @param integer $id
     * @param boolean $getDefault
     * @param integer $parentKey
     * @return array
     */
    public function lire(int $id, bool $getDefault = true, $parentKey = 0): array
    {
        if ($id == 0) {
            $data = $this->getDefaultValues($parentKey);
        } else {
            $data = $this->find($id);
            if (empty($data)) {
                $data = $this->getDefaultValues($parentKey);
            }
        }
        return $data;
    }
    public function read(int $id, bool $getDefault = true, $parentKey = 0): array
    {
        return $this->lire($id, $getDefault, $parentKey);
    }

    /**
     * Get the default values for a record, if not exists
     *
     * @param integer $parentKey
     * @return array
     */
    public function getDefaultValues($parentKey = 0): array
    {
        $data = array();
        foreach ($this->defaultValues as $k => $v) {
            if (is_callable($v)) {
                $data[$k] = $this->{$v}();
            } else {
                $data[$k] = $v;
            }
        }
        /**
         * Search for parent key
         */
        if ($parentKey > 0) {
            $data[$this->parentKeyName] = $parentKey;
        }
        if ($this->autoFormatDate) {
            $data = $this->formatDatesToLocale($data);
        }
        return $data;
    }

    /**
     * Get the formated list of the datatable
     *
     * @param string $order
     * @return array
     */
    public function getListe($order = ""): array
    {
        $result = array();
        if (empty($order)) {
            $data = $this->findAll();
        } else {
            $sql = "select * from " . $this->qi . $this->table . $this->qi . " order by $order";
            $query = $this->db->query($sql);
            $data = $query->getResult("array");
        }
        if ($this->autoFormatDate) {
            foreach ($data as $row) {
                $result[] = $this->formatDatesToLocale($row);
            }
        } else {
            $result = $data;
        }
        return $result;
    }
    function getList($order = ""): array
    {
        return $this->getListe($order);
    }

    function getListFromParent(int $parentId, $order = "")
    {
        if ($parentId > 0 && !empty($this->parentKeyName)) {
            $sql = "select * from " . $this->qi . $this->table . $this->qi;


        }
    }

    /*******************
     * Dates functions *
     *******************/

    /**
     * Specify the locale format to use for dates fields
     *
     * @param string $dateFormat
     * @return void
     */
    public function setDateFormat(string $dateFormat)
    {
        $this->dateFormat = $dateFormat;
        $this->datetimeFormat = $dateFormat . " h:i:s";
    }
    /**
     * Get a record or, if not exists, get the default values
     *
     * @param integer $id
     * @param boolean $getDefault
     * @param integer $parentKey
     * @return array
     */
    /**
     * Format all date and datetime columns of a row in locale
     *
     * @param array $row
     * @return array
     */
    protected function formatDatesToLocale(array $row): array
    {
        foreach ($this->dateFields as $field) {
            if (!empty($row[$field])) {
                $date = date_create_from_format("Y-m-d h:i:s", $row[$field]);
                $row[$field] = date_format($date, $this->dateFormat);
            }
        }
        foreach ($this->datetimeFields as $field) {
            if (!empty($row[$field])) {
                $date = date_create_from_format("Y-m-d h:i:s", $row[$field]);
                $row[$field] = date_format($date, $this->datetimeFormat);
            }
        }
        return $row;
    }
    /**
     * Format all date and datetime columns from locale to database format
     *
     * @param array $row
     * @return array
     */
    protected function formatDatesToDB(array $row): array
    {
        foreach ($this->dateFields as $field) {
            if (!empty($row[$field])) {
                $date = date_create_from_format($this->dateFormat, $row[$field]);
                $row[$field] = date_format($date, "Y-m-d");
            }
        }
        foreach ($this->datetimeFields as $field) {
            if (!empty($row[$field])) {
                $date = date_create_from_format($this->datetimeFormat, $row[$field]);
                $row[$field] = date_format($date, "Y-m-d h:i:s");
            }
        }
        return $row;
    }

    /**************************
     * Micellaneous functions *
     **************************/
    function getUUID(): string
    {
        $sql = "select gen_random_uuid() as uuid";
        $query = $this->executeQuery($sql);
        $result = $query->getFirstRow("array");
        return $result["uuid"];
    }

    function getDateTime(): string
    {
        return date($this->datetimeFormat);
    }
    function getDateHeure(): string
    {
        return date($this->datetimeFormat);
    }
    function getDate(): string
    {
        return (date($this->dateFormat));
    }
    function getDateJour(): string
    {
        return (date($this->dateFormat));
    }


}