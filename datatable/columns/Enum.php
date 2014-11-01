<?php
/**
 * Created by PhpStorm.
 * User: Mirel Mitache
 * Date: 25.10.2014
 * Time: 20:24
 */

namespace mWidgets\datatable\columns;

/**
 * Class Enum
 * @package mWidgets\datatable\columns
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
        $options = $this->dataProvider->getColumnOptions($this->column?$this->name:$this->column, $this->table);
        foreach ($options as $name){
            $this->filter[$name] = $name;
        }
        return parent::init($config);
    }
} 