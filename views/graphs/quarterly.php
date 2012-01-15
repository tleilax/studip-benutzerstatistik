<table style="width: 100%;" cellspacing="0" cellpadding="2">
    <colgroup>
        <col span="3" width="33%"/>
    </colgroup>
    <thead>
        <tr>
            <th colspan="3">
                <form action="<?=$_SERVER['REQUEST_URI']?>" method="get" id="stat_form" style="float: right;">
                <?php if ($show_hits): ?>
                    <select name="type" id="type_toggle">
                        <option value="visits" <?=$type=='visits'?' selected="selected"':''?>><?=_('Besucher')?></option>
                        <option value="hits" <?=$type=='hits'?' selected="selected"':''?>><?=_('Seitenaufrufe')?></option>
                    </select>
                <?php else: ?>
                    <input type="hidden" name="type" value="visits" />
                <?php endif; ?>

                    <select name="quarter" id="quarter_selector">
                    <?php foreach($quarters as $index=>$value): ?>
                        <option value="<?=$index?>" <?=(date('Y', $quarter_stamp).'-'.ceil(date('m', $quarter_stamp)/3))==$index?'selected="selected"':''?>><?=$value['quarter']?>. Quartal <?=$value['year']?></option>
                    <?php endforeach; ?>
                    </select>

                    <noscript>
                        <?=makebutton('anzeigen', 'input')?>
                    </noscript>

                    <input type="hidden" name="cmd" value="<?=$_GET['cmd']?>"/>
                    <input type="hidden" name="id" value="<?=$_GET['id']?>"/>
                </form>

                <?=ceil(date('m', $quarter_stamp)/3)?>. <?=_('Quartal')?> <?=date('Y', $quarter_stamp)?>:
                <?=date('1.m.', $quarter_stamp)?>
                <?=_('bis')?>
                <?=date('t.m.', strtotime('2 months', $quarter_stamp))?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr class="bordered">
            <td>
                <div class="visits" <?=$type!='visits'?' style="display: none;"':''?>>
                    <div class="stats_marker">&nbsp;</div>
                    <?=_('Besucher')?>: <?=number_format($totals['visits'], 0, ',', '.')?>
                    <br style="clear: left;"/>
                    <div class="stats_marker headcount">&nbsp;</div>
                    <?=_('Kopfzahl')?>: <?=number_format($totals['headcount'], 0, ',', '.')?>
                    <br style="clear: left;"/>
                </div>
            <?php if ($show_hits): ?>
                <div class="hits" <?=$type!='hits'?' style="display: none;"':''?>>
                    <div class="stats_marker">&nbsp;</div>
                    <?=_('Seitenaufrufe')?>: <?=number_format($totals['hits'], 0, ',', '.')?>
                    <br style="clear: left;"/>
                </div>
            <?php endif; ?>
            </td>
            <td>
                <div class="visits" <?=$type!='visits'?' style="display: none;"':''?>>
                    &Oslash;/<?=_('Tag')?>: <?=number_format($average['visits'], 2, ',', '.')?><br/>
                    &Oslash;/<?=_('Tag')?>: <?=number_format($average['headcount'], 2, ',', '.')?><br/>
                </div>
            <?php if ($show_hits): ?>
                <div class="hits" <?=$type!='hits'?' style="display: none;"':''?>>
                    &Oslash;/<?=_('Tag')?>: <?=number_format($average['hits'], 2, ',', '.')?><br/>
                </div>
            <?php endif; ?>
            </td>
            <td>
                <div class="visits" <?=$type!='visits'?' style="display: none;"':''?>>
                    <?=_('Maximum')?>: <?=number_format($max['visits'], 0, ',', '.')?><br/>
                    <?=_('Maximum')?>: <?=number_format($max['headcount'], 0, ',', '.')?><br/>
                </div>
            <?php if ($show_hits): ?>
                <div class="hits" <?=$type!='hits'?' style="display: none;"':''?>>
                    <?=_('Maximum')?>: <?=number_format($max['hits'], 0, ',', '.')?><br/>
                </div>
            <?php endif; ?>
            </td>
        </tr>
    </tbody>
    <tbody>
        <tr>
            <td colspan="3">
                <table style="width: 100%; border-collapse: collapse;" cellspacing="0" cellpadding="0">
                    <colgroup>
                        <col width="5%"/>
                        <col span="<?=$days?>" width="<?=round(95/$days,2)?>%"/>
                    </colgroup>
                    <tbody>
                        <tr>
                            <td id="scale">
                                <div class="stat_display visits" style="<?=$type!='visits'?'display: none;':''?>position: relative; height: 300px;">
                                <?php foreach ($visits_scale as $scale): ?>
                                    <div class="scale_marker" style="bottom: <?=round($scale['percent']*300)?>px;"><?=number_format($scale['value'],0,',','.')?></div>
                                <?php endforeach; ?>
                                </div>
                            <?php if ($show_hits): ?>
                                <div class="stat_display hits" style="<?=$type!='hits'?'display: none;':''?>position: relative; height: 300px;">
                                <?php foreach ($hits_scale as $scale): ?>
                                    <div class="scale_marker" style="bottom: <?=round($scale['percent']*300)?>px;"><?=number_format($scale['value'],0,',','.')?></div>
                                <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            </td>
                        <?php for ($i=1, $_day=$quarter_stamp, $stamp=date('n-j', $_day); $i<=$days; $i++, $_day+=24*60*60, $stamp=date('n-j', $_day)): ?>
                            <td class="<?=date('n', $_day)%2==0?'steelgraulight':'steel1'?>" style="height: 300px; vertical-align: top; padding: 0px;">
                            <?php if (empty($stats[$stamp])): ?>
                                &nbsp;
                            <?php else: ?>
                                <div style="position: absolute; height: 300px;" class="stats_bar stats_hover">
                                    <div class="visits" style="<?=$type!='visits'?'display: none;':''?>height: <?=floor(100*$stats[$stamp]['visits']/$max['visits'])?>%;" title="<?=date('d.m.Y', $_day)?> - <?=_('Besucher')?>: <?=number_format($stats[$stamp]['visits'],0,',','.')?> / <?=_('Kopfzahl')?>: <?=number_format($stats[$stamp]['headcount'],0,',','.')?>">&nbsp;</div>
                                    <div class="visits headcount" style="<?=$type!='visits'?'display: none;':''?>height: <?=floor(100*$stats[$stamp]['headcount']/$max['visits'])?>%;" title="<?=date('d.m.Y', $_day)?> - <?=_('Besucher')?>: <?=number_format($stats[$stamp]['visits'],0,',','.')?> / <?=_('Kopfzahl')?>: <?=number_format($stats[$stamp]['headcount'],0,',','.')?>">&nbsp;</div>
                                <?php if ($show_hits): ?>
                                    <div class="hits" style="<?=$type!='hits'?'display: none;':''?>height: <?=floor(100*$stats[$stamp]['hits']/$max['hits'])?>%;" title="<?=date('d.m.Y', $_day)?> - <?=_('Seitenaufrufe')?>: <?=number_format($stats[$stamp]['hits'],0,',','.')?>">&nbsp;</div>
                                <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                        </tr>
                        <tr style="font-size: 0.7em;">
                            <td rowspan="2">&nbsp;</td>
                        <?php foreach ($day_captions as $caption): ?>
                            <td colspan="<?=$caption['span']?>" class="caption"><?=$caption['title']?></td>
                        <?php endforeach; ?>
                        </tr>
                        <tr style="text-align: center;">
                            <?php for ($i=0, $stamp=$quarter_stamp; $i<3; $i++, $stamp=strtotime('next month', $stamp)): ?>
                            <td class="month_caption" colspan="<?=date('t', $stamp)?>">
                                <a href="<?=$links['monthly']?>?month=<?=date('Y-m', $stamp)?>"><?=$months[date('n', $stamp)]?></a>
                            </td>
                            <?php endfor; ?>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>