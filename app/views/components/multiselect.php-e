<?php
    /**
     * Parameters :
     * - name : string
     * - values : array<string>
     * - default : string (the prompt)
     * - options : associative array
     * - label : string
     * - show_global_picker : boolean (whether to enable a one-select-all button or not)
     */
?>
<?php $params['id'] = uniqid(); ?>

<?php
    // Treat values for Javascript
    $params['values'] = !empty($params['values']) ? $params['values'] : [];
?>

<div
    class="w-full"
    x-data="{
        open: false,
        values: [ <?= join(',', array_map(function($v) { return "'$v'"; }, $params['values'])) ?> ],
        shadow: [ <?= join(',', array_map(function($v) { return "'$v'"; }, $params['values'])) ?> ],
        options: { <?= join(',', array_map(function($k, $v) { return "'$k':'$v'"; }, array_keys($params['options']), $params['options'])) ?> }
    }"
    x-on:click.away="open = false"
    x-modelable="shadow"
    x-model="payload.<?= $params['name'] ?>"
>

    <?php if ( !empty($params['label']) ): ?>
        <label
            for="<?= $params['id'] ?>"
            class="block text-sm font-medium text-gray-700"
        >
            <?= $params['label'] ?>
        </label>
    <?php endif; ?>

    <div class="mt-1 relative">
        <button
            type="button"
            class="bg-white relative w-full border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" aria-haspopup="listbox" aria-expanded="true" aria-labelledby="listbox-label"
            x-on:click="open = !open"
        >
            <!-- VALUES DISPLAY -->
            <span class="block truncate" x-text="values.length === 0 ? 'Sélectionner' : values.map(v => options[v]).join(', ')"></span>

            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </span>
        </button>

        <!-- POPOVER -->
        <ul
            class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
            x-show="open"
            x-transition:leave="transition duration-100 ease-in"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            tabindex="-1"
        >
            <?php if ( isset($params['show_global_picker']) && $params['show_global_picker'] === true ): ?>
                <!-- PICK-ALL PICKER -->
                <li
                    class="cursor-default select-none relative py-2 pl-3 pr-9 cursor-pointer"
                    x-data="{ highlighted: false }"
                    x-bind:class="{ 'text-white bg-indigo-600': highlighted, 'text-gray-900': !highlighted }"
                    x-on:mouseenter="highlighted = true"
                    x-on:mouseleave="highlighted = false"
                    x-on:click="
                        (() => {
                            if(values.length === 0)
                                values = Object.keys(options);
                            else 
                                values = [];

                            shadow = values;
                            open = false;
                        })();
                    "
                >
                    <!-- OPTION LABEL -->
                    <span
                        class="block truncate"
                        x-bind:class="{ 'font-semibold': highlighted, 'font-normal': !highlighted }"
                    >
                        Tout sélectionner / effacer
                    </span>
                </li>

            <?php endif; ?>

            <!-- ELEMENTS -->
            <?php foreach($params['options'] as $value => $label): ?>
                <li
                    class="cursor-default select-none relative py-2 pl-3 pr-9 cursor-pointer"
                    x-data="{ highlighted: false, selected: values.includes('<?= $value ?>') }"
                    x-bind:class="{ 'text-white bg-indigo-600': highlighted, 'text-gray-900': !highlighted }"
                    x-on:mouseenter="highlighted = true"
                    x-on:mouseleave="highlighted = false"
                    x-effect="selected = values.includes('<?= $value ?>')"
                    x-on:click="
                        (() => {
                            const idx = values.indexOf('<?= $value ?>');

                            if(idx === -1) values.push('<?= $value?>');
                            else values.splice(idx, 1);

                            shadow = values;

                            selected = values.includes('<?= $value ?>');

                            // Close the picker if there's only one option available
                            if (Object.keys(options).length === 1) open = false;
                        })();
                    "
                >
                    <!-- OPTION LABEL -->
                    <span
                        class="block truncate"
                        x-bind:class="{ 'font-semibold': highlighted, 'font-normal': !highlighted }"
                    >
                        <?= $label ?>
                    </span>

                    <!-- CHECKMARK -->
                    <span
                        class="absolute inset-y-0 right-0 flex items-center pr-4"
                        x-bind:class="{ 'text-white': highlighted, 'text-indigo-600': !highlighted }"
                        x-show="selected"
                    >
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- HIDDEN INPUT TO ENABLE DEFAULT VALUE LOADING + INJECTION INSIDE FORM PAYLOAD -->
    <?php if (!empty($params['values'])): ?>
        <input type="hidden" value="[<?= join(',', array_map(function($v) { return "'$v'"; }, $params['values'])) ?>]"  name="<?= $params['name'] ?>"/>
    <?php endif; ?>
</div>
