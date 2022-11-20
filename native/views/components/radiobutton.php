<?php
    /**
     * Parameters :
     * - name : string
     * - value : string
     * - label : string
     * - payload : string
     * - is_clearable : boolean
     */
?>
<?php
    $payload = $params['payload'] ?? 'payload'; 
    $name = $params['name'];
    $value = $params['value'];
?>
<label 
    class="border rounded-md py-3 px-3 flex items-center justify-center text-sm font-medium uppercase sm:flex-1 cursor-pointer focus:outline-none"
    x-bind:class="{
        'bg-blue-600 border-transparent text-white hover:bg-blue-700' : <?= $payload ?>.<?= $name ?> == '<?= $value ?>',
        'bg-white border-blue-500 text-gray-900 hover:bg-gray-50' : <?= $payload ?>.<?= $name ?> != '<?= $value ?>' 
    }"

    <?php if ( isset($params['is_clearable']) && $params['is_clearable'] === true ): ?>
        x-on:click="if( <?= $payload ?>.<?= $name ?> == '<?= $value ?>' ) { <?= $payload ?>.<?= $name ?> = ''; $event.preventDefault(); }"
    <?php endif; ?>
>
    <input 
        type="radio" 
        name="<?= $name ?>" 
        value="<?= $value ?>" 
        x-model="<?= $payload ?>.<?= $name ?>"
        class="sr-only"
    />
    <span><?= $params['label'] ?></span>
</label>

