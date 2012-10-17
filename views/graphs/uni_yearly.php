<?
$width = function($month, $role, $type, $total = null) use ($months)
{
    if ($total === null) {
        $total = $months[$month]['total'][$type];
    }
    return round(min(100, $months[$month][$role][$type] / $total * 100), 2);
}
?>

<table class="default user-stats">
    <colgroup>
        <col width="10%">
        <col width="24%">
        <col span="2" width="33%">
    </colgroup>
    <thead>
        <tr>
            <th colspan="4">
                <form action="<?= $controller->url_for('graphs/uni_yearly') ?>" method="get" id="stat_form" style="float: right;">
                    <select name="year" onchange="$(this).closest('form').submit();">
                    <? foreach ($years as $name): ?>
                        <option value="<?= $name ?>" <? if ($name == $year) echo 'selected'; ?>>
                            <?=$name?> / <?= $name+1 ?>
                        </option>
                    <? endforeach; ?>
                    </select>

                    <noscript>
                        <?= makebutton('anzeigen', 'input') ?>
                    </noscript>
                </form>

                <?= _('Zeitraum') ?>: <?= date('d.m.Y', $start_date) ?>
                <?= _('bis') ?> <?= date('d.m.Y', $end_date) ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr class="bordered">
            <td colspan="2">
                <div class="visits">
                    <div class="stats_marker">&nbsp;</div>
                    <?= _('Besucher') ?>: <?= numberformat($totals['visits']) ?>
                </div>
                <div class="visits">
                    <div class="stats_marker headcount">&nbsp;</div>
                    <?= _('Kopfzahl') ?>: <?= numberformat($totals['uniquevisits']) ?>
                    <br>
                </div>
            <? if ($show_hits): ?>
                <div class="hits">
                    <div class="stats_marker">&nbsp;</div>
                    <?= _('Seitenaufrufe') ?>: <?= numberformat($totals['hits']) ?>
                </div>
            <? endif; ?>
            </td>
            <td>
            <? foreach ($areas as $area): ?>
                &Oslash;/<?= _('Monat') ?>: <?= numberformat($average[$area], 2) ?><br>
            <? endforeach; ?>
            </td>
            <td>
            <? foreach ($areas as $area): ?>
                <?=_('Maximum')?>: <?= numberformat($max[$area]) ?><br>
            <? endforeach; ?>
            </td>
        </tr>
    <? foreach (array(10,11,12,1,2,3,4,5,6,7,8,9) as $month): ?>
        <tr class="<?= $month % 2 ? 'steel1' : 'steelgraulight' ?><? if ($month == 9) echo ' bordered'; ?>">
            <td <? if (!isset($months[$month])) echo 'class="grayed"'; ?>>
            <? if (isset($months[$month])): ?>
                <a href="<?= $controller->url_for('graphs/monthly', $year, $month) ?>">
                    <?= $monthnames[$month] ?>
                </a>
            <? else: ?>
                <?= $monthnames[$month] ?>
            <? endif; ?>
            </td>
            <td colspan="3">
        <? if (isset($months[$month])): ?>
            <? foreach ($areas as $area): ?>
                <table class="default" style="width: <?= $width($month, 'total', $area, $max[$area]) ?>%;">
                    <tr>
                        <td style="width: <?= $width($month, 'unknown', $area) ?>%;" class="year_marker unknown <?= $area ?>">&nbsp;</td>
                        <td style="width: <?= $width($month, 'admin', $area) ?>%;" class="year_marker admin <?= $area ?>">&nbsp;</td>
                        <td style="width: <?= $width($month, 'teacher', $area) ?>%;" class="year_marker teacher <?= $area ?>">&nbsp;</td>
                        <td style="text-align: right; width: <?= $width($month, 'student', $area) ?>%;" class="year_marker <?= $area ?>">
                            <?= numberformat($months[$month]['total'][$area]) ?>
                        </td>
                    </tr>
                </table>
            <? endforeach; ?>
        <? endif; ?>
            </td>
        </tr>
    <? endforeach; ?>
    </tbody>
    <tfoot>
        <tr class="bordered">
            <td colspan="4">
                <? include 'field_legend.php'; ?>
            </td>
        </tr>
    </tfoot>
</table>