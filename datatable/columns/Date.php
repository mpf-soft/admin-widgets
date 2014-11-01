<?php
/**
 * Created by PhpStorm.
 * User: Mirel Mitache
 * Date: 25.10.2014
 * Time: 20:21
 */

namespace mWidgets\datatable\columns;

use mpf\web\helpers\Form;

class Date extends Basic {

    /**
     * @var string
     */
    public $format = 'Y-m-d H:i';

    /**
     * @var string
     */
    public $noValueDisplay = '-';

    /**
     * @return string
     */
    public function getFilter() {
        return Form::get()->date($this->dataProvider->filtersKey . '[' . $this->name . ']');
    }

    /**
     * @param $row
     * @param Table $table
     * @return bool|string
     */
    public function getValue($row, Table $table) {
        if (!$this->value) {
            $value = $row->{$this->name};
            if (!$value) {
                return $this->noValueDisplay;
            }
            return date($this->format, is_numeric($value) ? $value : strtotime($value));
        }
        return parent::getValue($row, $table);
    }
} 