<?php
/**
 * Created by PhpStorm.
 * User: Mirel Mitache
 * Date: 26.10.2014
 * Time: 13:50
 */

namespace mpf\widgets\datatable\columns;


class Select extends Basic {
    public $value = '$this->filter[$row->{$this->name}]';
} 