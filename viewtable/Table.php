<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 24.10.2014
 * Time: 16:07
 */

namespace mWidgets\viewtable;


use mpf\base\Widget;
use mpf\web\AssetsPublisher;
use mpf\web\helpers\Html;

class Table extends Widget {

    public $model;

    public $labels;

    public $columns;

    public $theme = 'default';

    public $htmlOptions = [];

    public $tableHtmlOptions = [];

    public function display() {
        $columns = $this->initColumns();
        $t = Html::get()->cssFile(AssetsPublisher::get()->publishFolder(__DIR__ . DIRECTORY_SEPARATOR . 'assets') . 'table.css');
        $content = array();
        foreach ($columns as $column) {
            /* @var $column \mWidgets\viewtable\columns\Basic */
            $content[] = $column->display();
        }
        $content = implode("\n", $content);
        $this->htmlOptions['class'] = 'm-viewtable m-viewtable-' . $this->theme;

        return $t . Html::get()->tag('div', Html::get()->tag('table', $content, $this->tableHtmlOptions), $this->htmlOptions);
    }

    protected function initColumns() {
        $columns = array();
        foreach ($this->columns as $name => $details) {
            if (is_array($details) && isset($details['visible']) && (false == $details['visible'])) {
                continue;
            }
            $name = is_array($details) ? $name : $details;
            $details = is_array($details) ? $details : array();
            $class = isset($details['class']) ? $details['class'] : 'Basic';
            $details['name'] = $name;
            $details['table'] = $this;
            if (false === strpos($class, '\\')) {
                $class = '\\mWidgets\\viewtable\\columns\\' . $class;
            }
            unset($details['class']);
            $columns[$name] = new $class($details);
        }
        return $columns;
    }

    /**
     * Get label for column
     * @param $column
     * @return string
     */
    public function getLabel($column) {
        return isset($this->labels[$column])?$this->labels[$column]:ucwords(str_replace('_', ' ', $column));
    }

} 