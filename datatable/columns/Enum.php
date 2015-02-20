<?php
/**
 * Created by PhpStorm.
 * User: Mirel Mitache
 * Date: 25.10.2014
 * Time: 20:24
 */

namespace mpf\widgets\datatable\columns;

/**
 * Class Enum
 * @package mpf\widgets\datatable\columns
 */
class Enum extends Basic{

    /**
     * Table name if columns is from another table
     * @var string
     */
    public $table;

    /**
     * Table Column name if is different than the one of the table.
     * @var string
     */
    public $column;

    /**
     * @param array|\string[] $config
     */
    public function init($config){
        $options = $this->dataProvider->getColumnOptions($this->column?$this->column:$this->name, $this->table);
        foreach ($options as $name){
            $this->filter[$name] = $name;
        }
        return parent::init($config);
    }
} 