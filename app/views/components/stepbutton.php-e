<?php
    /**
     * Parameters :
     * - type : string
     * - text : string
     * - condition : string (JS expression to enable the button)
     * - step : integer
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
    type="button" 
    class="<?= $class_color ?> w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed"
    x-bind:disabled="!(<?= $params['condition'] ?>)"
    x-on:click="step = step + 1"
    x-ref="submit_step_<?= $params['step'] ?>"
>
    <span>
        <?= $params['text'] ?>
    </span>
</button>
