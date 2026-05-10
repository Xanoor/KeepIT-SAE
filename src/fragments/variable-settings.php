<form class="edit-variable-form" action="../actions/admin-settings_action.php" method="POST">
    <input type="hidden" value="<?= isset($tableName) ? htmlspecialchars($tableName) : '' ?>" name="var-name"/>
    <input type="hidden" value="<?= isset($columnName) ? htmlspecialchars($columnName) : '' ?>" name="var-column"/>
    <label for="var-items-<?= isset($tableName) ? htmlspecialchars($tableName) : '' ?>"><?= isset($tableName) ? convertDataToFrench($tableName) : '' ?></label>
    <div class="edit-var-input-container">
        <select name="var-items" id="var-items-<?= isset($tableName) ? htmlspecialchars($tableName) : '' ?>">
            <?php foreach ($varItems ?? [] as $item): ?>
                <option value="<?= $item ?>"><?= $item ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Supprimer" name="DELETE_VAR">
    </div>
</form>