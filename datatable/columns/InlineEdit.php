<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 10.03.2015
 * Time: 11:14
 */

namespace mpf\widgets\datatable\columns;

use mpf\web\AssetsPublisher;
use mpf\web\helpers\Html;
use mpf\WebApp;
use mpf\widgets\datatable\Table;
use mpf\web\helpers\Form;


/**
 * Class InlineEdit
 * It will create a
 * @package mpf\widgets\datatable\columns
 */
class InlineEdit extends Basic {

    /**
     * Type of edit. Can have the following values:
     *  - input <- normal text input
     *  - password <- password text input, no default value
     *  - email <- normal text input(some updates for mobile version only)
     *  - date  <- input with calendar
     *  - select <- a dropdown menu to select from options list
     * @var string
     */
    public $type = 'input';

    /**
     * Name of the column that will be used as unique key.
     * @var string
     */
    public $key = 'id';

    /**
     * Used by select type, a list of options that can be selected
     * @var array
     */
    public $options = [];

    /**
     * HTML Options for the generated input field
     * @var array
     */
    public $inputHTMLOptions = [];

    /**
     * List of HTML options that will apply to form
     * @var array
     */
    public $formHTMLOptions = [];

    /**
     * List of HTML options that will apply to span that contains current value
     * @var array
     */
    public $spanHTMLOptions = [];

    /**
     * Icon that will be used while the request is still waiting for an answer
     * @var string
     */
    public $savingIcon = '%MPF_ASSETS%images/oxygen/%SIZE%/animations/process-idle.png';

    /**
     * Icon that will be used in case that there were an error while trying to save the content
     * @var string
     */
    public $errorIcon = '%MPF_ASSETS%images/oxygen/%SIZE%/status/task-reject.png';

    /**
     * Icon that will be used after the save was done successfully.
     * @var
     */
    public $doneIcon = '%MPF_ASSETS%images/oxygen/%SIZE%/status/task-complete.png';

    /**
     * If it's set to true then it will show a cancel button/link. If not then it will hide the edit when focus is lost.
     * @var bool
     */
    public $showCancelButton = true;

    /**
     * String to be used for cancel button. If a cancelButtonIcon is set then this string will be used as title for that image,
     * if not, the full string will be displayed by default as a link.
     * @var string
     */
    public $cancelButtonLabel = 'Cancel';

    /**
     * URL to Cancel button icon. Keywords like %MPF_ASSETS%, %SIZE% are accepted so that MPF assets icons can be used.
     * @var string
     */
    public $cancelButtonIcon = '%MPF_ASSETS%images/oxygen/%SIZE%/actions/dialog-cancel.png';

    /**
     * If it's set to true then it will show a save button next to the input.
     * @var bool
     */
    public $showSaveButton = true;

    /**
     * Label to be used for save button. If an icon is also selected then this label will be used as title.
     * @var string
     */
    public $saveButtonLabel = 'Save';

    /**
     * Icon to be used by save button.
     * @var string
     */
    public $saveButtonIcon = '%MPF_ASSETS%images/oxygen/%SIZE%/actions/dialog-ok-apply.png';

    /**
     * For select types, if nothing is selected
     * @var string
     */
    public $noValueDisplay = "-";

    /**
     * Set this to false if current user can't edit.
     * @var bool
     */
    public $canEdit = true;

    /**
     * Will create an ajax request. Must return a json with the following keys:
     * [
     *   'status' => 'ok', // ok if save done;  'error' if there were problems
     *   'message' => '', // optional - just in case of error, a message to display
     *   'value' => '', // the new value to display
     * ]
     * @var bool
     */
    public $ajax = false;

    /**
     * If not set current URL will be used;
     * @var string
     */
    public $url;


    /**
     * Element for value only; On click on it the form will appear
     * @var string
     */
    public $linkElement = 'a';

    public function init($config) {
        if (!isset($this->htmlOptions['class'])) {
            $this->htmlOptions['class'] = 'inline-edit-column';
        } else {
            $this->htmlOptions['class'] .= ' inline-edit-column';
        }

        parent::init($config);
    }

    /**
     * Returns column value for the specific row
     * @param string [string] $row
     * @param Table $table
     * @return string
     */
    public function getValue($row, Table $table) {
        $res = '';
        if ($this->value) {
            if (is_callable($this->value)){
                $res = call_user_func($this->value, $row, $table);
            } else {
                eval("\$res = {$this->value};");
            }
        } else {
            $res = ('select' == $this->type) ? ((isset($this->options[$row->{$this->name}]) ? $this->options[$row->{$this->name}] : $this->noValueDisplay)) : ($row->{$this->name}?:$this->noValueDisplay);
        }
        return $this->canEdit ? "<{$this->linkElement} class='inline-edit-column-edit-link' href='#'>" . $res . "</{$this->linkElement}>" . $this->getForm($row, $table) : $res;

    }

    protected function getForm($row, Table $table) {
        $this->formHTMLOptions['style'] = (isset($this->formHTMLOptions['style']) ? $this->formHTMLOptions['style'] : '') . 'display:none;';
        $this->formHTMLOptions['method'] = 'post';
        $this->formHTMLOptions['is-ajax'] = ($this->ajax ? '1' : '0');
        $this->formHTMLOptions['action'] = $this->url ? (is_array($this->url) ? WebApp::get()->request()->createURL($this->url[0], $this->url[1], isset($this->url[2]) ? $this->url[2] : [], isset($this->url[3]) ? $this->url[3] : null) : $this->url) : '';
        $form = Form::get()->openForm($this->formHTMLOptions);
        switch ($this->type) {
            case 'input':
            case 'password':
            case 'email':
                $form .= Form::get()->input($this->name, str_replace('input', 'text', $this->type), $row->{$this->name}, $this->inputHTMLOptions);
                break;
            case 'date':
                $form .= Form::get()->input($this->name, str_replace('input', 'date', $this->type), $row->{$this->name}, $this->inputHTMLOptions);
                break;
            case 'select':
                $form .= Form::get()->select($this->name, $this->options, $row->{$this->name}, $this->inputHTMLOptions);
                break;
            case 'default':
                trigger_error("Invalid type {$this->type}!");
                break;
        }
        $form .= Form::get()->hiddenInput($this->key, $row->{$this->key});
        $this->saveButtonIcon = str_replace(array('%DATATABLE_ASSETS%', '%SIZE%'), array($table->getAssetsURL(), ($this->iconSize . 'x' . $this->iconSize)), $this->saveButtonIcon);
        $this->cancelButtonIcon = str_replace(array('%DATATABLE_ASSETS%', '%SIZE%'), array($table->getAssetsURL(), ($this->iconSize . 'x' . $this->iconSize)), $this->cancelButtonIcon);
        if ('%MPF_ASSETS%' == substr($this->saveButtonIcon, 0, 12)) {
            $this->saveButtonIcon = AssetsPublisher::get()->mpfAssetFile(substr($this->saveButtonIcon, 12));
        }
        if ('%MPF_ASSETS%' == substr($this->cancelButtonIcon, 0, 12)) {
            $this->cancelButtonIcon = AssetsPublisher::get()->mpfAssetFile(substr($this->cancelButtonIcon, 12));
        }

        if ($this->showSaveButton) {
            $form .= Form::get()->imageButton($this->saveButtonIcon, $this->saveButtonLabel, '', '', ['class' => 'inline-save-button']);
        }
        if ($this->showCancelButton) {
            $form .= Form::get()->imageButton($this->cancelButtonIcon, $this->cancelButtonLabel, '', '', ['class' => 'inline-cancel-button']);
        }

        return $form . Form::get()->closeForm();
    }

}
