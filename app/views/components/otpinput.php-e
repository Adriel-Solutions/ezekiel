<?php
    /**
     * Parameters :
     * - length : int
     * - name : string
     */
    $params['length'] = isset($params['length']) ? $params['length'] : 6;
?>
<div 
    class="flex align-center justify-center w-full space-x-5"
    x-data="component_otp(<?= $params['length'] ?>)"
    x-modelable="otp_value"
    x-model="payload.<?= $params['name'] ?>"
>
    <?php for($i = 0; $i < $params['length']; $i++): ?>
        <input 
            class="p-0 text-center appearance-none block w-10 h-10 border border-gray-300 rounded placeholder-gray-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-lg"
            type="text" 
            maxlength="1"
            placeholder="0"
            x-on:input="handleInput($event)"
            x-on:keydown.backspace="handleBackspace(<?= $i ?>)"
            x-on:paste="handlePaste($event)"
            x-ref="otp_input_<?= $i + 1 ?>"
        />
    <?php endfor; ?>
</div>

