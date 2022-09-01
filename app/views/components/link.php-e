<?php
    /**
     * Parameters :
     * - size : string (2 letters, Tailwind-ish, like in text-sm. -> sm <-)
     * - href : string
     * - text : string
     * - type : string
     */
?>
<?php
    $type = $params['type'] ?? 'default'; 
    switch($type) {
        case 'default':
            $class_color = 'text-teal-600 hover:text-teal-500';
            break;
    }

    $size = $params['size'] ?? 'default';
    switch($size) {
        case 'default':
            $class_size = 'text-sm';
            break;
    }
?>
<a 
    href="<?= $params['href'] ?>"
    class="<?= $class_color ?> <?= $class_size ?> font-medium"
> 
    <?= $params['text'] ?>
</a>
