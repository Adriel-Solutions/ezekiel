<?php
    /**
     * Parameters:
     * - toggle : string (Alpine variable to toggle on click
     * - text : string
     */
?>
<button
    type="button"
    class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 w-full"
    x-on:click="<?= $params['toggle'] ?> = !<?= $params['toggle'] ?>"
    x-text="!<?= $params['toggle'] ?> ? '<?= $params['text_initial'] ?> ' : '<?= $params['text_toggle'] ?>'"
>
</button>

