<?php
    /**
     * Name : Badge
     */

    /**
     * Parameters :
     * - color : string
     * - text : string
     * - icon : string
     * - hide_dot : boolean
     */
?>
<?php
    $is_only_icon = empty($params['text']) && !empty($params['icon']);
?>
<span class="inline-flex items-center <?php if ( !$is_only_icon ): ?>px-3<?php else: ?>px-0.5<?php endif; ?> py-0.5 rounded-full text-sm font-medium bg-<?= $params['color'] ?>-100 text-<?= $params['color'] ?>-800">
    <?php if ( empty($params['hide_dot']) ): ?>
        <svg class="-ml-1 mr-1.5 h-2 w-2 text-<?= $params['color'] ?>-400" fill="currentColor" viewBox="0 0 8 8">
            <circle cx="4" cy="4" r="3" />
        </svg>
    <?php endif; ?>

    <?php if ( !empty($params['icon']) ): ?>
        <?= $params['icon'] ?>
    <?php endif; ?>

    <?php if ( !empty($params['text']) ): ?>
        <?= $params['text'] ?>
    <?php endif; ?>
</span>

