<?php
    /**
     * Name : Navigation Tabs
     */

    /**
     * Parameters :
     * - current : string
     * - tabs : array<string, link, title, active>
     */
?>
<nav class="flex flex-nowrap relative z-10 rounded-lg shadow flex divide-x divide-gray-200" aria-label="Tabs">
    <?php foreach($params['tabs'] as $tab): ?>
        <a 
            href="<?= $tab['link'] ?>" 

            <?php if($tab['key'] === $params['current']): ?>
                class="flex justify-center items-center text-gray-900 rounded-l-lg group relative min-w-0 flex-1 overflow-hidden bg-white py-4 px-4 text-sm font-medium text-center hover:bg-gray-50 focus:z-10" aria-current="page"
            <?php else: ?>
                class="flex justify-center items-center text-gray-500 hover:text-gray-700 group relative min-w-0 flex-1 overflow-hidden bg-white py-4 px-4 text-sm font-medium text-center hover:bg-gray-50 focus:z-10"
            <?php endif; ?>
        >
            <span><?= $tab['title'] ?></span>
            
            <?php if($tab['key'] === $params['current']): ?>
                <span class="bg-blue-500 absolute inset-x-0 bottom-0 h-0.5"></span>
            <?php else: ?>
                <span class="bg-transparent absolute inset-x-0 bottom-0 h-0.5"></span>
            <?php endif; ?>
        </a>
    <?php endforeach; ?>
</nav>

