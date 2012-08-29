<h1>Datei�bersicht vom <?= date('d.m.Y H:i') ?></h1>
<dl>
    <dt><?= _('Anzahl hochgeladener Dateien:') ?></dt>
    <dd><?= number_format($data['total'], 0, ',', '.') ?></dd>
    <dt><?= _('Gesamtgr��e hochgeladener Dateien:') ?></dt>
    <dd>
        ~ <?= number_format($data['size'] / (1024 * 1024 * 1024), 0, ',', '.') ?> Gb
        (= <?= number_format($data['size'], 0, ',', '.') ?> Byte)
     </dd>
</dl>