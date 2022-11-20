<?php
    /**
     * Name : Combo Box
     */

    /**
     * Parameters :
     * - name : string
     * - value : string
     * - default : string (the prompt)
     * - options : associative array
     * - label : string
     * - attributes : array
     * - free_input_enabled : true
     * - payload : string (Alpine payload object)
     * - is_clearable : boolean
     * - is_relational : boolean
     * - relation_field : string (Alpine variable)
     * - relations : array<key,array>
     * - native : boolean
     */
?>
<?php $params['id'] = uniqid(); ?>

<?php
    $params['value'] = !empty($params['value']) ? $params['value'] : null;
    $params['payload'] = !empty($params['payload']) ? $params['payload'] : 'payload';

    $is_relational = $params['is_relational'] ?? false;
    $relations = !empty($params['relations']) ? $params['relations'] : [];
    $relation_field = !empty($params['relation_field']) ? $params['relation_field'] : null;

    if($is_relational)
        $options = array_flatten(array_values($relations));
    else
        $options = empty($params['options']) ? [] : $params['options'];

    $is_native = isset($params['native']) && $params['native'] === true;

    // Prevent crash when supplied value is unknown
    if(!empty($params['value']))
        if(!in_array($params['value'], array_keys($options)))
            $params['value'] = '';
?>

<div
    class="w-full"
    x-data="{
        open: false,
        value: '<?= $params['value'] ?? '' ?>',
        options: { <?= join(',', array_map(function($k, $v) { return "'$k':'$v'"; }, array_keys($options), $options)) ?> },
        visibleOptions: {  },
        isRelational: <?= var_export($is_relational, true) ?>,

        <?php if ( $is_relational ): ?>
            relations:  <?= _e(json_encode($relations)) ?> ,
            oldRelationValue: '',
        <?php endif; ?>

        search: '',
        currentIndex: 0
    }"
    x-init="
        visibleOptions = options;
        <?php if ( !empty($params['value']) ): ?>
            setTimeout(() => { value = '<?= $params['value'] ?>' }, 50);
        <?php endif; ?>

        <?php if ( $is_relational ): ?>
            $watch('<?= $relation_field ?>', () => {
                $refs.clearer.click();

                setTimeout(() => {
                    <?php if ( !$is_native ): ?>
                        <?= $params['payload'] ?>.<?= $params['name'] ?> = 'a'; // Intentional random change
                    <?php endif; ?>

                    visibleOptions = !<?= $relation_field ?>
                                        ? options
                                        : relations[<?= $relation_field ?>];

                    <?php if ( !$is_native ): ?>
                        <?= $params['payload'] ?>.<?= $params['name'] ?> = '';
                    <?php endif; ?>
                }, 50);

            });
        <?php endif; ?>

        $watch('value', () => { $dispatch('change'); });
    "
    x-effect="
            visibleOptions = !search
                               ? options
                               : Object.entries(options)
                                .filter( (o) => o[1].toUpperCase().includes(search.toUpperCase()) )
                                .reduce( (a,b) => ({ ...a, [b[0]] : b[1] }) , {});
    "

    <?php if ( !$is_native ): ?>
        x-modelable="value"
        x-model="<?= $params['payload'] ?>.<?= $params['name'] ?>"
    <?php endif; ?>

    <?php if ( isset($params['attributes']) ): ?>
        <?php foreach($params['attributes'] as $k => $v): ?>
            <?php if ( $k !== 'x-on:change' ) continue; ?>
            <?= $k ?>="<?= $v ?>"
        <?php endforeach; ?>
    <?php endif; ?>
>

    <?php if ( !empty($params['label']) ): ?>
        <label
            for="<?= $params['id'] ?>"
            class="block text-sm font-medium text-gray-700"
        >
            <?= $params['label'] ?>
        </label>
    <?php endif; ?>

    <div class="relative">
        <button
            tabindex="0"
            type="button"
            class="cursor-pointer bg-white relative w-full border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2.5 text-left focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm disabled:bg-gray-100 disabled:pointer-events-none"
            x-on:click="open = true; $refs.display.value = $refs.display.value === '<?= $params['prompt'] ?>' ? '' : $refs.display.value; $refs.display.focus()"
            x-on:keyup.tab="open = true; $refs.display.value = $refs.display.value === '<?= $params['prompt'] ?>' ? '' : $refs.display.value; $refs.display.focus()"

            <?php if ( isset($params['free_input_enabled']) && $params['free_input_enabled'] === true ): ?>
                x-on:click.away="
                    if(!open) return;
                    open = false;
                "
                x-on:keydown.tab="
                    if(!open) return;
                    open = false;
                "
            <?php else: ?>
                x-on:click.away="
                    if(!open) return;

                    open = false;

                    $refs.display.value = 
                            $refs.display.value.length === 0 
                            ? !value
                                ? '<?= $params['prompt'] ?>' 
                                : Object.entries(options).find(o => o[0] == value)[1]
                            : Object.entries(options).find(o => o[0] == value)[1]
                "
                x-on:keydown.tab="
                    if(!open) return;

                    open = false;

                    $refs.display.value = 
                            $refs.display.value.length === 0 
                            ? !value
                                ? '<?= $params['prompt'] ?>' 
                                : Object.entries(options).find(o => o[0] == value)[1]
                            : Object.entries(options).find(o => o[0] == value)[1]
                "
            <?php endif; ?>

            <?php if ( isset($params['attributes']) ): ?>
                <?php foreach($params['attributes'] as $k => $v): ?>
                    <?php if ( $k === 'x-on:change' ) continue; ?>
                    <?= $k ?>="<?= $v ?>"
                <?php endforeach; ?>
            <?php endif; ?>
        >
            <!-- DISPLAY & INPUT -->
            <input
                class="w-full h-full border-0 outline-0 focus:outline-0 focus:border-0 bg-transparent"
                <?php if ( isset($params['free_input_enabled']) && $params['free_input_enabled'] === true): ?>
                    x-on:input="search = $el.value; setTimeout(() => { currentIndex = 0; }); value = $el.value;"
                <?php else: ?>
                    x-on:input="search = $el.value; setTimeout(() => { currentIndex = 0; })"
                <?php endif; ?>
                x-on:keyup.up="currentIndex = currentIndex > 0 ? currentIndex - 1 : 0"
                x-on:keyup.down="currentIndex = currentIndex >= Object.entries(visibleOptions).length - 1 ? currentIndex : currentIndex + 1"
                x-on:keydown.enter="
                    $event.preventDefault();
                    $event.stopPropagation();

                    if(Object.entries(visibleOptions).length === 0) {
                        document.body.click();
                        return;
                    }
                    $root.querySelector(`._option:nth-of-type(${currentIndex + 1})`).click();
                "
                x-ref="display"
                value="<?= !empty($params['value']) ? $params['options'][$params['value']] : $params['prompt'] ?>"
            />

            <!-- NATIVE FIELD FOR VALUE OUTSIDE OF ALPINE -->
            <?php if ( $is_native ): ?>
                <input
                    type="hidden",
                    name="<?= $params['name'] ?>"
                    x-bind:value="value"
                />
            <?php endif; ?>

            <!-- ARROWS -->
            <span 
                class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none"
                <?php if ( isset($params['is_clearable']) && $params['is_clearable'] === true ): ?>
                    x-show="!value"
                <?php endif; ?>
            >
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </span>

            <!-- CLEAR -->
            <?php if ( isset($params['is_clearable']) && $params['is_clearable'] === true ): ?>
                <span 
                    class="absolute inset-y-0 right-0 flex items-center pr-2"
                        x-show="value && value.length !== 0"
                        x-on:click.stop="$refs.display.value = '<?= $params['prompt'] ?>'; value = '';"
                        x-ref="clearer"
                >
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /> </svg>
                </span>
            <?php endif; ?>
        </button>

        <!-- POPOVER -->
        <ul
            class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-40 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
            x-show="open && Object.entries(visibleOptions).length > 0"
            x-transition:leave="transition duration-100 ease-in"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-effect="$el.scrollBy(0, currentIndex > 3 ? 50 : - 50)"
            tabindex="-1"
        >
            <template x-for="(option, index) in Object.entries(visibleOptions)" x-bind:key="`${index}_${option[0]}`">
                <li
                    class="_option select-none relative py-2 pl-3 pr-9 cursor-pointer"
                    x-data="{ selected: value == option[0] || currentIndex == index , index }"
                    x-bind:class="{ 'text-white bg-blue-600': selected, 'text-gray-900': !selected }"
                    x-on:mouseenter="currentIndex = index"
                    x-effect="selected = value == option[0] || currentIndex == index"
                    x-on:click="open = false; $refs.display.value = option[1]; value = option[0]; $refs.display.blur(); setTimeout(() => {search = '';}, 300)"
                    tabindex="-1"
                >
                    <!-- OPTION LABEL -->
                    <span
                        class="block truncate"
                        x-bind:class="{ 'font-semibold': selected , 'font-normal': !selected }"
                        x-text="option[1]"
                    >
                    </span>
                </li>
            </template>
        </ul>
    </div>
</div>

