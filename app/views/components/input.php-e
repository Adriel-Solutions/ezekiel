<?php
    /**
     * Parameters :
     * - hint : boolean / -> password-related only
     * - type : string
     * - label : string
     * - name : string
     * - placeholder : string
     * - disabled : boolean 
     * - attributes : associative array
     * - native : boolean (whether to use Alpine or not)
     * - options : associative array (when type = datalist)
     */
?>
<?php $params['id'] = uniqid(); ?>
<?php if ( $params['type'] !== 'hidden' ): ?>
    <div
        <?php if(isset($params['hint']) && $params['hint'] === true): ?>
            x-data="{ show_password: false }"
        <?php endif; ?>
        class="w-full"
    >
<?php endif; ?>

    <?php if ( !empty($params['label']) ): ?>
        <label
            for="<?= $params['id'] ?>"
            class="block text-sm font-medium text-gray-700"
        >
            <?= $params['label'] ?>
        </label>
    <?php endif; ?>

<?php if ( $params['type'] !== 'hidden' && !empty($params['label'])): ?>
    <div class="mt-1 relative">
<?php endif; ?>
        <input 
            id="<?= $params['id'] ?>"
            name="<?= $params['name'] ?>"

            <?php if ( $params['type'] !== 'datalist' ): ?>
                type="<?= $params['type'] ?>"
            <?php else: ?>
                list="list_<?= $params['id'] ?>"
            <?php endif; ?>

            <?php if( isset($params['disabled']) && $params['disabled'] === true ): ?>
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none sm:text-sm bg-gray-100"
            <?php else: ?>
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm"
            <?php endif; ?>

            placeholder="<?= empty($params['placeholder']) ? ($params['label'] ?? '') : $params['placeholder'] ?>"

            <?php if( isset($params['value']) ): ?>
                value="<?= $params['value'] ?>"
            <?php endif; ?>

            <?php if( isset($params['required']) && $params['required'] === true ): ?>
                required
            <?php endif; ?>

            <?php if( isset($params['disabled']) && $params['disabled'] === true ): ?>
                disabled
            <?php endif; ?>

            <?php if( isset($params['hint']) && $params['hint'] === true ): ?>
                x-bind:type="show_password ? 'text' : 'password'"
            <?php endif; ?>

            <?php if ( !isset($params['native']) || $params['native'] === false ): ?>
                x-model="payload.<?= $params['name'] ?>"
            <?php endif; ?>

            <?php if ( isset($params['attributes']) ): ?>
                <?php foreach($params['attributes'] as $k => $v): ?>
                    <?= $k ?>="<?= $v ?>"
                <?php endforeach; ?>
            <?php endif; ?>
        />

        <?php if ( $params['type'] === 'datalist' ): ?>
            <datalist id="list_<?= $params['id'] ?>">
                <?php foreach($params['options'] as $k => $v): ?>
                    <option value="<?= $v ?>"><?= $k ?></option>
                <?php endforeach; ?>
            </datalist>
        <?php endif; ?>

        <?php if ( $params['type'] === 'password' && isset($params['hint']) && $params['hint'] === true ): ?>
            <button 
                type="button" 
                class="cursor-pointer absolute inset-y-0 right-0 pl-3 pr-3 flex items-center text-gray-400 hover:text-black focus:outline-teal-500"
                x-on:click="show_password = !show_password"
            >

                <template x-if="!show_password">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </template>
                <template x-if="show_password">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </template>
            </button>
        <?php endif; ?>

    <?php if ( $params['type'] !== 'hidden' && !empty($params['label']) ): ?>
        </div>
    <?php endif; ?>
<?php if ( $params['type'] !== 'hidden' ): ?>
    </div>
<?php endif; ?>
