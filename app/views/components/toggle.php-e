<?php
    /**
     * Parameters :
     * - value : boolean
     * - label : string
     * - name : string
     * - help : string
     */
?>
<?php 
    $params['id'] = uniqid();
    $params['value'] = !empty($params['value']) ? $params['value'] : false; 
    $params['value'] = in_array($params['value'], [ 1 , true, 'yes' , 'true' ]);
?>
<div 
    class="w-full py-4 flex items-center justify-between"
>
    <?php if ( !empty($params['label']) ): ?>
        <div class="flex flex-col max-w-xl">
            <label
                class="block text-sm font-medium text-gray-700"
            >
                <?= $params['label'] ?>
            </label>
            <?php if ( !empty($params['help']) ): ?>
                <p class="text-sm text-gray-500"><?= $params['help'] ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class=""
        x-data="{ active: <?= var_export($params['value'], true) ?> }"
    >
        <button 
            type="button" 
            x-bind:class="active ? 'bg-teal-600 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500':'bg-gray-200 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500'"
            role="switch" 
            aria-checked="false"
            x-on:click="active = !active; $refs['<?= $params['id'] ?>'].dispatchEvent(new Event('change'))"
        >
            <span 
                aria-hidden="true" 
                class=""
                x-bind:class="active ? 'translate-x-5 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200': 'translate-x-0 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200'"
            ></span>
        </button>

        <input 
            x-ref="<?= $params['id'] ?>" 
            type="hidden" 
            name="<?= $params['name'] ?>" 
            <?php // x-model doesn't work, we have to programatically update the model ?>
            x-init="$data.payload.<?= $params['name'] ?> = <?= $params['value'] === true ?  'true' : 'false' ?>; active = <?= $params['value'] ===  true ? 'true' : 'false' ?>"
            x-on:change="$data.payload.<?= $params['name'] ?> = active"
        />
    </div>

</div>
