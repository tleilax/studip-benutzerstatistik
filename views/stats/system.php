<table class="default">
    <thead>
        <tr>
            <th>Rolle</th>
            <th>Gesamt</th>
            <th>Aktiv</th>
            <th>%</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($data as $perms => $row): ?>
        <tr class="<?= TextHelper::cycle('cycle_even', 'cycle_odd') ?>">
            <td><?= $perms ?></td>
            <td><?= numberformat($row['total']) ?></td>
            <td><?= numberformat($row['active']) ?></td>
            <td><?= percent($row['total'], $row['active']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
