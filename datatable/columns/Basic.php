<?php

/*
 * @author Mirel Nicu Mitache <mirel.mitache@gmail.com>
 * @package MPF Framework
 * @link    http://www.mpfframework.com
 * @category core package
 * @version 1.0
 * @since MPF Framework Version 1.0
 * @copyright Copyright &copy; 2011 Mirel Mitache 
 * @license  http://www.mpfframework.com/licence
 * 
 * This file is part of MPF Framework.
 *
 * MPF Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * MPF Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MPF Framework.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace mpf\widgets\datatable\columns;

use mpf\web\AssetsPublisher;
use mpf\web\helpers\Form;
use mpf\web\helpers\Html;
use mpf\widgets\datatable\Table;

/**
 * Description of Text
 *
 * @author mirel
 */
class Basic extends \mpf\base\Object
{

    /**
     * Name of the model. Sent by Table widget.
     * @var \mpf\datasources\DataProvider
     */
    public $dataProvider;

    /**
     * Column name
     * @var string
     */
    public $name;

    /**
     * A filter to be used, can be false for no filter, null for default text input
     * or an actuall HTML Code with that can be filled in the column.
     * @var mixed
     */
    public $filter;

    /**
     * Can be a php code to be evaluated by "eval()" method
     * @var string
     */
    public $value;

    /**
     * List of html options for current column
     * @var string[string]
     */
    public $htmlOptions = array();

    /**
     * List of html options for header column
     * @var string[string]
     */
    public $headerHtmlOptions = array();

    /**
     * List of html options for filter column
     * @var string[string]
     */
    public $filterHtmlOptions = array();

    /**
     * Show or hide current column
     * @var boolean
     */
    public $visible = true;

    /**
     * Column label to be used in header. If none is selected it will search in Model labels.
     * @var string
     */
    public $label;

    /**
     * Text to be shown when there is no value available
     * @var string
     */
    public $noValueText = '&nbsp;';

    /**
     * Set it to false to not allow order or to string to specifiy custom order column name.
     * @var boolean|string
     */
    public $order = true;

    /**
     * Arrow up icon used for order
     * @var string
     */
    public $iconArrowUp = '%MPF_ASSETS%images/oxygen/%SIZE%/actions/arrow-up.png';

    /**
     * Arrow down icon used for order.
     * @var string
     */
    public $iconArrowDown = '%MPF_ASSETS%images/oxygen/%SIZE%/actions/arrow-down.png';

    /**
     * Icons size in PX.
     * @var int
     */
    public $iconSize = 16;

    /**
     * Return true if column is visible, false if not.
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * Return string to be used in the header of the table for current column.
     * If none is defined it will search in Model::getLabels() if none is found
     * there it will generate one using the name of the column.
     * @return string
     */
    public function getLabel()
    {
        if ($this->label)
            return $this->label;
        return $this->dataProvider->getLabel($this->name);
    }

    /**
     * Returns column value for the specific row
     * @param string [string] $row
     * @param Table $table
     * @return string
     */
    public function getValue($row, Table $table)
    {
        if (!$this->value)
            return $row->{$this->name};
        $res = '';
        eval("\$res = {$this->value};");
        return $res;
    }

    /**
     * Get HTML table filter for selected column;
     * @return string
     */
    public function getFilter()
    {
        if (false === $this->filter) {
            return '';
        }
        if (is_array($this->filter)) {
            return Form::get()->select($this->dataProvider->filtersKey . '[' . $this->name . ']', $this->filter, null, array(), '');
        }
        return Form::get()->input($this->dataProvider->filtersKey . '[' . $this->name . ']');
    }

    /**
     * Return html options for header;
     * @return string
     */
    public function getHeaderHtmlOptions()
    {
        $r = '';
        foreach ($this->headerHtmlOptions as $k => $v)
            $r .= "$k = '$v' ";
        return $r;
    }

    /**
     * Return html options for filter;
     * @return string
     */
    public function getFilterHtmlOptions()
    {
        $r = '';
        foreach ($this->filterHtmlOptions as $k => $v)
            $r .= "$k = '$v' ";
        return $r;
    }

    /**
     * Return html options for cell;
     * @return string
     */
    public function getHtmlOptions()
    {
        $r = '';
        foreach ($this->htmlOptions as $k => $v)
            $r .= "$k = '$v' ";
        return $r;
    }

    /**
     * Get HTML code for header
     * @param Table $table
     * @return string
     */
    public function getHeaderCode(Table $table)
    {
        $label = $this->getLabel();
        if (!$this->order) {
            return $label;
        }
        $order = $this->dataProvider->getOrder();
        $prefix = '';

        $this->iconArrowUp = str_replace(array('%DATATABLE_ASSETS%', '%SIZE%'), array($table->getAssetsURL(), ($this->iconSize . 'x' . $this->iconSize)), $this->iconArrowUp);
        $this->iconArrowDown = str_replace(array('%DATATABLE_ASSETS%', '%SIZE%'), array($table->getAssetsURL(), ($this->iconSize . 'x' . $this->iconSize)), $this->iconArrowDown);
        if ('%MPF_ASSETS%' == substr($this->iconArrowUp, 0, 12)) {
            $this->iconArrowUp = AssetsPublisher::get()->mpfAssetFile(substr($this->iconArrowUp, 12));
        }
        if ('%MPF_ASSETS%' == substr($this->iconArrowDown, 0, 12)) {
            $this->iconArrowDown = AssetsPublisher::get()->mpfAssetFile(substr($this->iconArrowDown, 12));
        }
        if ($order[0] == $this->name) {
            $prefix = ('ASC' == $order[1]) ? Html::get()->image($this->iconArrowUp, 'Order Descendent', ['class' => 'order-by-img']) : Html::get()->image($this->iconArrowDown, 'Order Ascendent', ['class' => 'order-by-img']);
        }
        return $this->dataProvider->getColumnOrderLink($this->order ?: '`' . $this->name . '`', $prefix . $label);
    }

}
