<form class="menu" action="../actions/exportCSV_action.php" method="POST">
    <?php foreach ($columns as $colGroup): ?>
        <div class="column">
            <?php foreach ($colGroup as $col): ?>
                <div class="item">
                    <input type="checkbox" name="columns[]" value="<?= $col ?>">
                    <label><?= $col ?></label>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <div class="menu-footer">
        <span class="menu-text">Choisissez les attributs à exporter</span>
        <input type="submit" name="export_submit" value="Exporter" class="export-btn" id="export-menu-submit" />
    </div>
</form>