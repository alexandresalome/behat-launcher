$(function () {
    $("div.block-features").each(function (i, e) {
        var $cb = $('<input type="checkbox" />');
        var $exp = $('<a class="expand">&nbsp; +</a>');
        var $col = $('<a class="collapse">&nbsp; -</a>')
        var $row = $(e);
        var path = $row.attr('data-path');
        $cb.attr('data-path', path);
        $exp.attr('data-path', path);
        $col.attr('data-path', path);

        $col.hide();

        var $header = $row.find('> div.header');
        var $rows = $row.find('> div.rows');

        $header.prepend($cb);
        $header.append($exp);
        $header.append($col);

        // select/unselect
        $cb.click(function () {
            $(e).find('> div.rows input[type="checkbox"]').prop('checked', $cb.prop('checked'));
        });

        var collapse = function () {
            $rows.slideUp();
            $col.hide();
            $exp.show();
        };

        var expand = function () {
            $rows.slideDown();
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
    });
});
