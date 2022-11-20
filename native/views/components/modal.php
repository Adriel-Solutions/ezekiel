<?php
    /**
     * Name : Modal
     */

    /**
     * Parameters :
     * - id : string
     * - content : string
     * - width : string (xl, lg, sm, md, etc)
     * - callback_cancel : string (Alpine function)
     */
?>
<?php 
    $width = $params['width'] ?? 'lg';
    $callback_cancel = empty($params['callback_cancel']) ? '' : ($params['callback_cancel'] . '()');
?>
<template 
    x-teleport="body"
>
    <div
        class="relative z-10"
        x-data="{ id: '<?= $params['id'] ?>', open: false }" 
        x-show="open"
        x-on:alert.window="open = $event.detail.id == id"
    >
        <!-- BACKDROP -->
        <div 
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="opacity-0"
           x-transition:enter-end="opacity-100"
           x-transition:leave="transition ease-in duration-200"
           x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0"
           class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
           x-show="open"
       ></div>

        <!-- PANEL -->
        <div 
            class="fixed inset-0 z-10 overflow-y-auto"
        >
            <div 
                class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0"
                x-on:click.self="open = false; <?= $callback_cancel ?>"
            >
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-<?= $width ?>"
                    x-show="open"
                    x-init="$watch('open', () => { if(!open) return; setTimeout(() => { $el.focus(); }, 500) })"
                    x-on:keydown.escape="open = false; <?= $callback_cancel ?>"
                    tabindex="0"
                >
                    <div class="bg-white px-4 py-4 sm:p-6 sm:py-4 w-full">
                        <div class="sm:flex sm:items-start w-full">
                            <?= $params['content'] ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

