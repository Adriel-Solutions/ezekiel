<?php
    /**
     * Parameters :
     * - length : int
     * - name : string
     * - callback : string (Alpine action)
     */
    $params['length'] = isset($params['length']) ? $params['length'] : 6;
?>
<div 
    class="flex align-center justify-between w-full"

    <?php if ( empty($params['callback']) ): ?>
        x-data="component_otpinput(<?= $params['length'] ?>)"
    <?php else: ?>
        x-data="component_otpinput(<?= $params['length'] ?>, <?= $params['callback'] ?>)"
    <?php endif; ?>
    x-modelable="otp_value"
    x-model="payload.<?= $params['name'] ?>"
    x-on:clear="clear"
>
    <?php for($i = 0; $i < $params['length']; $i++): ?>
        <input 
            class="p-0 text-center appearance-none block w-12 h-12 border border-gray-300 rounded placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-lg"
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

