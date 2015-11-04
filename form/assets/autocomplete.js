/**
 * Created by mirel on 18.08.2015.
 */

var MPFForm_AutoComplete = {
    /**
     * Call this method for each autocomplete element
     * @param element
     */
    init: function (element) {
        var enterPressed = false;
        var _self = this;
        $(element).focus(function(){
            if ($('.form-autocomplete-list', this.parentNode).find('li').length) { // only if there are any options available
                $('.form-autocomplete-list', this.parentNode)
                    .css('top', $(this).position().top + $(this).height() + 5 + 'px')
                    .css('width', $(this).width() + 'px')
                    .fadeIn();
            }
        }).blur(function(){
            $('.form-autocomplete-list', this.parentNode).fadeOut();
        }).keyup(function(){
            if (($(this).attr('autc_ajax') == '1') && ($(this).val().length >= $(this).attr('autc_minletters'))){
                // ajax search
            } else {
                if (!$('.form-autocomplete-list', this.parentNode).visible()){
                    $('.form-autocomplete-list', this.parentNode).fadeIn();
                }
                _self.filterList(this.value, $('.form-autocomplete-list', this.parentNode));
            }
        }).keydown(function(e){
            if (e.which == 40){ //down
                if (enterPressed && $('.form-autocomplete-list', this.parentNode).find('li').length){
                    $('.form-autocomplete-list', this.parentNode).fadeIn();
                    enterPressed = false;
                }
                _self.moveDown($('.form-autocomplete-list', this.parentNode));
            } else if (e.which == 38){ //up
                if (enterPressed && $('.form-autocomplete-list', this.parentNode).find('li').length){
                    $('.form-autocomplete-list', this.parentNode).fadeIn();
                    enterPressed = false;
                }
                _self.moveUp($('.form-autocomplete-list', this.parentNode));
            } else if (e.which == 13) {
                $(this).val($('.form-autocomplete-list', this.parentNode).find('li.selected').text());
                $('#' + $(this).attr('autc_for')).val($('.form-autocomplete-list', this.parentNode).find('li.selected').text());
                $('.form-autocomplete-list', this.parentNode).fadeOut();
                enterPressed = true;
                return false;
            } else if (e.which == 27){
                $('.form-autocomplete-list', this.parentNode).fadeOut();
            }
        });
        $('.form-autocomplete-list li', element.parentNode).hover(function(){
            $('li',this.parentNode).removeClass('selected');
            $(this).addClass('selected');
        }).click(function(){
            $(_self).val($(this).text());
            $('#' + $(_self).attr('autc_for')).val($(this).text());
            $('.form-autocomplete-list', _self.parentNode).fadeOut();
            enterPressed = true;
            return false;
        })
    },
    checkScroll: function (list) {
        if ($('li.selected', list).position().top + $('list.selected', list).height() > $(list).height()) {
            $(list).scroll($('li.selected', list).position().top + $('list.selected', list).height());
        }
    },

    moveUp: function (list) {
        var all = $('li', list);
        if (all.length == 0) {
            return;//no action if there are no items
        }
        if (!$('li.selected', list).length) {
            $(all[all.length - 1]).addClass('selected');
            this.checkScroll(list);
            return;//if none are selected then select the last one
        }

        for (var i = 0; i < all.length; i++) {
            if ($(all[i]).hasClass('selected')) {
                if (i != 0) {
                    $(all[i - 1]).addClass('selected');
                    $(all[i]).removeClass('selected');
                } else {
                    $(all[i]).removeClass('selected');
                    $(all[all.length - 1]).addClass('selected');
                }
                break;
            }
        }
        this.checkScroll(list);
    },
    moveDown: function (list) {
        var all = $('li:not(.hidden)', list);
        if (all.length == 0) {
            return;// no action if there are no items;
        }
        if (!$('li.selected', list).length) {
            $(all[0]).addClass('selected');
            this.checkScroll(list);
            return;//if none are selected then select the first one
        }
        for (var i = 0; i < all.length; i++) {
            if ($(all[i]).hasClass('selected')) {
                if (i != all.length - 1) {
                    $(all[i + 1]).addClass('selected');
                    $(all[i]).removeClass('selected');
                } else {
                    $(all[i]).removeClass('selected');
                    $(all[0]).addClass('selected');
                }
                break;
            }
        }
        this.checkScroll(list);
    },

    filterList: function (text, list) {
        if (text.length == 0) {
            $('li', list).removeClass('hidden');
            return;
        }
        $('li', list).each(function () {
            if ($(this).text().toLowerCase().indexOf(text.toLowerCase()) == -1) {
                $(this).addClass('hidden');
            } else {
                $(this).removeClass('hidden');
            }
        })
    }
};

$.fn.autocomplete = function(){
    this.each(function(){
        MPFForm_AutoComplete.init(this);
    });
};