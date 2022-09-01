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

    <div class="mt-1 relative">
        <textarea 
            id="<?= $params['id'] ?>"
            name="<?= $params['name'] ?>"
            rows="4"

            <?php if( isset($params['disabled']) && $params['disabled'] === true ): ?>
                class="resize-none appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none sm:text-sm bg-gray-100"
            <?php else: ?>
                class="resize-none appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm"
            <?php endif; ?>

            placeholder="<?= empty($params['placeholder']) ? ($params['label'] ?? '') : $params['placeholder'] ?>"

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
            <?php endif; ?>
        ></textarea>
    </div>
</div>
