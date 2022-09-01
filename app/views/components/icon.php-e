<?php 
    $size = $params['size'] ?? 6; 
    $viewbox = $params['viewbox'] ?? 24; 
?>
<svg 
    xmlns="http://www.w3.org/2000/svg" 
    class="h-<?= $size ?> w-<?= $size ?>" 

    viewBox="0 0 <?= $viewbox ?> <?= $viewbox ?>" 

    <?php if(!empty($params['stroke'])): ?>
        stroke-width="2" 
        stroke="currentColor" 
        fill="none" 
    <?php endif; ?>

    <?php if(!empty($params['fill'])): ?>
        fill="currentColor" 
    <?php endif; ?>

    aria-hidden="true"
>
    <?php include __DIR__ . '/../assets/icons/' . $params['icon'] . '.php'; ?>
</svg>
