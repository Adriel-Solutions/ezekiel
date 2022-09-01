<?php

    /**
     * Utility function to generate a pagination link based on current page, count, and filters
     *
     * @param {int} $index : The page to generate the link for
     * @return {string} The generated link with proper GET parameters
     */
    function page_link($index, $params) {
        $link = '?';
        $link .= 'p=';
        $link .= $index;
        $link .= '&c=';
        $link .= $params['per_page'];

        if(empty($params['filters_query']))
            return $link;

        $link .= '&s=1';
        $link .= '&';
        $link .= $params['filters_query'];

        return $link;
    }
?>

<div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
    <div class="sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <!-- RESULTS COUNT -->
        <div class="hidden sm:flex">
            <p class="text-sm text-gray-700">
            Affichage de
            <span class="font-medium"><?= $params['per_page'] * ($params['page'] - 1) + 1 ?></span>
            à
            <span class="font-medium">
                <?php if( $params['per_page'] * $params['page'] <= $params['count_rows']): ?>
                    <?= $params['per_page'] * $params['page'] ?>
                <?php else: ?>
                    <?= $params['count_rows'] ?>
                <?php endif; ?>
            </span>
            sur
            <span class="font-medium"><?= $params['count_rows'] ?></span>
                ligne<?php if ($params['count_rows'] > 1): ?>s<?php endif; ?>
            </p>
        </div>

        <!-- NAVIGATION -->
        <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">

                <?php if($params['page'] > 1): ?>
                    <!-- PREVIOUS -->
                    <a 
                        href="<?= page_link($params['page'] - 1, $params) ?>" 
                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                    >
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                <?php endif; ?>

                <!-- PAGES -->
                <?php
                    $should_be_smart = $params['count_pages'] > 6;

                    // Case when we have N > 6 pages, we should show : 1 2 3 ... 5 6 7
                    if($should_be_smart) {
                        $links_to_show = [ 
                            $params['page'],
                            $params['page'] + 1,
                            $params['page'] + 2,
                            'skip',
                            $params['count_pages'] - 2,
                            $params['count_pages'] - 1,
                            $params['count_pages'],
                        ];

                        // Case when we have N > 6, BUT we have reached the point where P = N - 6
                        if($params['page'] > $params['count_pages'] - 6) {
                            $links_to_show = [ 'skip' ];
                            for($i = $params['count_pages'] - 5; $i <= $params['count_pages']; $i++)
                                $links_to_show[] = $i;
                        }
                    }
                    // Case when we have N <= 6 pages, we should show 1 2 3 4 5 6
                    else {
                        $links_to_show = [];
                        for($i = 1; $i < $params['count_pages'] + 1; $i++)
                            $links_to_show[] = $i;
                    }
                ?>
                <?php foreach($links_to_show as $link_idx): ?>

                    <?php if($link_idx === 'skip'): ?>
                        <!-- SKIP -->
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"> ... </span>
                    <?php else: ?>
                        <!-- PAGE -->
                        <a 
                            href="<?= page_link($link_idx, $params) ?>" 

                            <?php if( $link_idx === $params['page'] ): ?>
                                class="z-10 bg-indigo-50 border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                            <?php else: ?>
                                class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                            <?php endif; ?>
                        >
                            <?= $link_idx ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php if($params['page'] < $params['count_pages']): ?>
                    <!-- NEXT -->
                    <a 
                        href="<?= page_link($params['page'] + 1, $params) ?>" 
                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                    >

                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</div>
