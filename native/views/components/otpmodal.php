<?php
    // Based on /native/components/alert.php

    /**
     * Parameters :
     * - id : string
     * - title : string
     * - subtitle : string
     * - is_cancelable : bool
     * - endpoint : string
     * - callback : string (Alpine action)
     */
?>
<?php
    $class_icon_background = "bg-blue-100";
    $icon = '<svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /> </svg>';
    $class_button_primary = "bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2";
    $text_confirm = "Vérifier";
    $text_cancel = "Annuler";
?>
<template 
    x-teleport="body"
>
    <div
        class="relative z-10"
        x-data="component_otpmodal('<?= $params['id'] ?>','<?= $params['endpoint'] ?>', <?= $params['callback'] ?>)"
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
                x-on:click.self="cancel"
            >
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl"
                    x-show="open"
                >
                    <!-- INFORMATION -->
                    <div x-show="!showOTPInput" class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="<?= $class_icon_background ?> mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                <?= $icon ?>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg font-medium leading-6 text-gray-900"><?= $params['title'] ?></h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500"><?= $params['subtitle'] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- OTP Panel -->
                    <div x-show="showOTPInput" class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-center justify-center w-full">
                            <div class="mt-3 text-center sm:mt-0 max-w-md w-full flex flex-col items-center">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Renseignez votre code</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">Vous avez reçu un code de vérification à usage unique par email</p>
                                </div>
                                <div class="w-full mt-3 max-w-sm flex justify-center flex-col space-y-6">
                                    <?php
                                        HNC(
                                            'OTPInput',
                                            [
                                                'length' => 6,
                                                'name' => 'otp',
                                                'callback' => 'submit'
                                            ]
                                        );

                                        HNC(
                                            'FormError',
                                            [
                                                'key' => 'error',
                                                'label' => 'Le code renseigné est invalide'
                                            ]
                                        );

                                        HNC(
                                            'FormSuccess',
                                            [
                                                'key' => 'success',
                                                'label' => 'Le code renseigné est valide'
                                            ]
                                        );
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CONTROLS -->
                    <div 
                        class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6"
                        x-bind:class="{ 'justify-center': showOTPInput }"
                    >
                        <?php
                            HNC(
                                'LoadableButton',
                                [
                                    'action' => 'requestOTP',
                                    'text' => $text_confirm,
                                    'attributes' => [
                                        'class' => "$class_button_primary inline-flex w-full justify-center rounded-md border border-transparent px-4 py-2 text-base font-medium text-white shadow-sm sm:ml-3 sm:w-auto sm:text-sm",
                                        'x-show' => "!showOTPInput"
                                    ]
                                ]
                            );

                            HNC(
                                'FormButton',
                                [
                                    'text' => 'Confirmer',
                                    'attributes' => [
                                        'class' => "$class_button_primary inline-flex w-full justify-center rounded-md border border-transparent px-4 py-2 text-base font-medium text-white shadow-sm sm:ml-3 sm:w-auto sm:text-sm disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200",
                                        'x-show' => "showOTPInput"
                                    ]
                                ]
                            );
                        ?>
                        <button 
                            type="button" 
                            class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            x-on:click="cancel"
                        >
                            <?= $text_cancel ?>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

