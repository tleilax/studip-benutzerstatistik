<table style="width: 100%;" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="3">
                <form action="<?=$_SERVER['REQUEST_URI']?>" method="get" id="stat_form" style="float: right;">
                <?php if ($show_hits): ?>
                    <select name="type">
                        <option value="visits" <?=$type=='visits'?' selected="selected"':''?>><?=_('Besucher')?></option>
                        <option value="hits" <?=$type=='hits'?' selected="selected"':''?>><?=_('Seitenaufrufe')?></option>
                    </select>
                <?php else: ?>
                    <input type="hidden" name="type" value="visits" />
                <?php endif; ?>

                    <select name="month">
                    <?php foreach($months as $index=>$name): ?>
                        <option value="<?=$index?>" <?=date('Y-n', $month_stamp)==$index?'selected="selected"':''?>><?=$name?></option>
                    <?php endforeach; ?>
                    </select>

                    <noscript>
                        <?=makebutton('anzeigen', 'input')?>
                    </noscript>

                    <input type="hidden" name="cmd" value="<?=$_GET['cmd']?>"/>
                    <input type="hidden" name="id" value="<?=$_GET['id']?>"/>
                </form>

                <?=_('Zeitraum')?>:
                <?=date('1.m.Y', $month_stamp)?>
                <?=_('bis')?>
                <?=date('t.m.Y', $month_stamp)?>
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
                    &Oslash;/<?=_('Tag')?>: <?=number_format($totals['visits'] / $max_days, 2, ',', '.')?><br/>
                    &Oslash;/<?=_('Tag')?>: <?=number_format($average['headcount'] / $max_days, 2, ',', '.')?><br/>
                </div>
            <?php if ($show_hits): ?>
                <div class="hits" <?=$type!='hits'?' style="display: none;"':''?>>
                    &Oslash;/<?=_('Tag')?>: <?=number_format($totals['hits'] / $max_days, 2, ',', '.')?><br/>
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
        <tr>
            <td colspan="3" style="border-bottom: 1px solid black;">
                <table style="width: 100%; border-collapse: collapse;" cellspacing="0" cellpadding="0">
                    <colgroup>
                        <col width="5%"/>
                        <col span="<?=date('t', $month_stamp)?>" width="<?=round(95/date('t', $month_stamp), 2)?>%"/>
                    </colgroup>
                    <tbody>
                        <tr>
                            <td id="scale">
                                <div class="visits" style="<?=$type!='visits'?'display: none;':''?>position: relative; height: 300px;">
                                <?php foreach ($visits_scale as $scale): ?>
                                    <div class="scale_marker" style="bottom: <?=round($scale['percent']*300)?>px;"><?=number_format($scale['value'],0,',','.')?></div>
                                <?php endforeach; ?>
                                </div>
                            <?php if ($show_hits): ?>
                                <div class="hits" style="<?=$type!='hits'?'display: none;':''?>position: relative; height: 300px;">
                                <?php foreach ($hits_scale as $scale): ?>
                                    <div class="scale_marker" style="bottom: <?=round($scale['percent']*300)?>px;"><?=number_format($scale['value'],0,',','.')?></div>
                                <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            </td>
                        <?php for ($i=1; $i<=date('t', $month_stamp); $i++): ?>
                            <td class="<?=$i%2==0?'steel1':'steelgraulight'?>" style="height: 300px; vertical-align: top; padding: 0px;">
                            <?php if (empty($stats[$i])): ?>
                                &nbsp;
                            <?php else: ?>
                                <div style="position: absolute; height: 300px;" class="stats_bar_big stats_hover">
<?php // START -> VISITS ?>
<?php $tooltip = $i.'.'.date('m.Y', $month_stamp).' - '._('Besucher').': '.number_format($stats[$i]['total']['visits'],0,',','.').' / '._('Kopfzahl').': '.number_format($stats[$i]['total']['headcount'],0,',','.'); ?>
                                    <div class="visits" style="<?=$type!='visits'?'display: none;':''?>height: <?=floor(100*$stats[$i]['total']['visits']/$max['visits'])?>%;" title="<?=$tooltip?>">&nbsp;</div>
                                    <div class="visits noconnector" style="<?=$type!='visits'?'display: none;':''?>position: absolute; height: <?=round(100*$stats[$i]['unknown']['visits']/$max['visits'], 2)?>%; background-image: url(<?= $image_path ?>bg_dotted.gif);" title="<?=$tooltip?>">&nbsp;</div>
                                    <div class="visits noconnector" style="<?=$type!='visits'?'display: none;':''?>position: absolute; height: <?=round(100*$stats[$i]['admin']['visits']/$max['visits'], 2)?>%; bottom: <?=round(100*$stats[$i]['unknown']['visits']/$max['visits'], 2)?>%; background-image: url(<?=$image_path?>bg_diagonal.gif);" title="<?=$tooltip?>">&nbsp;</div>
                                    <div class="visits noconnector" style="<?=$type!='visits'?'display: none;':''?>position: absolute; height: <?=round(100*$stats[$i]['teacher']['visits']/$max['visits'], 2)?>%; bottom: <?=round(100*($stats[$i]['unknown']['visits']+$stats[$i]['admin']['visits'])/$max['visits'], 2)?>%; background-image: url(<?=$image_path?>bg_diagonal2.gif);" title="<?=$tooltip?>">&nbsp;</div>
<?php // END -> VISITS ?>

<?php // START -> UNIQUE VISITS ?>
                                    <div class="visits headcount" style="<?=$type!='visits'?'display: none;':''?>left: 9px;height: <?=floor(100*$stats[$i]['total']['headcount']/$max['visits'])?>%;" title="<?=$tooltip?>">&nbsp;</div>
                                    <div class="visits headcount noconnector" style="<?=$type!='visits'?'display: none;':''?>position: absolute; left: 9px; height: <?=round(100*$stats[$i]['unknown']['headcount']/$max['visits'], 2)?>%; background-image: url(<?=$image_path?>bg_dotted.gif);" title="<?=$tooltip?>">&nbsp;</div>
                                    <div class="visits headcount noconnector" style="<?=$type!='visits'?'display: none;':''?>position: absolute; left: 9px; height: <?=round(100*$stats[$i]['admin']['headcount']/$max['visits'], 2)?>%; bottom: <?=round(100*$stats[$i]['unknown']['headcount']/$max['visits'], 2)?>%; background-image: url(<?=$image_path?>bg_diagonal.gif);" title="<?=$tooltip?>">&nbsp;</div>
                                    <div class="visits headcount noconnector" style="<?=$type!='visits'?'display: none;':''?>position: absolute; left: 9px; height: <?=round(100*$stats[$i]['teacher']['headcount']/$max['visits'], 2)?>%; bottom: <?=round(100*($stats[$i]['unknown']['headcount']+$stats[$i]['admin']['headcount'])/$max['visits'], 2)?>%; background-image: url(<?=$image_path?>bg_diagonal2.gif);" title="<?=$tooltip?>">&nbsp;</div>
<?php // END -> UNIQUE VISITS ?>

<?php if ($show_hits): // START -> HITS ?>
<?php $tooltip = $i.'.'.date('m.Y', $month_stamp).' - '._('Seitenaufrufe').': '.number_format($stats[$i]['total']['hits'],0,',','.');?>
                                    <div class="hits" style="<?=$type!='hits'?'display: none;':''?>height: <?=floor(100*$stats[$i]['total']['hits']/$max['hits'])?>%;" title="<?=$tooltip?>">&nbsp;</div>
                                    <div class="hits noconnector" style="<?=$type!='hits'?'display: none;':''?>position: absolute; height: <?=round(100*$stats[$i]['unknown']['hits']/$max['hits'], 2)?>%; background-image: url(<?=$image_path?>bg_dotted.gif);" title="<?=$tooltip?>">&nbsp;</div>
                                    <div class="hits noconnector" style="<?=$type!='hits'?'display: none;':''?>position: absolute; height: <?=round(100*$stats[$i]['admin']['hits']/$max['hits'], 2)?>%; bottom: <?=round(100*$stats[$i]['unknown']['hits']/$max['hits'], 2)?>%; background-image: url(<?=$image_path?>bg_diagonal.gif);" title="<?=$tooltip?>">&nbsp;</div>
                                    <div class="hits noconnector" style="<?=$type!='hits'?'display: none;':''?>position: absolute; height: <?=round(100*$stats[$i]['teacher']['hits']/$max['hits'], 2)?>%; bottom: <?=round(100*($stats[$i]['unknown']['hits']+$stats[$i]['admin']['hits'])/$max['hits'], 2)?>%; background-image: url(<?=$image_path?>bg_diagonal2.gif);" title="<?=$tooltip?>">&nbsp;</div>
<?php endif; // END -> HITS ?>
                                </div>
                            <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        <?php for ($i=1; $i<=date('t', $month_stamp); $i++): ?>
                            <td style="<?=in_array(date('w', $month_stamp+12*60*60+($i-1)*24*60*60), array('0','6'))?' background-color: #FFC;':''?>border: 1px solid black; border-bottom: 0px; text-align: center;"><?=$i?></td>
                        <?php endfor; ?>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr class="bordered">
            <td colspan="3">
                <?= $this->render_partial('graphs/field_legend.php') ?>
            </td>
        </tr>
    </tbody>
</table>