<table style="width: 100%;" cellspacing="0" cellpadding="2">
    <colgroup>
        <col width="10%">
        <col width="24%">
        <col span="2" width="33%"/>
    </colgroup>
    <thead>
        <tr>
            <th colspan="4">
                <form action="<?=$_SERVER['REQUEST_URI']?>" method="get" id="stat_form" style="float: right;">
                    <select name="year">
                    <?php foreach($years as $name): ?>
                        <option value="<?=$name?>" <?=$name==$year?'selected="selected"':''?>><?=$name?></option>
                    <?php endforeach; ?>
                    </select>

                    <noscript>
                        <?=makebutton('anzeigen', 'input')?>
                    </noscript>

                    <input type="hidden" name="cmd" value="<?=$_GET['cmd']?>"/>
                    <input type="hidden" name="id" value="<?=$_GET['id']?>"/>
                </form>

                <?=_('Zeitraum')?>: <?=date('d.m.Y', $start_date)?> <?=_('bis')?> <?=date('d.m.Y', $end_date)?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr class="bordered">
            <td colspan="2">
                <div class="visits">
                    <div class="stats_marker">&nbsp;</div>
                    <?=_('Besucher')?>: <?=number_format($totals['visits'],0,',','.')?>
                    <br style="clear: left;"/>
                    <div class="stats_marker headcount">&nbsp;</div>
                    <?=_('Kopfzahl')?>: <?=number_format($totals['uniquevisits'],0,',','.')?>
                    <br style="clear: left;"/>
                </div>
            <?php if ($show_hits): ?>
                <div class="hits">
                    <div class="stats_marker">&nbsp;</div>
                    <?=_('Seitenaufrufe')?>: <?=number_format($totals['hits'],0,',','.')?>
                    <br style="clear: left;"/>
                </div>
            <?php endif; ?>
            </td>
            <td>
                &Oslash;/<?=_('Monat')?>: <?=number_format($average['visits'], 2, ',', '.')?><br/>
                &Oslash;/<?=_('Monat')?>: <?=number_format($average['uniquevisits'], 2, ',', '.')?><br/>
            <?php if ($show_hits): ?>
                &Oslash;/<?=_('Monat')?>: <?=number_format($average['hits'], 2, ',', '.')?><br/>
            <?php endif; ?>
            </td>
            <td>
                <?=_('Maximum')?>: <?=number_format($max['visits'], 0, ',', '.')?><br/>
                <?=_('Maximum')?>: <?=number_format($max['uniquevisits'], 0, ',', '.')?><br/>
            <?php if ($show_hits): ?>
                <?=_('Maximum')?>: <?=number_format($max['hits'], 0, ',', '.')?><br/>
            <?php endif; ?>
            </td>
        </tr>
    <?php for ($month=1; $month<=12; $month++): ?>
        <tr class="<?=$month%2==0?'steel1':'steelgraulight'?><?=$month==12?' bordered':''?>">
            <td <?=!isset($months[$month])?' class="grayed"':''?>>
            <?php if (isset($months[$month])): ?>
                <a href="<?= $links['monthly'] ?>?month=<?= $year ?>-<?= $month ?>"><?=$monthnames[$month]?></a>
            <?php else: ?>
                <?=$monthnames[$month]?>
            <?php endif; ?>
            </td>
            <td colspan="3">
            <?php if (isset($months[$month])): ?>
                <table style="width: <?=str_replace(',', '.', min(100, round($months[$month]['total']['visits']/$max['visits']*100,2)))?>%; border-collapse: collapse;" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="overflow: hidden; border: 1px solid; background-image: url(<?=$image_path?>bg_dotted.gif); width: <?=str_replace(',', '.', 0+round($months[$month]['unknown']['visits']/$months[$month]['total']['visits']*100,2))?>%;" class="year_marker visits">&nbsp;</td>
                        <td style="overflow: hidden; border: 1px solid; background-image: url(<?=$image_path?>bg_diagonal.gif); width: <?=str_replace(',', '.', 0+round($months[$month]['admin']['visits']/$months[$month]['total']['visits']*100,2))?>%;" class="year_marker visits">&nbsp;</td>
                        <td style="overflow: hidden; border: 1px solid; background-image: url(<?=$image_path?>bg_diagonal2.gif); width: <?=str_replace(',', '.', 0+round($months[$month]['teacher']['visits']/$months[$month]['total']['visits']*100,2))?>%;" class="year_marker visits">&nbsp;</td>
                        <td style="overflow: hidden; border: 1px solid; text-align: right; width: <?=str_replace(',', '.', 0+round($months[$month]['student']['visits']/$months[$month]['total']['visits']*100,2))?>%;" class="year_marker visits"><?=number_format($months[$month]['total']['visits'],0,',','.')?></td>
                    </tr>
                </table>

                <table style="width: <?=str_replace(',', '.', min(100, round($months[$month]['total']['uniquevisits']/$max['visits']*100,2)))?>%; border-collapse: collapse;" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="border: 1px solid; background-image: url(<?=$image_path?>bg_dotted.gif); width: <?=str_replace(',', '.', round($months[$month]['unknown']['uniquevisits']/$months[$month]['total']['uniquevisits']*100,2))?>%;" class="year_marker headcount">&nbsp;</td>
                        <td style="border: 1px solid; background-image: url(<?=$image_path?>bg_diagonal.gif); width: <?=str_replace(',', '.', round($months[$month]['admin']['uniquevisits']/$months[$month]['total']['uniquevisits']*100,2))?>%;" class="year_marker headcount">&nbsp;</td>
                        <td style="border: 1px solid; background-image: url(<?=$image_path?>bg_diagonal2.gif); width: <?=str_replace(',', '.', round($months[$month]['teacher']['uniquevisits']/$months[$month]['total']['uniquevisits']*100,2))?>%;" class="year_marker headcount">&nbsp;</td>
                        <td style="border: 1px solid; text-align: right; width: <?=str_replace(',', '.', round($months[$month]['student']['uniquevisits']/$months[$month]['total']['uniquevisits']*100,2))?>%;" class="year_marker headcount"><?=number_format($months[$month]['total']['uniquevisits'],0,',','.')?></td>
                    </tr>
                </table>

            <?php if ($show_hits): ?>
                <table style="width: <?=str_replace(',', '.', min(100, round($months[$month]['total']['hits']/$max['hits']*100,2)))?>%; border-collapse: collapse;" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="border: 1px solid; background-image: url(<?=$image_path?>bg_dotted.gif); width: <?=str_replace(',', '.', round($months[$month]['unknown']['hits']/$months[$month]['total']['hits']*100,2))?>%;" class="year_marker hits">&nbsp;</td>
                        <td style="border: 1px solid; background-image: url(<?=$image_path?>bg_diagonal.gif); width: <?=str_replace(',', '.', round($months[$month]['admin']['hits']/$months[$month]['total']['hits']*100,2))?>%;" class="year_marker hits">&nbsp;</td>
                        <td style="border: 1px solid; background-image: url(<?=$image_path?>bg_diagonal2.gif); width: <?=str_replace(',', '.', round($months[$month]['teacher']['hits']/$months[$month]['total']['hits']*100,2))?>%;" class="year_marker hits">&nbsp;</td>
                        <td style="border: 1px solid; text-align: right; width: <?=str_replace(',', '.', round($months[$month]['student']['hits']/$months[$month]['total']['hits']*100,2))?>%;" class="year_marker hits"><?=number_format($months[$month]['total']['hits'],0,',','.')?></td>
                    </tr>
                </table>
            <?php endif; ?>

            <?php else: ?>
                &nbsp;
            <?php endif; ?>
            </td>
        </tr>
    <?php endfor; ?>
        <tr class="bordered">
            <td colspan="4">
                <?= $this->render_partial('graphs/field_legend.php') ?>
            </td>
        </tr>
    </tbody>
</table>