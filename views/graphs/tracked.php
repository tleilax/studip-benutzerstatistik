<table style="width: 100%;" cellspacing="0" cellpadding="2">
    <colgroup>
        <col width="10%"/>
        <col width="24%"/>
        <col span="2" width="33%"/>
    </colgroup>
    <thead>
        <tr>
            <th colspan="4">
                <form action="<?= PluginEngine::getLink('benutzerstatistik/graphs/tracked/'.$id) ?>" method="get" id="stat_form" style="float: right;">
                    <select name="year">
                    <?php foreach ($years as $name): ?>
                        <option value="<?= $name ?>" <?= $name==$year ? 'selected="selected"' : '' ?>>
                            <?= htmlReady($name) ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                    <noscript>
                        <?= makebutton('anzeigen', 'input') ?>
                    </noscript>
                </form>

                <?= htmlReady($title) ?>
                <?= _('im Zeitraum') ?>:
                <?= date('d.m.Y', $start_date) ?>
                <?= _('bis') ?>
                <?= date('d.m.Y', $end_date) ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr class="bordered">
            <td colspan="2" class="visits">
                <div class="stats_marker">&nbsp;</div>
                <?= _('Aufrufe') ?>: <?= number_format($total, 0, ',', '.') ?>

                <br style="clear: left;" />

                <div class="stats_marker headcount">&nbsp;</div>
                <?= _('Kopfzahl') ?>: <?= number_format($total_headcount, 0, ',', '.') ?>
            </td>
            <td>
                &Oslash;/<?= _('Monat') ?>: <?= number_format($average, 2, ',', '.') ?> <br />
                &Oslash;/<?= _('Monat') ?>: <?= number_format($average_headcount, 2, ',', '.') ?> <br />
            </td>
            <td>
                <?= _('Maximum') ?>: <?= number_format($max, 0, ',', '.') ?> <br />
                <?= _('Maximum') ?>: <?= number_format($max_headcount, 0, ',', '.') ?> <br />
            </td>
        </tr>
    <?php for ($month=1; $month<=12; $month++): ?>
        <tr class="<?= $month%2==0 ? 'steel1' : 'steelgraulight' ?><?= $month==12 ? ' bordered' : '' ?>">
            <td <?= empty($months[$month]) ? ' class="grayed"' : '' ?>>
                <?= $monthnames[$month] ?>
            </td>
            <td colspan="3">
            <?php if (isset($months[$month])): ?>
                <?php $quotient = $months[$month]/$max; ?>
              <?php if ($quotient > 0.03): ?>
                <div class="year_marker visits" style="width:<?= str_replace(',', '.', min(99, round($quotient*99, 2))) ?>%;">
                    <?= number_format($months[$month], 0, ',', '.') ?>
                </div>
              <?php else: ?>
                <div class="year_marker visits" style="float:left;width:<?= str_replace(',', '.', min(99, round($quotient*99, 2))) ?>%;">&nbsp;</div>
                <span style="font-size:0.8em;margin-left:0.5em;"><?= number_format($months[$month], 0, ',', '.') ?></span>
                <br style="clear: left;" />
              <?php endif; ?>
                <?php $quotient = $head_count[$month]/$max; ?>
              <?php if ($quotient > 0.03): ?>
                <div class="year_marker headcount" style="width:<?= str_replace(',', '.', min(99, round($quotient*99, 2))) ?>%;">
                    <?= number_format($head_count[$month], 0, ',', '.') ?>
                </div>
              <?php else: ?>
                <div class="year_marker headcount" style="float:left;width:<?= str_replace(',', '.', min(99, round($quotient*99, 2))) ?>%;">&nbsp;</div>
                <span style="font-size:0.8em;margin-left:0.5em;"><?= number_format($head_count[$month], 0, ',', '.') ?></span>
                <br style="clear: left;" />
              <?php endif; ?>
            <?php else: ?>
                &nbsp;
            <?php endif; ?>
            </td>
        </tr>
    <?php endfor; ?>
    </tbody>
</table>