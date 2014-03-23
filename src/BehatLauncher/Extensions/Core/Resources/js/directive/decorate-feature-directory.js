blApp.directive('decorateFeatureDirectory', function () {
    var decorate = function (e) {
        var $row = $(e);
        var $cb = $row.find("> .panel-heading > input");
        var path = $cb.attr('data-path');
        var $exp = $('<a class="expand"><span class="glyphicon glyphicon-chevron-down"></span></a>');
        var $col = $('<a class="collapse"><span class="glyphicon glyphicon-chevron-up"></span></a>')

        $col.hide();

        var $header = $row.find('> div.panel-heading');
        var $rows = $row.find('> div.panel-body');

        $header.prepend($cb);
        $header.append($exp);
        $header.append($col);

        // select/unselect
        $cb.click(function () {
            $row.find('> div.panel-body input[type="checkbox"]').each(function(i, e) {
                var $e = $(e);
                if ($e.prop('checked') != $cb.prop('checked')) {
                    $e.click();
                }
            });
        });

        var collapse = function () {
            $rows.addClass('hide');
            $col.hide();
            $exp.show();
        };

        var expand = function () {
            $rows.removeClass('hide');
            $col.show();
            $exp.hide();
        };

        var toggle = function () {
            if ($rows.is(':visible')) {
                collapse();
            } else {
                expand();
            }
        };

        $exp.click(expand);
        $col.click(collapse);

        // toggle
        $header.find('> label').click(function () {
            toggle();
        })
    };

    return {
        link : function(scope, element, attrs) {
            decorate(element);
        }
    };
});
