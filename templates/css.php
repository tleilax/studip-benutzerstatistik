/** TABLE GRID LAYOUT **/
.user-statistics h2 {
 color: #fff !important;
 text-align: center;
}
.user-statistics > div {
    display: inline-block;
    padding-bottom: 1em;
    vertical-align: top;
    width: 49.7%;
    min-width: 400px;
}
.user-statistics th, .user-statistics td {
    text-align: right !important;
}
.user-statistics th:first-child, .user-statistics td:first-child {
    text-align: left !important;
}

/** BROWSERS **/
.browsers tr:first-child td:first-child:before {
    padding-right: 5px;
    vertical-align: top;
}
<?php
    $browsers = array(
        'aol', 'camino', 'chrome', 'dolphin', 'firefox', 'galeon', 'icab',
        'iceweasel', 'konqueror', 'mozilla', 'msie', 'msnexplorer', 'netscape',
        'mobile', 'omniweb', 'opera', 'safari', 'seamonkey', 'shiira',
        'sunrise', 'unbekannt',
    );
    foreach ($browsers as $browser): ?>
.browsers .<?= $browser ?> tr:first-child td:first-child:before {
    content:url(<?= $path ?>browser_<?= $browser ?>.png);
}
<?php endforeach; ?>
.browsers td:first-child {
    padding-left: 20px;
}
.browsers tr:first-child td:first-child {
    padding-left: 0;
}
/** OPERATING SYSTEMS **/
.os td:first-child:before {
    padding-right: 5px;
    vertical-align: top;
}
<?php
    $operating_systems = array(
        'android', 'blackberry', 'ios', 'linux', 'macosx', 'windows',
        'windowsmobile',
    );
    foreach ($operating_systems as $os): ?>
.os .<?= $os ?> td:first-child:before {
    content:url(<?= $path ?>os_<?= $os ?>.png);
}
<?php endforeach; ?>

/** COLLAPSABLE TABLE BODIES **/
tbody.collapsable tr:first-child td:first-child {
    background: url(<?= Assets::image_path('icons/16/blue/arr_1down.png') ?>) left center no-repeat;
    cursor: pointer;
    padding-left: 18px;
    padding-top: 5px;
}
tbody.collapsed tr:first-child td:first-child {
    background-image: url(<?= Assets::image_path('icons/16/blue/arr_1right.png') ?>);
}
tbody.collapsed tr {
    display: none;
}
tbody.collapsed tr:first-child {
    display: table-row;
}

/* OLD STUFF */
.stats_marker {
    float: left;
    margin-top: 0.4em;
    margin-right: 4px;
    border: 1px solid black;
    width: 20px;
    height: 5px;
}
.stats_bar div ,.stats_bar_big div {
    position: absolute;
    left: 1px;
    bottom: 0px;
    border: 1px solid black;
    cursor: default;
    width: 6px;
}
.stats_bar_big div {
    left: 4px;
    width: 10px;
}

.year_marker {
    border: 1px solid black;
    text-align: right;
    overflow: hidden;
    font-size: 0.8em;
    height: 1em;
    line-height: 1em;
    padding: 0px;
    margin-bottom: 1px;
}

.visits .stats_marker, .stats_bar .visits, .stats_bar_big .visits, .year_marker.visits  {
    background-color: green;
}
.visits .stats_marker.headcount, .stats_bar .headcount, .stats_bar_big .headcount, .year_marker.headcount {
    background-color: lightgreen;
}
.hits .stats_marker, .stats_bar .hits, .stats_bar_big .hits, .year_marker.hits {
    background-color: orange;
}

#scale {
    border-right: 1px solid black;
    font-size: 0.7em;
    margin-bottom: -2px;
}

.scale_marker {
    position: absolute;
    right: 0px;
    border-bottom: 1px solid black;
    line-height: 0.9em;
}

.caption {
    border: 1px solid black;
    border-bottom: 0px;
    vertical-align: top;
}
.month_caption {
    border: 1px solid black;
    border-top: 0px;
    border-bottom: 0px;
}

td.grayed {
    color: gray;
}

form fieldset div label {
    display: inline-block;
    text-align: right;
    width: 200px;
}
form fieldset div {
    margin: 1px;
}
form fieldset li label {
    display: inline-block;
    width: 300px;
}
form fieldset li input {
    width: 200px;
}
form fieldset .type-button {
    margin-left: 203px;
}

tr.bordered td {
    border-bottom: 1px solid black;
}
