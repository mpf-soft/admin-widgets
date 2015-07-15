<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 22.06.2015
 * Time: 13:52
 */

namespace mpf\widgets\jqueryfileupload;

use mpf\base\Widget;
use mpf\loggers\DevLogger;
use mpf\web\AssetsPublisher;
use mpf\web\helpers\Form;
use mpf\web\helpers\Html;
use mpf\WebApp;

class Uploader extends Widget {
    /**
     * A list of extra options as presented in https://github.com/blueimp/jQuery-File-Upload
     * @var array
     */
    public $options = [];

    /**
     * A method that will be called for each uploaded file.
     * Example:
     *  function ($path, $file){ //file is an object with file details
     *         return $name
     *  }
     *
     * Must return file name or false if for some reason it wasn't valid.
     * @var callable
     */
    public $uploadCallback;

    /**
     * A method that will be called when a request to delete a photo is made;
     * Example:
     *  function ($name) {
     *      return true;
     *  }
     * Must return true|false in case it was deleted or not.
     * @var callable
     */
    public $deleteCallback;

    /**
     * Id of the input used for files;
     * @var string
     */
    public $id = 'fileupload';
    /**
     * Name of the input used for files;
     * @var string
     */
    public $name = 'files';
    /**
     * URL where all the results are processed;
     * By default that URL is the current URL.
     * @var string
     */
    public $dataUrl = '';

    /**
     * ID of the HTML element that holds the results;
     * @var string
     */
    public $resultsId = 'uploaded-files';
    /**
     * If this is set to true then it will also generate the results element, if you create that element in a different location
     * then set this to true and change "resultsId" with your custom id(if different)
     * @var bool
     */
    public $generateResultsDiv = true;

    /**
     * Location of the jQuery-File-Upload code. By default(when using composer) it will be in vendor folder/blueimp/jQuery-FileUpload
     * @var string
     */
    public $jsSource = "{VENDOR}blueimp/jQuery-File-Upload";

    /**
     * Set it to true from the page acessed by js object;
     * @var bool
     */
    public $handleRequest = false;

    /**
     * Path to upload folder
     * @var string
     */
    public $uploadDir;

    /**
     * URL to upload folder
     * @var string
     */
    public $uploadURL;

    /**
     * Class of handler used for uploads.
     * @var string
     */
    public $uploadHandlerClass = '\mpf\widgets\jqueryfileupload\Handler';

    /**
     * Method names for each event to be handled;
     * Params for each event method (e, data)
     * @url https://github.com/blueimp/jQuery-File-Upload
     * Possible events:
     * fileuploadadd
     * fileuploadprocessalways
     * fileuploadprogressall
     * fileuploaddone
     * fileuploadfail
     */
    public $jsEventsHandlers = [];


    /**
     * It will take care of upload and delete requests;
     * @param array $config
     * @return bool
     * @throws \Exception
     */
    protected function init($config = []) {
        parent::init($config);
        if (!$this->dataUrl) {
            $this->dataUrl = WebApp::get()->request()->getCurrentURL(); // if dataUrl is not set then current URL will be used;
        }
        if ($this->handleRequest) {
            $this->_handle();
            die();
        }

        return true;
    }

    protected function _handle() {
        DevLogger::$ignoreOutput = true;
        $handler = $this->uploadHandlerClass;
        $handler = new $handler(['uploader' => $this, 'options' => $this->options]);
        /* @var $handler Handler */
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'OPTIONS':
            case 'HEAD':
                $handler->head();
                break;
            case 'GET':
                $handler->get();
                break;
            case 'PATCH':
            case 'PUT':
            case 'POST':
                $handler->post();
                break;
            case 'DELETE':
                $handler->delete();
                break;
            default:
                header('HTTP/1.1 405 Method Not Allowed');
        }
        die();
    }

    /**
     * JS code to add events;
     * @return string
     */
    protected function getJSEvents(){
        $final = "";
        foreach ($this->jsEventsHandlers as $event=>$method){
            $final .= ".on('$event', function(e, data) { $method(e, data); })";
        }
        return $final;
    }

    /**
     * Returns the HTML code for the element.
     * @return string
     */
    public function display() {
        $source = str_replace(['{VENDOR}', '{APP_ROOT}'], [LIBS_FOLDER, APP_ROOT], $this->jsSource);
        $url = AssetsPublisher::get()->publishFolder($source);
        $events = $this->getJSEvents();
        $r = Form::get()->input($this->name . '[]', 'file', null, [
                'id' => $this->id,
                'data-url' => $this->dataUrl,
                'multiple' => 'multiple',
            ])
            . Html::get()->scriptFile($url . "js/vendor/jquery.ui.widget.js")
            . Html::get()->scriptFile($url . "js/jquery.iframe-transport.js")
            . Html::get()->scriptFile($url . "js/jquery.fileupload.js")
            . Html::get()->script("\$(function () {
    \$('#{$this->id}').fileupload({
        dataType: 'json' " . (isset($this->jsEventsHandlers['fileuploaddone'])?'': ",
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                $('<p/>').text(file.name).appendTo($(\"#{$this->resultsId}\"));
            });
        }") . "
    })$events;
});");
        if ($this->generateResultsDiv) {
            $r .= Html::get()->tag("div", "", ["id" => $this->resultsId]);
        }
        return $r;
    }
}