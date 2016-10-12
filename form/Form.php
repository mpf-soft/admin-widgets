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

namespace mpf\widgets\form;

use mpf\web\AssetsPublisher;
use mpf\web\helpers\Html;
use \mpf\web\helpers\Html as HtmlHelper;
use \mpf\web\helpers\Form as FormHelper;
use mpf\WebApp;

/**
 * Description of Form
 *
 * @author Mirel Mitache
 */
class Form extends \mpf\base\Widget
{

    /**
     * Name of the form it's used for submit button as it's name.
     * @var string
     */
    public $name;

    /**
     * Folder name where all plugin assests are found. Is made an attribute
     * so that it can be changed by any class that extends this plugin, or even
     * by config.
     * @var string
     */
    protected $assetsFolder = 'assets';

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
    public $htmlOptions = [];

    /**
     * List of extra HTML option specific for Form tag
     * @var string[string]
     */
    public $formHtmlOptions = [];

    /**
     * List of fields with corresponding options.
     * @var string[string]
     */
    public $fields = [];

    /**
     * Details about the submit button. If not define form name will be used as name and a label will be generated
     * automatically using the name.
     * @var string|array
     */
    public $submitButton;

    /**
     * List of extra buttons to show. Each button can have the following options:
     *  - htmlOptions : list of html options and values
     *  - name  :   name of the button
     *  - label  : will be used as value and text to display
     *  - visible  : if set to false button will be hidden
     * Or can be simple string and then  that string will be used as name and a label will be generated from that.
     * @var string[]
     */
    public $buttons = [];

    /**
     * List of htmlOptions for buttons row
     * @var string[string]
     */
    public $buttonsRowHtmlOptions = [];

    /**
     * List of links to be used. They will be displayed on the button row.
     * Example:
     *  [
     *         'Label' => 'http://www.url.here.com',
     *         'Label2' => [
     *              'href' => '#',
     *              'onclick' => 'something_here();',
     *              //... other options
     *          ]
     *  ]
     * @var string[]
     */
    public $links = [];

    /**
     * Name of model class or the exact model object (for edits to get default value )
     * @var string|\mpf\datasources\sql\DbModel
     */
    public $model;

    /**
     * How will it send data to server. Variants: POST,  GET
     * @var string
     */
    public $method = 'POST';

    /**
     * List of hidden inputs and the value for each. Structure: name => value
     * @var array
     */
    public $hiddenInputs = [];

    /**
     * URL to assets folder
     * @var string
     */
    protected $assetsURL;

    /**
     * Form action. If null it will use current page.
     * @var string|array
     */
    public $action;

    public function publishAssets()
    {
        $this->assetsURL = AssetsPublisher::get()->publishFolder(__DIR__ . DIRECTORY_SEPARATOR . $this->assetsFolder);
        echo HtmlHelper::get()->cssFile($this->getAssetsURL() . 'style.css')
            . HtmlHelper::get()->mpfScriptFile('jquery.js')
            . HtmlHelper::get()->scriptFile($this->getAssetsURL() . 'form.js');
    }

    /**
     * Display form.
     */
    public function display()
    {
        $this->publishAssets();
        if (!isset($this->htmlOptions['class'])) {
            $this->htmlOptions['class'] = 'mform mform-' . $this->theme;
        } else {
            $this->htmlOptions['class'] .= 'mform mform-' . $this->theme;
        }
        if ($this->action) {
            $this->formHtmlOptions['action'] = is_string($this->action) ? $this->action : WebApp::get()->request()->createURL($this->action[0],
                isset($this->action[1]) ? $this->action[1] : null,
                isset($this->action[2]) ? $this->action[2] : [],
                isset($this->action[3]) ? $this->action[3] : null);
        }
        $this->formHtmlOptions['method'] = $this->method;
        return HtmlHelper::get()->tag('div', FormHelper::get()->openForm($this->formHtmlOptions) . $this->getContent() . FormHelper::get()->closeForm(), $this->htmlOptions);
    }

    /**
     * @return string
     */
    protected function getContent()
    {

        $hiddenInputs = [];
        foreach ($this->hiddenInputs as $name => $value) {
            $hiddenInputs[] = \mpf\web\helpers\Form::get()->hiddenInput($name, $value);
        }
        $hiddenInputs = implode("\n", $hiddenInputs);

        $fields = array();
        foreach ($this->fields as $field) {
            if (is_string($field)) {
                $field = new fields\Text(array('name' => $field));
                $fields[] = $field->display($this);
            } elseif (is_array($field)) {
                $class = isset($field['type']) ? ucfirst($field['type']) : 'Text';
                $class = (false === strpos($class, '\\')) ? '\\mpf\\widgets\\form\\fields\\' . $class : $class;
                unset($field['type']);
                $field = new $class($field);
                /* @var $field \mpf\widgets\form\Field */
                $fields[] = $field->display($this);
            }
        }

        $buttons = [];

        if ($this->submitButton) {
            $buttons[] = $this->getButton(is_array($this->submitButton) ? $this->submitButton : ['name' => $this->name, 'label' => $this->submitButton]);
        } else {
            $buttons[] = $this->getButton($this->name);
        }
        foreach ($this->buttons as $button) {
            $buttons[] = $this->getButton($button);
        }

        if (!isset($this->buttonsRowHtmlOptions['class'])) {
            $this->buttonsRowHtmlOptions['class'] = 'row-buttons';
        }
        $links = [];
        foreach ($this->links as $label => $link) {
            if (is_array($link)) {
                $links[] = Html::get()->link($link['href'], $label, $link);
            } else {
                $links[] = Html::get()->link($link, $this->translate($label));
            }
        }
        $links = implode(' ', $links);
        return $hiddenInputs . implode("\n", $fields) . HtmlHelper::get()->tag('div', $links . implode("\n", $buttons), $this->buttonsRowHtmlOptions);
    }

    /**
     * Return URL to assets folder. Only works after :display() has been called;
     * @return string
     */
    public function getAssetsURL()
    {
        return $this->assetsURL;
    }

    /**
     * Return html code for a button.
     * @param string|array $details
     * @return string
     */
    public function getButton($details)
    {
        if (is_string($details)) {
            return HtmlHelper::get()->noContentElement('input', array(
                'type' => 'submit',
                'name' => $details,
                'value' => $this->translate(ucwords(str_replace('_', ' ', $details)))
            ));
        }
        if (isset($details['visible']) && $details['visible'] == false) {
            return ''; //return nothing if hidden;
        }
        $htmlOptions = isset($details['htmlOptions']) ? $details['htmlOptions'] : array();
        $htmlOptions['type'] = isset($details['type']) ? $details['type'] : 'submit';
        $htmlOptions['name'] = $details['name'];
        $htmlOptions['value'] = $this->translate($details['label']);
        return HtmlHelper::get()->noContentElement('input', $htmlOptions);
    }

}
