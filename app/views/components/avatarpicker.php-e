<?php $default_size = 8; ?>

<div 
    class="flex items-center"
    x-data="avatar_picker()"

    <?php if(!empty($params['value'])): ?>
        data-default="<?= $params['value'] ?>"
    <?php endif; ?>
>
    <span 
        class="inline-block h-<?= $params['size'] ?? $default_size ?> w-<?= $params['size'] ?? $default_size ?> rounded-full overflow-hidden bg-gray-100"
        x-on:erased:avatar.window="clearPreview()"
    >
        <template x-if="!<?= $params['name'] ?>">
            <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </template>
        <template x-if="<?= $params['name'] ?>">
            <img alt="Avatar" x-bind:src="<?= $params['name'] ?>" />
        </template>
    </span>

    <input 
        type="file" 
        style="display:none" 
        name="<?= $params['name'] ?>" 
        x-on:change="updatePreview(Object.values($event.target.files)[0])"
        x-ref="filePicker"
    />

    <button 
        type="button" 
        class="ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        x-on:click="triggerFilePicker('<?= $params['name'] ?>')"
    >
        <?= __('Modifier') ?>
    </button>
</div>
