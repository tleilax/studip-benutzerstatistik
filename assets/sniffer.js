var url = STUDIP.URLHelper.getURL('plugins.php/benutzerstatistik/tracker/sniff');
url += '/' + parseInt(screen.width, 10);
url += '/' + parseInt(screen.height, 10);
$.get(url);