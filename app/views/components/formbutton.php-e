<?php
    /**
     * Parameters :
     * - type : string
     * - native : boolean (whether to use Alpine or not)
     * - text : string
     */
?>
<?php 
    $type = $params['type'] ?? 'default'; 
    switch($type) {
        case 'default':
            $class_color = 'bg-teal-600 hover:bg-teal-700 focus:ring-teal-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200';
            break;
    }
?>
<button 
    type="submit" 
    class="<?= $class_color ?> w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed"

    <?php if ( !isset($params['native']) || $params['native'] === false ): ?>
        x-bind:disabled="!ready"
        x-bind:class="{ loading: loading }"
    <?php endif;?>
>

    <?php if ( !isset($params['native']) || $params['native'] === false ): ?>
        <template x-if="loading">
            <svg 
                xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24" 
                class="animate-spin h-5 w-5 text-white" 
            >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </template>

        <template x-if="!loading">
            <span>
                <?= $params['text'] ?>
            </span>
        </template>
    <?php endif; ?>

    <?php if ( isset($params['native']) && $params['native'] === true ): ?>
        <span>
            <?= $params['text'] ?>
        </span>
    <?php endif; ?>
</button>
