<form class="edit-variable-form" action="../actions/admin-settings_action.php" method="POST">
    <input type="hidden" value="<?=  $tableName ?>" name="var-name"/>
    <input type="hidden" value="<?=  $columnName ?>" name="var-column"/>
    <div>
        <label for="var-items-<?= $tableName ?>"><?= convertDataToFrench($tableName) ?></label>
        <select name="var-items" id="var-items-<?= $tableName ?>">
            <?php foreach ($varItems as $item): ?>
                <option value="<?= $item ?>"><?= $item ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <input type="submit" value="Supprimer" name="DELETE_VAR">
</form>