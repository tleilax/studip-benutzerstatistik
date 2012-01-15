<?php

$permissions = array(
    'guest'   => _('Gasthörer'),
    'student' => _('Studenten'),
    'teacher' => _('Dozenten'),
    'admin'   => _('Admins'),
    'unknown' => _('Unbekannt'),
);
?>

<div class="user-statistics">
    <h2 class="topic">
    <?php if (is_array($stats['date'])): ?>
        <?= sprintf(_('Erfassungszeitraum: <em>%s</em> bis <em>%s</em>'), $stats['date']['start'], $stats['date']['end']) ?>
    <?php else: ?>
        <?= sprintf(_('Erfassungsdatum: <em>%s</em>'), $stats['date']) ?>
    <?php endif; ?>
    </h2>
    <div>
        <table class="default">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th><?= _('Insgesamt') ?></th>
                    <th><?= _('Kopfzahl') ?></th>
                    <th><?= _('Prozent') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr class="<?= TextHelper::cycle('cycle_even', 'cycle_odd') ?>">
                    <td><?= _('Besucher') ?></td>
                    <td><?= numberformat($stats['visits']) ?></td>
                    <td><?= numberformat($stats['headcount']) ?></td>
                    <td>100,0%</td>
                </tr>
                <tr class="<?= TextHelper::cycle('cycle_even', 'cycle_odd') ?>">
                    <td><?= _('Javascript aktiviert') ?></td>
                    <td><?= numberformat($stats['javascript']) ?></td>
                    <td>-</td>
                    <td><?= percent($stats['visits'], $stats['javascript']) ?></td>
                </tr>
                <tr class="<?= TextHelper::cycle('cycle_even', 'cycle_odd') ?>">
                    <td><?= _('Uni-intern') ?></td>
                    <td><?= numberformat($stats['internal']) ?></td>
                    <td>-</td>
                    <td><?= percent($stats['visits'], $stats['internal']) ?></td>
                </tr>
                <tr class="<?= TextHelper::cycle('cycle_even', 'cycle_odd') ?>">
                    <td><?= _('Mobile Endgeräte') ?></td>
                    <td><?= numberformat($stats['mobile']) ?></td>
                    <td>-</td>
                    <td><?= percent($stats['visits'], $stats['mobile']) ?></td>
                </tr>
                <tr class="<?= TextHelper::cycle('cycle_even', 'cycle_odd') ?>">
                    <td><?= _('Tablets') ?></td>
                    <td><?= numberformat($stats['tablet']) ?></td>
                    <td>-</td>
                    <td><?= percent($stats['visits'], $stats['tablet']) ?></td>
                </tr>
                <tr>
                    <th colspan="4"><?= _('Nach Berechtigungen') ?></th>
                </tr>
            <?php foreach ($permissions as $key => $title): ?>
                <tr class="<?= TextHelper::cycle('cycle_even', 'cycle_odd') ?>">
                    <td><?= $title ?></td>
                    <td><?= numberformat($stats['permissions'][$key]) ?></td>
                    <td><?= numberformat($stats['permissions'][$key.'_headcount']) ?></td>
                    <td><?= percent($stats['visits'], $stats['permissions'][$key]) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($stats['visits']): ?>
                <tr class="<?= TextHelper::cycle('cycle_even', 'cycle_odd') ?>">
                    <td colspan="4" style="padding:1em 0;">
                        <div id="permission-graph" style="height:150px"></div>
                        <script>
                        (function ($) {
                            var data = [],
                                $element = $('#permission-graph');
                            $element.width( $element.closest('td').innerWidth() - 8 );
<?php foreach ($permissions as $key => $title): ?>
                            data.push({label: "<?= $title ?>", data: <?= 0 + $stats['permissions'][$key] ?>});
<?php endforeach; ?>
                            $.plot($element, data, {series: {pie: {show: true}}});
                        }(jQuery));
                        </script>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php if (!empty($stats['user_agents'])): ?>
    <div>
        <table class="default browsers">
            <colgroup>
                <col>
                <col width="60px">
                <col width="60px">
            </colgroup>
            <thead>
                <tr>
                    <th><?= _('Browser') ?></th>
                    <th>&nbsp;</th>
                    <th><?= _('Prozent') ?></th>
                </tr>
            </thead>
        <?php foreach ($stats['user_agents'] as $agent): ?>
            <tbody class="collapsable <?= strtolower($agent['name']) ?>">
                <tr class="<?= $class = TextHelper::cycle('cycle_even', 'cycle_odd') ?>">
                    <td><?= $agent['name'] ?></td>
                    <td>&nbsp;</td>
                    <td><?= percent($stats['user_agent_total'], $agent['quantity'], 2) ?></td>
                </tr>
            <?php foreach (array_slice($agent['versions'], 0, 10) as $version => $quantity): ?>
                <tr class="<?= $class ?>">
                    <td><?= $version ?></td>
                    <td style="color:#888">
                        <?= percent($agent['quantity'], $quantity, 2) ?>
                    </td>
                    <td><?= percent($stats['user_agent_total'], $quantity, 2) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>

<?php if ($show_hits or !empty($stats['tracked'])): ?>
    <div>
        <table class="default">
        <?php if ($show_hits): ?>
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th><?= _('Insgesamt') ?></th>
                    <th><?= _('Kopfzahl') ?></th>
                    <th>&Oslash;</th>
                    <th><?= _('Prozent') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr class="<?= TextHelper::cycle('cycle_even', 'cycle_odd') ?>">
                    <td><?= _('Seitenaufrufe') ?></td>
                    <td><?= numberformat($stats['hits']) ?></td>
                    <td><?= numberformat($stats['headcount']) ?></td>
                    <td><?= percent($stats['visits'], $stats['hits'], 1, 1, '') ?></td>
                    <td><?= percent(100, 100) ?></td>
                </tr>
                <tr class="<?= TextHelper::cycle('cycle_even', 'cycle_odd') ?>">
                    <td>AJAX</td>
                    <td><?= numberformat($stats['ajax']) ?></td>
                    <td>-</td>
                    <td><?= percent($stats['visits'], $stats['ajax'], 1, 1, '') ?></td>
                    <td><?= percent($stats['hits'], $stats['ajax']) ?></td>
                </tr>
            </tbody>
        <?php endif; ?>
        <?php if (!empty($stats['tracked'])): ?>
            <tbody>
                <tr>
                    <th colspan="5"><?= _('Überwachte URLs') ?></th>
                </tr>
            <?php foreach ($stats['tracked'] as $title => $data): ?>
                <tr class="<?= TextHelper::cycle('cycle_even', 'cycle_odd') ?>">
                    <td><?= htmlReady($title) ?></td>
                    <td><?= numberformat($data['count']) ?></td>
                    <td><?= numberformat($data['head_count']) ?></td>
                    <td>-</td>
                    <td><?= percent($stats['hits'], $data['count']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

    <div>
        <table class="default os">
            <thead>
                <tr>
                    <th><?= _('Plattformen') ?></th>
                    <th><?= _('Prozent') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($stats['os'] as $os): ?>
                <tr class="<?= TextHelper::cycle('cycle_even', 'cycle_odd') ?> <?= @$os_mapping[$os['name']] ?>">
                    <td><?= htmlReady($os['name']) ?></td>
                    <td><?= percent($stats['os_total'], $os['quantity'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div>
        <table class="default">
            <thead>
                <tr>
                    <th><?= _('Bildschirmgrößen') ?></th>
                    <th><?= _('Prozent') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($stats['screen_sizes'] as $index => $size): ?>
                <tr class="<?= TextHelper::cycle('cycle_even', 'cycle_odd') ?>">
                    <td>
                        <?= $size['width'] ?>x<?= $size['height'] ?>
                    </td>
                    <td><?= percent($stats['screensize_total'], $size['quantity'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
