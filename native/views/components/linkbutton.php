<?php
    /**
     * Name : Link Button
     */

    /**
     * Parameters :
     * - href : string
     * - text : string
     * - icon : string
     * - type : string (default, danger, neutral, neutral_flat)
     * - style : string (default, rounded)
     * - size : int
     * - callback : string (Alpine function)
     * - attributes : array
     */
?>
<?php
    $type = $params['type'] ?? 'default'; 
    switch($type) {
        case 'default':
            $class = 'text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 shadow-sm border-transparent';
            break;

        case 'danger':
            $class = 'text-white bg-red-600 hover:bg-red-700 focus:ring-red-500 shadow-sm border-transparent';
            break;

        case 'neutral':
            $class = 'text-gray-700 bg-white hover:bg-gray-50 focus:ring-blue-500 border-gray-300 shadow-sm';
            break;

        case 'neutral_flat':
            $class = 'text-gray-700 bg-white hover:bg-gray-50 focus:ring-blue-500 shadow-none broder-transparent';
            break;
    }

    $size = $params['size'] ?? 12;
    $style = $params['style'] ?? 'default';
    switch($style) {
        case 'default':
            $class .= ' rounded-md';
            break;

        case 'rounded':
            $class .= ' rounded-full h-' . $size . ' w-' . $size;
            break;
    }

    $is_only_icon = isset($params['icon']) && !isset($params['text']);
    if($is_only_icon) {
        if($style !== 'rounded')
            $class .= ' px-3 py-2';
    }
    else 
        $class .= ' px-4 py-2';
?>
<a
    <?php if ( !empty($params['href']) ): ?>
        href="<?= $params['href'] ?>"
    <?php elseif ( !empty($params['callback']) ):  ?>
        x-on:click="<?= $params['callback'] ?>"
    <?php endif; ?>
    class="<?= $class ?> cursor-pointer inline-flex items-center justify-center border text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 w-full"

    <?php if ( isset($params['attributes']) ): ?>
        <?php foreach($params['attributes'] as $k => $v): ?>
            <?= $k ?>="<?= $v ?>"
        <?php endforeach; ?>
    <?php endif; ?>
>
    <?php if ( isset($params['icon']) ): ?>
        <?php if ( isset($params['text']) ): ?>
            <div class="-ml-1 mr-2">
        <?php endif; ?>
            <?= $params['icon'] ?>
        <?php if ( isset($params['text']) ): ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if ( isset($params['text']) ): ?>
        <?= $params['text'] ?>
    <?php endif; ?>
</a>

