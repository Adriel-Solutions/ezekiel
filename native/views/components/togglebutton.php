<?php
    /**
     * Name : ToggleButton
     */

    /**
     * Parameters:
     * - toggle : string (Alpine variable to toggle on click)
     * - text_initial : string
     * - text_toggle : string
     * - icon_initial : string
     * - icon_toggle : string
     * - size : int
     * - attributes : array
     * - type : string
     */
?>
<?php
    $is_only_icon = !isset($params['text_initial']) && isset($params['icon_initial']);
    $size = $params['size'] ?? 12;
    $type = $params['type'] ?? 'default';
    switch($type) {
        case 'default':
            $class_color = 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 border-transparent text-white' ;
            break;

        case 'neutral':
            $class_color = 'text-gray-600 shadow-sm border border-gray-300 bg-white hover:bg-gray-50 focus:ring-blue-500 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200';
            break;
    }
?>
<button
    type="button"

    <?php if ( $is_only_icon ): ?>
        class="w-<?= $size ?> h-<?= $size ?> <?= $class_color ?>shadow-sm rounded-full inline-flex items-center justify-center rounded-md text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 w-full"
    <?php else: ?>
        class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 w-full"
    <?php endif; ?>

    x-on:click="<?= $params['toggle'] ?> = !<?= $params['toggle'] ?>"

    <?php if ( !$is_only_icon ): ?>
        x-text="!<?= $params['toggle'] ?> ? '<?= $params['text_initial'] ?> ' : '<?= $params['text_toggle'] ?>'"
    <?php endif; ?>

    <?php if ( isset($params['attributes']) ): ?>
        <?php foreach($params['attributes'] as $k => $v): ?>
            <?= $k ?>="<?= $v ?>"
        <?php endforeach; ?>
    <?php endif; ?>
>
    <?php if ( $is_only_icon ): ?>
        <template x-if="!<?= $params['toggle'] ?>">
            <?= $params['icon_initial'] ?>
        </template>
        <template x-if="<?= $params['toggle'] ?>">
            <?= $params['icon_toggle'] ?>
        </template>
    <?php endif; ?>
</button>

