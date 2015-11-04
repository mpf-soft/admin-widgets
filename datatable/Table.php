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

namespace mpf\widgets\datatable;

use mpf\web\AssetsPublisher;
use mpf\web\helpers\Html;
use mpf\widgets\Exception as WidgetException;
use \mpf\web\helpers\Html as HtmlHelper;

class Table extends \mpf\base\Widget {

    /**
     * If there no / it will be considered label
     * @var string
     */
    public $nextPageIcon = '%MPF_ASSETS%images/oxygen/16x16/actions/go-next-view.png';

    /**
     * If there no / it will be considered label
     * @var string
     */
    public $previousPageIcon = '%MPF_ASSETS%images/oxygen/16x16/actions/go-previous-view.png';

    /**
     * If there no / it will be considered label
     * @var string
     */
    public $firstPageIcon = '%MPF_ASSETS%images/oxygen/16x16/actions/go-first-view.png';

    /**
     * If there no / it will be considered label
     * @var string
     */
    public $lastPageIcon = '%MPF_ASSETS%images/oxygen/16x16/actions/go-last-view.png';

    /**
     * Folder name where all plugin assets are found. Is made an attribute
     * so that it can be changed by any class that extends this plugin, or even
     * by config.
     * @var string
     */
    protected $assetsFolder = 'assets';

// ========================== Structure Specific Options ==================

    /**
     * File path for the entire table view.
     * @var string
     */
    public $tableView;

    /**
     * File path for pages view of the table.
     * @var string
     */
    public $pagesView;

// ========================== Style Specific Options ======================

    /**
     * Name of the theme to be used by the current datatable. For now the only
     * theme that is available with the plugin is default. You can create another
     * one using a separate style and set it to use that one.
     * @var string
     */
    public $theme = 'default';

    /**
     * List of extra html options
     * @var string[string]
     */
    public $htmlOptions = array();

// ========================== Data Options ================================

    /**
     * Required! It needs the condition in order to use it to extract the models.
     * @var \mpf\datasources\DataProvider
     */
    public $dataProvider;

    /**
     * List of columns to be displayed for table.
     * It can be a simple list:
     *  array('id', 'name', 'title', 'email', 'age')
     * Or each column can have multiple options:
     *  array(
     *      'id',
     *      'name' => array(
     *          'class' => 'SomeColumnClass', // if namespace it's not specified it will use \mpf\widgets\datatable\columns\ as namespace
     *              //but another can be specified in order to search for custom columns in other locations
     *              // default class is: \mpf\widgets\datatable\columns\Basic
     *          'filter' => false, // can be set to false (and there is no filter), array('key' => 'value') for a select with specified options or can be a string
     *              // with html code for the exact filter, also NULL is the default value and shows a text input
     *          'value' => '$data["column"] . $data["anotherColumn"]' // it can use a php eval to run custom code for value
     *          'htmlOptions' => array(), // a list of HTML options to be used for column
     *          'visible' => true or false, // true to show the column, false to hide it.
     *          'label' => 'Text' //optional use custom label instead of the auto generated one or model one.
     *      )
     *  )
     * @var string[string]
     */
    public $columns;

    /**
     * If set to true then it will add a checkbox at the beginning of the row that allows to check a line. Also some shortcuts to select all/none.
     * @var bool
     */
    public $multiSelect = false;

    /**
     * List of multi select available actions.
     * Structure:
     *      array(
     *          'actionName' => array(
     *              'label' => 'Action Label',
     *              'url' => '', // [optional] if is not set then current URL will be used
     *              'js' => '', // [optional] JS function name to call (parameters: action, ids); If this is set then it won't access the URL, just call this method
     *              'shortcut' => 'Ctrl+C', // [optional] Keyboard shortcut
     *              'confirmation' => 'Are you sure?', // [optional] Add a confirmation message before the action is executed.
     *              'icon' => 'http://domain.com/file/url.jpg'
     *          ),
     *          'secondActionName' => 'Second Action Label' // for simple actions, that don't need shortcut, confirmation, separate URL or js call
     *      )
     * HTML Call POST variables:
     *      array(
     *          'action' => 'actionName',
     *          'ids' => array(1,2,3..) //list of selected IDs
     *      )
     * @var array
     */
    public $multiSelectActions = array();

    /**
     * An expression to be eval() in order to get a custom html class for each row.
     * you can use $row to get info about current row.
     * @var string
     */
    public $rowClass = "";

// =========================== PROTECTED:  ==================================

    /**
     * List of instantiated columns
     * @var \mpf\widgets\datatable\columns\Basic[string]
     */
    protected $columnObjects;

    /**
     * URL to assets folder
     * @var string
     */
    protected $assetsURL;

    public function display() {
        $this->assetsURL = \mpf\web\AssetsPublisher::get()->publishFolder(__DIR__ . DIRECTORY_SEPARATOR . $this->assetsFolder);
        $helper = HtmlHelper::get();
        echo $helper->cssFile($this->getAssetsURL() . 'style.css')
            . $helper->mpfScriptFile('jquery.js')
            . $helper->mpfScriptFile('shortcut.js')
            . $helper->scriptFile($this->getAssetsURL() . 'table.js');
        $this->_loadDefaultViewValues();
        $this->extractData();
        return $this->render($this->tableView);
    }

    /**
     * Return URL to assets folder. Only works after :display() has been called;
     * @return string
     */
    public function getAssetsURL() {
        return $this->assetsURL;
    }

    /**
     * Get extra class for current row
     * @param $row
     * @return string
     */
    protected function getRowClass($row){
        if (!$this->rowClass)
            return "";
        $res = '';
        eval("\$res = {$this->rowClass};");
        return $res;
    }

    /**
     * Render a selected view.
     * @param string $page
     * @param mixed [string] $options
     */
    protected function renderPage($page, $options = array()) {
        $this->render(__DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $page . '.php', $options);
    }

    private function _loadDefaultViewValues() {
        $this->tableView = $this->tableView ? $this->tableView : __DIR__ . '/views/table.php';
        $this->pagesView = $this->pagesView ? $this->pagesView : __DIR__ . '/views/pages.php';
    }

    protected function getRows() {
        return $this->dataProvider->getData();
    }

    /**
     * Loads data from DB using modelCondition and fills $rows and $total attributes
     * with the real values.
     */
    protected function extractData() {
        if (is_null($this->dataProvider) || !is_a($this->dataProvider, '\\mpf\\datasources\\DataProvider'))
            throw new WidgetException("Data Provider was not specified!", WidgetException::CODE_MISSING_CONFIG, null, __CLASS__, 'modelCondition');
        if (is_null($this->columns))
            throw new WidgetException("No Columns were specified!", WidgetException::CODE_MISSING_CONFIG, null, __CLASS__, 'columns');
        $this->dataProvider->getData();
        $this->initColumns();
    }

    protected function initColumns() {
        $this->columnObjects = array();
        foreach ($this->columns as $key => $details) {
            if (is_array($details)) {
                $details['name'] = $key;
                $details['dataProvider'] = $this->dataProvider;
                if (!isset($details['class'])) {
                    $this->columnObjects[$key] = new columns\Basic($details);
                } else {
                    $class = $details['class'];
                    if (strpos($class, '\\') === false)
                        $class = str_replace('Table', '', __CLASS__) . 'columns\\' . $class;
                    unset($details['class']);
                    $this->columnObjects[$key] = new $class($details);
                }
            } else {
                $this->columnObjects[$details] = new columns\Basic(array(
                    'name' => $details,
                    'dataProvider' => $this->dataProvider
                ));
            }
        }
    }

    protected function getColumns() {
        return $this->columnObjects;
    }


    public function getPageLink($pageNo, $type) {
        if (!in_array($type, array('next', 'first', 'previous', 'last'))) {
            trigger_error('Invalid page type ' . $type . '!');
        }
        $icon = $this->{$type . 'PageIcon'};
        if (false === strpos($icon, '/')) {
            $label = $this->translate($icon);
        } else {
            $icon = str_replace('%DATATABLE_ASSETS%', $this->getAssetsURL(), $icon);
            if ('%MPF_ASSETS%' == substr($icon, 0, 12)) {
                $icon = AssetsPublisher::get()->mpfAssetFile(substr($icon, 12));
            }
            $label = Html::get()->image($icon, $this->translate('Page') . ' ' . $pageNo);
        }

        return $this->dataProvider->getLinkForPage($pageNo, $label);
    }

}
