<?php
    /**
     * Name : Drop Zone
     */

    /**
     * Parameters :
     * - prompt : string
     * - show_button : boolean
     * - show_icon : boolean
     * - callback : string (Alpine action)
     * - accept : array<string>
     * - supports_multiple_files : boolean
     * - endpoint : string
     * - use_s3 : boolean
     */
?>
<div
    class="text-gray-500 flex-col flex justify-center items-center w-full h-full border-dashed border-2 rounded-md space-y-3 hover:border-gray-500 cursor-pointer duration-300"

    <?php if ( isset($params['supports_multiple_files']) && $params['supports_multiple_files'] === true ): ?>
        data-multiple="true"
    <?php endif; ?>

    <?php if ( isset($params['use_s3']) && $params['use_s3'] === true ): ?>
        data-s3="true"
    <?php endif; ?>


    <?php if ( isset($params['endpoint']) ): ?>
        data-endpoint="<?= $params['endpoint'] ?>"
    <?php endif; ?>

    data-accept="<?= _e(json_encode($params['accept'])) ?>"

    x-bind:class="{ 'hover:border-blue-500 border-blue-500 bg-gray-200 text-blue-500': dragging || loadingUpload }"
    x-data="component_dropzone(<?= $params['callback'] ?>)"
    x-on:click.stop="triggerFilePicker"
    x-on:dragenter.prevent="handleDragEnter"
    x-on:dragover.prevent="handledragOver"
    x-on:dragleave="handleDragLeave"
    x-on:drop="handleDrop"
>
    <?php if ( isset($params['show_icon']) && $params['show_icon'] === true ): ?>
        <svg x-show="!loadingUpload" class="h-12 w-12" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15M9 12l3 3m0 0l3-3m-3 3V2.25" /> </svg>
    <?php endif; ?>

    <template x-if="!dragging && !loadingUpload">
        <div class="flex flex-col items-center justify-center w-full space-y-3">
            <p class="text-lg text-center"><?= $params['prompt'] ?></p>

            <?php if ( isset($params['show_button']) && $params['show_button'] === true ): ?>
                <button
                    class="px-4 py-2 bg-gray-200 text-gray-500 border transition-border duration-300 hover:border hover:border-gray-300 hover:text-black"
                    x-on:click.stop="triggerFilePicker"
                >
                    Sélectionnez un fichier
                </button>
            <?php endif; ?>

            <?php if ( !empty($params['accept']) ): ?>
                <span
                    class="text-sm text-center text-gray-400"
                >
                    Formats acceptés :
                    <?php
                        
                        $parts = array_map( fn($f) => explode('/', $f), $params['accept'] );
                        $exts = array_map( fn($p) => $p[1] , $parts);
                        echo join(', ', $exts);
                    ?>
                </span>
            <?php endif; ?>
        </div>
    </template>
    <template x-if="dragging && !loadingUpload">
        <p class="text-lg text-center">Vous pouvez relâcher</p>
    </template>

    <?php if ( isset($params['supports_multiple_files']) && $params['supports_multiple_files'] === true ): ?>
        <template x-if="loadingUpload">
            <div class="w-full h-full flex flex-col items-center justify-center space-y-4">
                <?php if ( isset($params['show_icon']) && $params['show_icon'] === true ): ?>
                    <svg class="h-12 w-12" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15M9 12l3 3m0 0l3-3m-3 3V2.25" /> </svg>
                <?php endif; ?>

                <!-- PROGRESS BAR -->
                <div class="w-full bg-gray-200 rounded-md h-4 dark:bg-gray-700 w-9/12">
                    <div
                        class="bg-blue-600 h-full rounded-md duration-300"
                        x-bind:style="`width: ${progress}%`"
                    >
                    </div>
                </div>

            </div>
        </template>
    <?php else: ?>
        <template x-if="loadingUpload">
            <?php HNC('Loader'); ?>
        </template>
    <?php endif; ?>

    <input
        style="display:none"
        type="file"
        accept="<?= join(', ', $params['accept']) ?>"
        x-ref="filePicker"
        x-on:change="handleFilePickerChange(Object.values($event.target.files)[0])"
    />
</div>
