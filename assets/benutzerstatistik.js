(function ($) {

    $('tbody.collapsable tr:first-child td:first-child').live('click', function () {
        $(this).closest('tbody').toggleClass('collapsed');
    });

    $('.toggle-next').live('click', function () {
        $(this).next().slideToggle();
        return false;
    });

    $('.user_statistics tr').live('hover', function (event) {
        $(this).toggleClass('hovered', event.type === 'mouseenter');
    });

    $(function () {
        $('tbody.collapsable td:first-child').click();

        $('.toggle-next.closed').next().hide();

        $('.stats_hover').css('opacity', 0.5).hover(function () {
            $(this).closest('.stats_hover').css('opacity', 1).find(':visible').each(function () {
                $('<div class="scale_connector" style="position: absolute; z-index: 10; border-top: 1px dotted #000;" />')
                    .css('left', $('#scale').next('td').offset().left - 10)
                    .css('width', $(this).offset().left - $('#scale').next('td').offset().left + 10)
                    .css('top', $(this).offset().top)
                    .appendTo('body');
            });
        }, function () {
            $(this).closest('.stats_hover').css('opacity', 0.5);
            $('.scale_connector').remove();
        });

        $('#stat_form select[name=type]').change(function () {
            $('.visits,.hits').toggle();
        })

        $('#stat_form select:not([name=type])').change(function() {
            $(this).closest('form').submit();
        });
    });

}(jQuery));
