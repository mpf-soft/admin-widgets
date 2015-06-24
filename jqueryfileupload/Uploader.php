<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 22.06.2015
 * Time: 13:52
 */

namespace mpf\widgets\jqueryfileupload;

use mpf\base\Widget;
use mpf\web\AssetsPublisher;
use mpf\web\helpers\Form;
use mpf\web\helpers\Html;
use mpf\WebApp;

class Uploader extends Widget {
    /**
     * A method that will be called for each uploaded file.
     * Example:
     *  function ($tempPath, $originalName){
     *         return [
     *              "name" => "Name",
     *              "url" => "",
     *              "thumbnailUrl" => "",
     *              "deleteUrl" => "", // optional, if it's not the same one
     *              "deleteType" => "DELETE" // optional;
     *          ];
     *  }
     * Must return file name or false if for some reason it wasn't valid.
     * @var callback
     */
    public $uploadCallback;

    /**
     * A method that will be called when a request to delete a photo is made;
     * Example:
     *  function ($name) {
     *      return true;
     *  }
     * Must return true|false in case it was deleted or not.
     * @var
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
     * If set to true it will also generate a progress bar.
     * @var bool
     */
    public $generateProgressBar = true;
    /**
     * Id used for the generated progress bar
     * @var string
     */
    public $progressBarId = "upload-progress";

    /**
     * Location of the jQuery-File-Upload code. By default(when using composer) it will be in vendor folder/blueimp/jQuery-FileUpload
     * @var string
     */
    public $jsSource = "{VENDOR}blueimp/jQuery-File-Upload";

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
        $this->handleUploads();
        $this->handleDeletes();
        return true;
    }

    protected function handleUploads() {

    }

    protected function handleDeletes() {

    }

    /**
     * Returns the HTML code for the element.
     * @return string
     */
    public function display() {
        $source = str_replace(['{VENDOR}', '{APP_ROOT}'], [LIBS_FOLDER, APP_ROOT], $this->jsSource);
        $url = AssetsPublisher::get()->publishFolder($source);
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
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                $('<p/>').text(file.name).appendTo($(\"#{$this->resultsId}\"));
            });
        }
    });
});");
        if ($this->generateResultsDiv) {
            $r .= Html::get()->tag("div", "", ["id" => $this->resultsId]);
        }
        if ($this->generateProgressBar) {
            $r .= Html::get()->tag("div", Html::get()->tag("div", "", ["class" => "bar", "style" => "width: 0%;"]), ["id" => $this->progressBarId]);
        }
        return $r;
    }
}