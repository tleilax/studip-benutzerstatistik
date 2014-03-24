var urls=<?= json_encode(array_flip($urls)) ?>;
$('a[href]:not(.no-track)').live('click',function(){
    var href = $(this).attr('href'),
        id = urls[href],
        url;
    if (typeof id !== 'undefined') {
        url = STUDIP.URLHelper.getURL('plugins.php/benutzerstatistik/tracker/url/' + id);
        if ($(this).attr('target') === '_blank') {
            window.open(url);
        } else {
            location.href = url;
        }
        return false;
    }
});
