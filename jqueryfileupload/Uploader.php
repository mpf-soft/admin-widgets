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

class Uploader extends Widget {

    public $id = 'fileupload';
    public $name = 'files';
    public $dataUrl = '';
    public $resultsId = 'uploaded-files';
    public $generateResultsDiv = true;
    public $generateProgressBar = true;
    public $progressBarId = "upload-progress";

    public $jsSource = "{VENDOR}blueimp/jQuery-File-Upload";

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
        if ($this->generateResultsDiv){
            $r .= Html::get()->tag("div", "", ["id" => $this->resultsId]);
        }
        if ($this->generateProgressBar){
            $r .= Html::get()->tag("div", Html::get()->tag("div", "", ["class" => "bar", "style" => "width: 0%;"]), ["id" => $this->progressBarId]);
        }
        return $r;
    }

    public function handleUpload() {
        return new UploadHandler();
    }

}