<?php
    /**
     * Name : Empty State
     */

    /**
     * Parameters :
     * - title : string
     * - subtitle : string
     * - action_link : string
     * - action_text : string
     * - action_icon : string
     * - action_callback : string (Alpine listener)
     */
?>
<div class="flex-1 h-full flex items-center justify-center">
    <div class="text-center">
        <h3 class="mt-2 text-3xl font-medium text-gray-900">
            <?= $params['title'] ?>
        </h3>
        <p class="mt-4 text-xl text-gray-500">
            <?= $params['subtitle'] ?>
        </p>
        <div class="mt-6">
            <?php if ( !empty($params['action_link']) ): ?>
                <a 
                    href="<?= $params['action_link'] ?>"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <div class="-ml-1 mr-2">
                        <?= $params['action_icon'] ?>
                    </div>
                    <?= $params['action_text'] ?>
                </a>
            <?php elseif ( !empty($params['action_callback']) ): ?>
                <button 
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    x-on:click="<?= $params['action_callback'] ?>"
                >
                    <div class="-ml-1 mr-2">
                        <?= $params['action_icon'] ?>
                    </div>
                    <?= $params['action_text'] ?>
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

