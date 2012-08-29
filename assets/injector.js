var urls=<?= json_encode(array_flip($urls)) ?>;
$('a[href]:not(.no-track)').live('click',function(){
    var href = $(this).attr('href'),
        id = urls[href],
        url = STUDIP.URLHelper.getURL('plugins.php/benutzerstatistik/tracker/url');
    if (typeof id !== 'undefined') {
        if ($(this).attr('target') === '_blank') {
            window.open(url + '/' + id);
        } else {
            location.href = url + '/' + id;
        }
        return false;
    }
});
