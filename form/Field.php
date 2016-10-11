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

use mpf\web\helpers\Html;

/**
 * Description of Field
 *
 * @author Mirel Mitache
 */
abstract class Field extends \mpf\base\TranslatableObject {

    /**
     * List of options to be used as properties for html input tag.
     * @var string[string]
     */
    public $htmlOptions = array();

    /**
     * List of options to be used as properties for html label
     * @var string[string]
     */
    public $labelHtmlOptions = array();

    /**
     * HTML Class for row div.
     * @var string
     */
    public $rowClass = 'row';

    /**
     * HTML Class for row label
     * @var string
     */
    public $labelClass = 'label';

    /**
     * HTML Class for input
     * @var string
     */
    public $inputClass = 'input';

    /**
     * List of html options for row
     * @var string[string]
     */
    public $rowHtmlOptions = array();

    /**
     * Input name
     * @var string
     */
    public $name;

    /**
     *
     * @var \mpf\widgets\form\Form
     */
    public $form;

    /**
     * Label that will be show for current field. If it's not specified here then it will be searched in Model (if one is attributed)
     * or it will be automatically generated from name.
     * @var string
     */
    public $label;

    /**
     * Default value for this field.
     * @var string
     */
    public $defaultValue = '';

    /**
     * An error message for current field. If nothing is specified here then it will search for errors in model.
     * @var string
     */
    public $error;

    /**
     * @var bool
     */
    public $visible = true;
    
    /**
     * For Special cases when value is manually added( like relations ). This will override everything else(POST, GET)
     * @var string
     */
    public $value = null;

    /**
     * Get label for current field. It will first try to get label attribute, if that's not specified it will check for a model
     * and read the label from there, if it's still not found then it will generate one using the name.
     * @return string
     */
    public function getLabel() {
        if ($this->label) {
            return $this->translate($this->label);
        }
        $labels = array();
        if ($this->form->model && is_string($this->form->model)) {
            $class = $this->form->model;
            $labels = $class::getLabels();
        } elseif ($this->form->model && is_object($this->form->model)) {
            $class = get_class($this->form->model);
            $labels = $class::getLabels();
        }
        if (isset($labels[$this->name])) {
            return $this->translate($labels[$this->name]);
        }
        return $this->translate(ucwords(str_replace('_', ' ', $this->name)));
    }

    /**
     * Get html content for current field.
     * @param \mpf\widgets\form\Form $form
     * @return string
     */
    public function display(Form $form) {
        if (!$this->visible){
            return "";
        }
        $this->form = $form;
        if (!isset($this->rowHtmlOptions['class'])) {
            $this->rowHtmlOptions['class'] = $this->rowClass;
        } else {
            $this->rowHtmlOptions['class'] .= ' ' . $this->rowClass;
        }
        if (!is_null($this->getError())){
            $this->rowHtmlOptions['class'] .= ' has-error';
        }
        return Html::get()->tag('div', $this->getContent(), $this->rowHtmlOptions);
    }

    public function getContent() {
        $this->labelHtmlOptions['class'] = (isset($this->labelHtmlOptions['class']) ? $this->labelHtmlOptions['class'] . ' ' : '') . $this->labelClass;
        return Html::get()->tag('label', $this->getLabel(), $this->labelHtmlOptions)
        . $this->getInput()
        . $this->getHTMLError();
    }

    /**
     * Get HTML code for errors.
     * @return string
     */
    public function getHTMLError() {
        $error = $this->getError();
        if (!is_null($error)) {
            if (is_string($error)) {
                $errorContent = Html::get()->tag('span', $error);
            } elseif (is_array($error)) {
                $errors = array();
                foreach ($error as $message) {
                    $errors[] = Html::get()->tag('li', $message);
                }
                $errorContent = Html::get()->tag('ul', implode("\n", $errors));
            } else {
                $errorContent = '';
            }
            return Html::get()->tag('div', $errorContent, array('class' => 'errors'));
        }
        return '';
    }

    /**
     * Get HTML Code for Input
     * @return string
     */
    abstract function getInput();

    /**
     * Return a simple text message or a list of messages with errors for current field
     * @return string|string[]|null
     */
    public function getError() {
        if ($this->error) {
            return $this->translate($this->error);
        }
        if ($this->form->model && is_object($this->form->model) && $this->form->model->hasErrors($this->name)) {
            $errors = $this->form->model->getErrors($this->name);
            foreach ($errors as &$message) {
                $message = $this->translate($message);
            }
            return $errors;
        }
        return null;
    }

    /**
     * Will try to read value from $_POST or $_GET, if not found then will try to get it from
     * model, if no value it's there either then it will get it from $defaultValue attribute.
     * @return mixed|string|void
     */
    public function getValue() {
        if ($this->value)
            return $this->value;
        $value = $this->getArrayValue($this->form->method == 'POST' ? $_POST : $_GET, $this->getName());
        if ($value) {
            return $value;
        }
        $key = null;
        if (false !== strpos($this->name, '[')){
            $name = explode('[', $this->name, 2);
            $key = substr($name[1], 0, strlen($name[1]) - 1);
            $name = $name[0];
        } else {
            $name = $this->name;
        }
        if ($this->form->model && is_object($this->form->model) && (!$this->form->model->isNewRecord() || $this->form->model->$name)) {
            $val = $this->form->model->$name;
            if (!is_null($key)){
                return isset($val[$key])?$val[$key]:null;
            } else {
                return $val;
            }
        }
        return $this->defaultValue;
    }

    /**
     * Get value from array. It will parse the name and search for [ or ] to read the real value.
     * @param string [string] $source
     * @param string $name
     * @return null|string
     */
    protected function getArrayValue($source, $name) {
        if (false === strpos($name, '[')) {
            return isset($source[$name]) ? $source[$name] : null;
        }
        $name = explode('[', $name, 2);
        return isset($source[$name[0]]) ? $this->getArrayValue($source[$name[0]], substr($name[1], 0, strlen($name[1]) - 1)) : null;
    }

    public function getName() {
        if ($this->form->model) {
            $shortClass = get_class($this->form->model);
            $shortClass = explode('\\', $shortClass);
            $shortClass = $shortClass[count($shortClass) - 1];
            $name = explode('[', $this->name, 2);
            return $shortClass . '[' . $name[0] . ']' . (isset($name[1]) ? '[' . $name[1] : '');
        }
        return $this->name;
    }
}
