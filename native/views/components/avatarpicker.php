<?php
    /**
     * Name : Avatar Picker
     */

    /**
     * Parameters :
     * - value : string
     * - size : int
     * - name : string ?????
     * - style : string (among : minimal | form (default) )
     * - submit_on_change : boolean
     * - endpoint : url
     * - use_s3 : boolean
     * - icon : string
     */
?>
<?php 
    $style = $params['style'] ?? 'form'; 
    $size = $params['size'] ?? 8;
    $avatar_class = $params['style'] === 'minimal' ? 'group group-hover cursor-pointer transition duration-300 relative' : '';
?>

<div 
    class="flex items-center"
    x-data="avatar_picker()"

    <?php if(!empty($params['value'])): ?>
        data-default="<?= $params['value'] ?>"
    <?php endif; ?>

    <?php if(!empty($params['endpoint'])): ?>
        data-endpoint="<?= $params['endpoint'] ?>"
    <?php endif; ?>

    <?php if( isset($params['submit_on_change']) && $params['submit_on_change'] ): ?>
        data-submit="true"
    <?php endif; ?>

    <?php if(isset($params['use_s3']) && $params['use_s3']): ?>
        data-s3="true"
    <?php endif; ?>
>
    <span 
        class="inline-block h-<?= $size ?> w-<?= $size ?> rounded-full overflow-hidden bg-gray-100 <?= $avatar_class ?>"
    >
        <!-- DISPLAY -->
        <template x-if="!avatar">
            <?php if ( !isset($params['icon']) ): ?>
                <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            <?php else: ?>
                <?= $params['icon'] ?>
            <?php endif; ?>
        </template>
        <template x-if="avatar">
            <img alt="Avatar" x-bind:src="avatar" />
        </template>

        <?php if ( $style === 'minimal' ): ?>
            <div 
                x-show="loading"
                class="bg-blue-500 absolute h-full transition duration-300 w-full top-0 flex items-center justify-center"
            >
                    <svg 
                        xmlns="http://www.w3.org/2000/svg" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        class="animate-spin h-5 w-5 text-white" 
                    >
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
            </div>

            <!-- CONTROLS -->
            <div 
                x-show="!loading"
                class="absolute h-full transition duration-300 w-full bg-gray-500 top-0 flex group-hover:opacity-100 opacity-0 flex-col"
            >
                <!-- TRIGGER -->
                <button 
                    class="hover:bg-blue-600 transition duration-300 focus:outline-0 flex-1 flex justify-center items-center outline-0 text-white bg-blue-500"
                    x-on:click="triggerFilePicker"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                      <path fill-rule="evenodd" d="M1 8a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 018.07 3h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0016.07 6H17a2 2 0 012 2v7a2 2 0 01-2 2H3a2 2 0 01-2-2V8zm13.5 3a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM10 14a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                    </svg>
                </button>

                <!-- CLEARER -->
                <button 
                    class="hover:bg-red-600 transition duration-300 focus:outline-0 flex-1 flex justify-center items-center outline-0 text-white bg-red-500"
                    x-on:click="clearPreview"
                    x-show="avatar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                      <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>
            </div>
        <?php endif; ?>
    </span>

    <input 
        type="file" 
        style="display:none" 
        name="avatar" 
        accept="image/png, image/jpg, image/jpeg"
        x-on:change="updatePreview(Object.values($event.target.files)[0])"
        x-ref="filePicker"
    />

    <?php if ( $style === 'form' ): ?>
        <button 
            type="button" 
            class="ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            x-on:click="triggerFilePicker"
        >
            <?= __('Modifier') ?>
        </button>
    <?php endif; ?>
</div>
