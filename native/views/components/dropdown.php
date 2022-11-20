<?php
    /**
     * Name : Dropdown
     */

    /**
     * Parameters :
     * - prompt : string
     * - items : array<[href , action , label]>
     * - is_minimal : boolean
     * - style : string (default, button)
     */
?>
<?php
    $style = $params['style'] ?? 'default';
    $class_trigger = match($style) {
        'default' => 'bg-white focus:ring-blue-500 focus:border-blue-500 border-gray-300 ',
        'button' => 'bg-blue-600 focus:ring-offset-2 focus:ring-blue-500'
    };
    $class_prompt = match($style) {
        'default' => 'text',
        'button' => 'text-white'
    };
?>
<div
    class="w-full"
    x-data="{ open: false }"
    x-on:click.away="open = false"
>

    <div class="mt-1 relative">
        <!-- TRIGGER -->
        <?php if ( !isset($params['is_minimal']) || $params['is_minimal'] !== true ): ?>
            <button
                type="button"
                class="<?= $class_trigger ?> relative inline-flex w-full border rounded-md shadow-sm px-4 py-2 text-left cursor-default focus:outline-none focus:ring-1 sm:text-sm cursor-pointer"
                x-on:click="open = !open"
            >
                <span class="<?= $class_prompt ?> cursor-pointer block truncate"><?= $params['prompt'] ?></span>
                <svg class="<?= $class_prompt ?> cursor-pointer -mr-1 ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </button>
        <?php else: ?>
            <button
                type="button"
                class="ml-auto rounded-full flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-blue-500"
                x-on:click="open = !open"
            >
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" />
                </svg>
            </button>
        <?php endif; ?>

        <!-- POPOVER -->
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            x-on:click="open = !open"
            class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" tabindex="-1"
        >
            <div class="py-1" role="none">
                <?php foreach($params['items'] as $item): ?>
                    <a
                        <?php if ( isset($item['href']) && !empty($item['href']) ): ?>
                            href="<?= $item['href'] ?>"
                        <?php else: ?>
                            x-on:click="<?= $item['action'] ?>"
                        <?php endif; ?>
                        class="text-gray-700 block px-4 py-2 text-sm hover:text-gray-900 hover:bg-gray-100"
                        tabindex="-1"
                    >
                        <?= $item['label'] ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

