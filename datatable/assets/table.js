$(document).ready(function () {
    $('.m-datatable-row').click(function () {
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            if ($(this.parentNode.parentNode).attr('multiselect') == 'on') {
                $(this).find('td:first-child').find('input').attr('checked', false);
                $(this.parentNode.parentNode).find('th:first-child').find('input').attr('checked', false);
            }
            return;
        }
        if ($(this.parentNode.parentNode).attr('multiselect') == 'off') {
            $('.m-datatable-row', this.parentNode).removeClass('selected');
        }
        $(this).addClass('selected');
        if ($(this.parentNode.parentNode).attr('multiselect') == 'on') {
            $(this).find('td:first-child').find('input').attr('checked', true);
        }
    });

    $('.m-datatable-filters input, .m-datatable-filters select, .m-datatable-filters textarea').change(function () {
        this.form.submit();
    });

    $('.mtable-page-input').change(function () {
        var url = $(this).attr('url-template');
        window.location = url.replace('999999', $(this).val());
    });

    $('.mtable-per-page-select').change(function () {
        $('<form>').attr('method', 'POST')
            .appendTo(this.parentNode)
            .html('<input type="hidden" name="' + $('.m-datatable:first').attr('data-token-key') + '" value="' + $('.m-datatable:first').attr('data-token-key') + '" />'
            +'<input type="hidden" name="' + $(this).attr('name') + '" value="' + $(this).val() + '" />')
            .submit();
    });

    $('.m-datatable table').each(function () {
        if ($(this).attr('multiselect') == 'on'){
            var _table= this;
            $('tr', this).find('th:first').find('input').click(function(){
                if ($(this).attr('checked')){
                    $(_table).find('tr.m-datatable-row').each(function(){
                        $($(this).find('td').first()).find('input').attr('checked', true);
                        $(this).addClass('selected');
                    });
                } else {
                    $(_table).find('tr').each(function(){
                        $($(this).find('td').first()).find('input').attr('checked', false);
                        $(this).removeClass('selected');
                    });
                }
            });
        }
    });
    $('.mdata-table-post-link').click(function(){
        if ($(this).attr('post-confirmation')){
            if (!confirm($(this).attr('post-confirmation'))){
                return false;
            }
        }
        var fields = $.parseJSON($("<div/>").html($(this).attr('post-data')).text());
        var form =$('<form>').appendTo(document.body).attr('method', 'post').attr('action', $(this).attr('href'));
        $('<input>').attr('name', $('.m-datatable:first').attr('data-token-key')).val($('.m-datatable:first').attr('data-token-value')).appendTo(form);
        $.each(fields, function(name, value){
            $('<input>').attr('type', 'hidden').attr('name', name).attr('value', value).appendTo(form);
        });
        form.submit();
        return false;
    });

    $('.m-datatable-multiselect').each(function(){
        var key = $(this).attr('multi-actions-key');
        var _self = this;
        $('.m-datatable-multiactions a', this).click(function(){
            if ($(this).attr('data-confirmation') && (!confirm($(this).attr('data-confirmation')))){
                return false;
            }
            var keys = mDataTable_GetSelected(_self);
            if (keys.length == 0){
                return false;
            }

            mDataTable_SubmitAction(this, keys, key, $(_self).attr('data-token-key'), $(_self).attr('data-token-value'));
            return false;
        });

        $('.m-datatable-multiactions:first a', this).each(function(){
            if ($(this).attr('data-shortcut')){
                var __self = this;
                shortcut.add($(this).attr('data-shortcut'), function(){
                    $(__self).trigger("click");
                }, {
                    type : 'keydown',
                    target : document,
                    propagate : false
                });
            }
        })
    })
});

/**
 * Gets a list of all selected keys.
 * @param element
 * @returns {Array}
 */
function mDataTable_GetSelected(element){
    var ids = new Array();
    $('.m-datatable-row', element).each(function(){
        if ($('td:first input', this).is(':checked')){
            ids[ids.length] = $('td:first input', this).val();
        }
    });
    return ids;
}

/**
 * Submits the selected action.
 * @param actionLink
 * @param keys
 * @param key
 */
function mDataTable_SubmitAction(actionLink, keys, key, csrfKey, csrfValue){
    if ($(actionLink).attr('data-js')){
        var fn = window[$(actionLink).attr('data-js')];
        if (typeof fn === 'function'){
            fn($(actionLink).attr('data-action'), keys, key, csrfKey, csrfValue);
        }
    }
    if ($(actionLink).attr('data-url')){
        var form = $('<form>').attr('method', 'post').attr('action', $(actionLink).attr('data-url')).appendTo(document.body);
        $('<input>').attr('name', 'action').val($(actionLink).attr('data-action')).appendTo(form);
        $('<input>').attr('name', csrfKey).val(csrfValue).appendTo(form);
        for (var i =0; i < keys.length; i++){
            $('<input>').attr('name', key+'[]').val(keys[i]).appendTo(form);
        }
        form.submit();
    }
}