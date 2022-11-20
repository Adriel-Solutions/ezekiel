<?php
    /**
     * Name : Link
     */

    /**
     * Parameters :
     * - size : string (2 letters, Tailwind-ish, like in text-sm. -> sm <-)
     * - href : string
     * - text : string
     * - type : string
     * - callback : string
     * - use_button : boolean
     */
?>
<?php
    $type = $params['type'] ?? 'default';
    switch($type) {
        case 'default':
            $class_color = 'text-blue-600 hover:text-blue-500';
            break;

        case 'danger':
            $class_color = 'text-red-600 hover:text-red-500';
            break;
    }

    $size = $params['size'] ?? 'default';
    switch($size) {
        case 'default':
            $class_size = 'text-sm';
            break;
    }

    $use_button = $params['use_button'] ?? false;
    $node = !$use_button ? 'a' : 'button';
?>
<<?= $node ?>
    <?php if ( isset($params['callback']) ): ?>
        x-on:click.prevent="<?= $params['callback'] ?>"
    <?php else: ?>
        href="<?= $params['href'] ?>"
    <?php endif; ?>
    class="<?= $class_color ?> <?= $class_size ?> font-medium cursor-pointer focus:outline-0 focus:border-0 disabled:pointer-events-none disabled:text-gray-500"

    <?php if ( isset($params['attributes']) ): ?>
        <?php foreach($params['attributes'] as $k => $v): ?>
            <?php if ( $k === 'x-show' ) continue; ?>
            <?= $k ?>="<?= $v ?>"
        <?php endforeach; ?>
    <?php endif; ?>
>
    <?= $params['text'] ?>
</<?= $node ?>>
