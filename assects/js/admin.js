(function($) {
    var App = {
        advanceSelect: function() {
            $('.tpl-field-advance_multi_select select').selectize({
                plugins: ['remove_button', 'drag_drop'],
                cursor: "grabbing",
                item: function(data, escape) {
                    return '<div> <i class="tpl-icon icon-facebook">&#xe801;</i>"' + escape(data.text) + '"</div>';
                }
            });
        }
    }

    App.init = function() {
        this.advanceSelect();
    }

    $(function() {
        App.init();
    });
})(jQuery);