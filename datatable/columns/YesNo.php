<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 19.02.2015
 * Time: 11:32
 */

namespace mpf\widgets\datatable\columns;


class YesNo extends Basic{

    public $filter  = ['No', 'Yes'];

    public $value = '$row->{$this->name}?"Yes":"No"';

}