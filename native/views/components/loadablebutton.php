<?php
    /**
     * Name : Loadable Button
     */

    /**
     * Parameters :
     * - type : string
     * - loader : string (Alpine `loading` attribute, defaults to 'loading')
     * - text : string
     * - icon : string
     * - action : string (Alpine function name)
     * - attributes : array
     * - style : string
     */
?>
<?php 
    $type = $params['type'] ?? 'default'; 
    switch($type) {
        case 'default':
            $class_color = 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 border-transparent text-white' ;
            break;

        case 'danger':
            $class_color = 'bg-red-600 hover:bg-red-700 focus:ring-red-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 border-transparent text-white';
            break;

        case 'success':
            $class_color = 'bg-green-600 hover:bg-green-700 focus:ring-green-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 border-transparent text-white';
            break;

        case 'neutral':
            $class_color = 'text-gray-600 shadow-sm border border-gray-300 bg-white hover:bg-gray-50 focus:ring-blue-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200';
            break;
    }

    $style = $params['style'] ?? 'default';
    switch($style) {
        case 'default': 
            $class_style = "w-full py-2 px-4";
            break;

        case 'circle': 
            $class_style = "rounded-full p-4 w-14 h-14";
            break;
    }

    $action = $params['action']; 
    $loader = $params['loader'] ?? 'loading';
?>
<button 
    type="button" 

    x-on:click.prevent="<?= $action ?>"

    <?php if ( !isset($params['attributes']) || !isset($params['attributes']['class']) ): ?>
        class="flex items-center justify-center <?= $class_style ?> border rounded-md shadow-sm text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed <?= $class_color?>"
    <?php endif; ?>

    x-bind:class="{ <?= $loader ?>: <?= $loader ?> }"

    <?php if ( isset($params['attributes']) ): ?>
        <?php foreach($params['attributes'] as $k => $v): ?>
            <?= $k ?>="<?= $v ?>"
        <?php endforeach; ?>
    <?php endif; ?>
>

    <template x-if="<?= $loader ?>">
        <svg 
            xmlns="http://www.w3.org/2000/svg" 
            fill="none" 
            viewBox="0 0 24 24" 
            class="animate-spin h-5 w-5" 
        >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </template>

    <template x-if="!<?= $loader ?>">
        <?php if ( !empty($params['text']) ): ?>
            <span>
                <?= $params['text'] ?>
            </span>
        <?php else: ?>
            <?= $params['icon'] ?>
        <?php endif; ?>
    </template>
</button>
