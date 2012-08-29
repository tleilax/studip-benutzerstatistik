<?php if ($flash['success']): ?>
    <?= Messagebox::success($flash['success']) ?>
<?php elseif ($flash['error']): ?>
    <?= Messagebox::error($flash['error']) ?>
<?php endif; ?>

<form id="usadmin" action="<?= $controller->url_for('admin/config') ?>" method="post">
    <fieldset>
        <legend><?= _('Einstellungen') ?>:</legend>

        <div class="type-text">
            <label for="internal_ip_mask"><?= _('IP-Maske für interne Zugriffe') ?>:</label>
            <input type="text" id="internal_ip_mask" name="internal_ip_mask" value="<?= htmlReady($internal_ip_mask) ?>">
            <small><?= _('Hinweis: Als Platzhalter wird * verwendet.') ?></small>
        </div>

        <div class="type-checkbox">
            <label for="store_hits"><?= _('Seitenzugriffe erfassen') ?></label>
            <input type="hidden" name="store_hits" value="0" />
            <input type="checkbox" id="store_hits" name="store_hits" value="1" <?= $store_hits ? 'checked' : '' ?>>
        </div>

        <div class="type-button">
            <?= makebutton('absenden', 'input') ?>
        </div>
    </fieldset>
</form>

<form action="<?= $controller->url_for('admin/edit') ?>" method="post">
    <fieldset>
        <legend><?= _('Zu überwachende URLs') ?></legend>

        <table class="default">
            <colgroup>
                <col width="50px">
                <col width="20px">
                <col width="40%">
                <col>
                <col width="100px">
            </colgroup>
            <thead>
                <tr>
                    <th><?= _('ID') ?></th>
                    <th><?= _('Aktiv') ?></th>
                    <th><?= _('Beschreibung') ?></th>
                    <th><?= _('URL') ?></th>
                    <th><?= _('Optionen') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($tracked_urls as $id => $url): ?>
                <tr class="<?= TextHelper::cycle('cycle_odd', 'cycle_even') ?>">
                    <td><?= htmlReady($id) ?></td>
                    <td style="text-align:center">
                        <input type="hidden" name="urls[<?= $id ?>][active]" value="0">
                        <input type="checkbox" name="urls[<?= $id ?>][active]" value="1" <?= $url['active'] ? 'checked' : '' ?>>
                    </td>
                    <td>
                        <input type="text" name="urls[<?= $id ?>][description]" value="<?= htmlReady($url['description']) ?>" style="width:95%">
                    </td>
                    <td>
                        <input type="text" name="urls[<?= $id ?>][url]" value="<?= htmlReady($url['url']) ?>" style="width:95%">
                    </td>
                    <td style="text-align: right;">
                        <a href="<?= $controller->url_for('admin/reset', $id) ?>" onclick="return confirm('<?= _('Wollen Sie diese URL wirklich zurücksetzen?') ?>');">
                            <?= Assets::img('icons/16/blue/refresh.png', array(
                                'title' => _('URL zurücksetzen'),
                            )) ?>
                        </a>
                        <a href="<?= $controller->url_for('admin/remove', $id) ?>" onclick="return confirm('<?= _('Wollen Sie diese URL wirklich löschen?') ?>');">
                            <?= Assets::img('icons/16/blue/trash.png', array(
                                'title' => _('URL löschen'),
                            )) ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="steelgroup6" style="text-align:center">
                    <td colspan="5">
                        <?= makebutton('speichern', 'input') ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </fieldset>
</form>

<form action="<?= $controller->url_for('admin/add') ?>" method="post">
    <fieldset>
        <legend><?= _('Neue zu überwachende URL eintragen') ?>:</legend>

        <div class="type-checkbox">
            <label for="new_active"><?= _('Aktiv') ?>:</label>
            <input type="hidden" name="new_active" value="0">
            <input type="checkbox" name="new_active" id="new_active" value="1" checked>
        </div>

        <div class="type-text">
            <label for="new_description"><?= _('Beschreibung') ?>:</label>
            <input type="text" name="new_description" id="new_description" style="width:400px">
        </div>

        <div class="type-text">
            <label for="new_url"><?= _('URL') ?>:</label>
            <input type="text" name="new_url" id="new_url" style="width:400px">
        </div>

        <div class="type-button">
            <?= makebutton('eintragen', 'input', null, 'add_url') ?>
        </div>
    </fieldset>
</form>

<form action="<?= $controller->url_for('admin/extra_tab') ?>" method="post">
    <fieldset>
        <legend><?= _('Zusätzlichen Tab einfügen') ?></legend>
        
        <div class="type-text">
            <label for="extra-tab-title">Titel:</label>
            <input type="text" name="extra-tab-title" id="extra-tab-title"
                   value="<?= htmlReady(Request::get('extra-tab-title', @$extra_tab['title'])) ?>"
                   style="width:400px">
        </div>
        
        <div class="type-text">
            <label for="extra-tab-url">URL:</label>
            <input type="text" name="extra-tab-url" id="extra-tab-url"
                   value="<?= htmlReady(Request::get('extra-tab-url', @$extra_tab['url'])) ?>"
                   style="width:400px">
        </div>
        
        <div class="type-button">
            <?= makebutton('speichern', 'input', null, 'extra_tab') ?>
        </div>
    </fieldset>
</form>

<form action="<?= $controller->url_for('admin/summarize') ?>" method="post">
    <fieldset>
        <legend><?= _('Zusammenfassung der Daten') ?>:</legend>
        <div class="type-text">
            <?= sprintf(_('Es können %s Tage zusammengefasst werden'), number_format($days_to_summarize, 0, ',', '.')) ?>.
        <?php if ($days_to_summarize): ?>
            <br>
            <button>&raquo; <?= _('Statistiken jetzt zusammenfassen') ?></button>
        <?php endif; ?>
        </div>
    </fieldset>
</form>
