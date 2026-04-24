<form class="edit-variable-form" action="../actions/admin-settings_action.php" method="POST">
    <input type="hidden" value="<?=  $tableName ?>" name="var-name"/>
    <div>
        <label for="var-items"><?= convertDataToFrench($tableName) ?></label>
        <select name="var-items" id="var-items">
            <?php foreach ($varItems as $item): ?>
                <option value="<?= $item ?>"><?= $item ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <input type="button" value="Supprimer" name="DELETE_VAR">
    <input type="button" value="Créer" name="CREATE_VAR">
</form>