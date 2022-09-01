<?php
    /**
     * Parameters :
     * - disabled : boolean
     * - name : string
     * - value : string
     * - required : boolean
     * - native : boolean (whether to use Alpine or not)
     * - prompt : string
     * - options : associative array
     * - label : string
     */
?>
<?php $params['id'] = uniqid(); ?>

<div
    class="w-full"
>
    <?php if ( !empty($params['label']) ): ?>
        <label
            for="<?= $params['id'] ?>"
            class="block text-sm font-medium text-gray-700"
        >
            <?= $params['label'] ?>
        </label>
    <?php endif; ?>

<?php if ( !empty($params['label']) ): ?>
    <div class="mt-1 relative">
<?php endif; ?>
    <select 
        id="<?= $params['id'] ?>"
        name="<?= $params['name'] ?>"

        <?php if( isset($params['disabled']) && $params['disabled'] === true ): ?>
            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none sm:text-sm bg-gray-100"
        <?php else: ?>
            class="cursor-pointer appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm"
        <?php endif; ?>

        <?php if( !empty($params['value']) ): ?>
            value="<?= $params['value'] ?>"
        <?php endif; ?>

        <?php if( isset($params['required']) && $params['required'] === true ): ?>
            required
        <?php endif; ?>

        <?php if( isset($params['disabled']) && $params['disabled'] === true ): ?>
            disabled
        <?php endif; ?>

        <?php if ( !isset($params['native']) || $params['native'] === false ): ?>
            x-model="payload.<?= $params['name'] ?>"
            x-bind:class="{ 'text-gray-400': !payload.<?= $params['name'] ?>  }"
        <?php endif; ?>
    >

        <?php if (isset($params['prompt'])): ?>
            <option value><?= $params['prompt'] ?></option>
        <?php endif; ?>

        <?php foreach($params['options'] as $v => $l): ?>
            <option 
                value="<?= $v ?>"

                <?php if(isset($params['value']) && $params['value'] === $v): ?>
                    selected
                <?php endif; ?>
            >
                <?= $l ?>
            </option>
        <?php endforeach; ?>
    </select>

<?php if ( !empty($params['label']) ): ?>
    </div>
<?php endif; ?>
</div>
