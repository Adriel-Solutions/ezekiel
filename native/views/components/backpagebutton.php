<?php
    /**
     * Name : Back Page Button
     */

    /**
     * Parameters :
     * - align : string (left | right)
     * - icon : string (arrow | close)
     * - href : string 
     * - callback : string (Alpine function)
     */
?>
<?php
    $align = $params['align'] ?? 'left';
    $class_position = $align === 'left' ? 'left-0' : 'right-0';
?>
<div class="relative h-full flex items-center">
    <a 
        <?php if ( !isset($params['href']) && !isset($params['callback']) ): ?>
            href="<?= $_SERVER['HTTP_REFERER'] ?? $_SERVER['REQUEST_URI'] ?>" 
        <?php elseif ( isset($params['href']) ): ?>
            href="<?= $params['href'] ?>" 
        <?php elseif ( isset($params['callback']) ): ?>
            x-on:click="<?= $params['callback'] ?>"
        <?php endif; ?>
        class="back-page-link cursor-pointer absolute <?= $class_position ?> inline-flex items-center p-3 border border-gray-300 rounded-full shadow-sm text-gray-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
    >
        <?php if ( !isset($params['icon']) || $params['icon']  === 'arrow'): ?>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        <?php elseif ( $params['icon'] === 'close' ): ?>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        <?php endif; ?>
    </a>
</div>
