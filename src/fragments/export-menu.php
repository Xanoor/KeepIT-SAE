<form class="menu" action="../actions/exportCSV_action.php" method="POST">
    <button type="button" class="close-menu-btn" id="close-export-menu" aria-label="Close">X</button>

    <div class="menu-columns">
        <?php foreach ($columns as $tableName => $colGroup): ?>
            <div class="column">
                <h3 class="column-title"><?= htmlspecialchars($tableName) ?></h3>
                <div class="column-items">
                    <?php foreach ($colGroup as $col): ?>
                        <div class="item">
                            <label>
                                <input type="checkbox" name="columns[]" value="<?= htmlspecialchars($col) ?>">
                                <span class="item-text"><?= htmlspecialchars($col) ?></span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="menu-footer">
        <span class="menu-text">Choisissez les attributs à exporter</span>
        <input type="submit" name="export_submit" value="Exporter" class="export-btn" id="export-menu-submit" />
    </div>
</form>