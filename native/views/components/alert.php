<?php
    /**
     * Name : Alert
     */

    /**
     * Parameters :
     * - id : string
     * - title : string
     * - subtitle : string
     * - text_confirm : string
     * - text_cancel : string
     * - is_cancelable : bool
     * - callback : string (Alpine action)
     * - input : string (PHP-generated HTML input)
     * - type : string
     */
?>
<?php
    $type = $params['type'] ?? 'information';
    switch($type) {
        case 'information':
            $class_icon_background = "bg-blue-100";
            $icon = '<svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /> </svg>';
            $class_button_primary = "bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2";
        break;

        case 'danger':
            $class_icon = "";
            $class_icon_background = "bg-red-100";
            $icon = '<svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v3.75m-9.303 3.376C1.83 19.126 2.914 21 4.645 21h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 4.88c-.866-1.501-3.032-1.501-3.898 0L2.697 17.626zM12 17.25h.007v.008H12v-.008z" /></svg>';
            $class_button_primary = "bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2";
        break;

    }
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
                <?php if ( isset($params['is_cancelable']) && $params['is_cancelable'] ): ?>
                    x-on:click.self="open = false"
                <?php endif; ?>
            >
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                    x-show="open"

                    <?php if ( empty($params['input']) ): ?>
                        x-init="$watch('open', () => { if(!open) return; setTimeout(() => { $el.focus(); }, 500) })"
                    <?php endif; ?>

                    <?php if ( !empty($params['input']) ): ?>
                        x-data="{ alertPayload: {  } , ready: false }"
                        x-init="$watch('alertPayload', () => { ready = <?= $params['rule'] ?>; })"
                    <?php endif; ?>

                    x-on:keydown.enter="$refs.submit_button.click()"
                    x-on:keydown.escape="$refs.cancel_button.click()"
                    tabindex="0"
                >
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 w-full">
                        <div class="sm:flex sm:items-start w-full">
                            <div class="<?= $class_icon_background ?> mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                <?= $icon ?>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title"><?= $params['title'] ?></h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500"><?= $params['subtitle'] ?></p>
                                </div>
                                <?php if ( !empty($params['input']) ): ?>
                                    <form 
                                        class="mt-2 w-full" 
                                        x-on:submit.prevent="$refs.submit_button.click()"
                                    >
                                        <?= $params['input'] ?>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- CONTROLS -->
                    <?php if ( empty($params['input']) ): ?>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button 
                                type="button" 
                                class="<?= $class_button_primary ?> inline-flex w-full justify-center rounded-md border border-transparent px-4 py-2 text-base font-medium text-white shadow-sm sm:ml-3 sm:w-auto sm:text-sm"
                                x-ref="submit_button"
                                x-on:click.prevent="<?= $params['callback'] ?>; open = false;"
                            >
                                <?= $params['text_confirm'] ?>
                            </button>
                            <?php if ( isset($params['is_cancelable']) && $params['is_cancelable'] ): ?>
                                <button 
                                    type="button" 
                                    class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                    x-ref="cancel_button"
                                    x-on:click.prevent="open = false"
                                >
                                    <?= $params['text_cancel'] ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <form 
                            class="m-0 bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6"
                            x-on:submit.prevent="<?= $params['callback'] ?>(alertPayload); open = false; setTimeout(() => { alertPayload = {}; }, 800)"
                        >
                            <div class="inline-flex w-full justify-center rounded-md border border-transparent text-base font-medium text-white shadow-sm sm:ml-3 sm:w-auto sm:text-sm">
                                <?php
                                    HNC(
                                        'FormButton',
                                        [
                                            'text' => $params['text_confirm'],
                                            'attributes' => [
                                                "x-ref" => "submit_button"
                                            ]
                                        ]
                                    );
                                ?>
                            </div>
                            <?php if ( isset($params['is_cancelable']) && $params['is_cancelable'] ): ?>
                                <button 
                                    type="button" 
                                    class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                    x-ref="cancel_button"
                                    x-on:click.prevent="open = false"
                                >
                                    <?= $params['text_cancel'] ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</template>
