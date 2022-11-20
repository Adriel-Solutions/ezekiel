<?php
    $menu = [];
    $footer = [];

    fire_hook('before_print_menu', $menu);
    fire_hook('before_print_menu_footer', $footer);

    usort($menu, fn($a,$b) => $a['rank'] - $b['rank']);
?>

<!-- DESKTOP SIDE BAR -->
<div class="hidden lg:flex lg:flex-shrink-0">
    <div class="flex flex-col w-28">
        <div class="flex-1 flex flex-col min-h-0 overflow-y-auto bg-blue-700">
            <div class="flex-1 flex flex-col">

                <!-- LOGO -->
                <div 
                    class="h-16 flex items-center justify-center cursor-pointer"
                    data-href="<?= front_path('/dashboard') ?>"
                >
                    <img class="h-8 w-auto "src="<?= front_asset_path('/images/logo.png') ?>" alt="Logo" />
                </div>

                <!-- ACTUAL MENU -->
                <nav class="my-auto flex flex-col items-center space-y-1 px-2">
                    <?php foreach($menu as $route): ?>
                        <?php if ( !in_array($params['authenticated_user']['power'], $route['available_to']) ) continue; ?>
                        <a 
                            href="<?= $route['link'] ?>" 

                            <?php 
                                $is_active = false; 

                                if ( isset($route['should_exact_match'] ) && $route['should_exact_match'] )
                                    $is_active = ($route['link'] === $params['current_path']) || ( ($route['link'] . '/') === $params['current_path'] );
                                else
                                    $is_active = str_starts_with($params['current_path'], $route['link']); 
                            ?>

                            <?php if ( $is_active  ): ?>
                                class="text-blue-100 bg-blue-800 text-white group w-full p-3 rounded-md flex flex-col items-center text-xs font-medium"
                            <?php else: ?>
                                class="text-blue-100 hover:bg-blue-800 hover:text-white group w-full p-3 rounded-md flex flex-col items-center text-xs font-medium"
                            <?php endif; ?>
                        >
                            <div class="h-6 w-6 flex justify-center items-center">
                                <?= $route['icon'] ?>
                            </div>
                            <span class="mt-2"><?= __($route['label']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>

            </div>

            <!-- FOOTER -->
            <div class="flex pb-5 justify-center">
                <nav class="flex flex-col items-center space-y-1 px-2 w-full">
                    <?php foreach($footer as $route): ?>
                        <?php if ( !in_array($params['authenticated_user']['power'], $route['available_to']) ) continue; ?>
                        <a 
                            href="<?= $route['link'] ?>" 

                            <?php 
                                $is_active = false; 

                                if ( isset($route['should_exact_match'] ) && $route['should_exact_match'] )
                                    $is_active = ($route['link'] === $params['current_path']) || ( ($route['link'] . '/') === $params['current_path'] );
                                else
                                    $is_active = str_starts_with($params['current_path'], $route['link']); 
                            ?>

                            <?php if ( $is_active  ): ?>
                                class="text-blue-100 bg-blue-800 text-white group w-full p-3 rounded-md flex flex-col items-center text-xs font-medium"
                            <?php else: ?>
                                class="text-blue-100 hover:bg-blue-800 hover:text-white group w-full p-3 rounded-md flex flex-col items-center text-xs font-medium"
                            <?php endif; ?>
                        >
                            <div class="h-6 w-6 flex justify-center items-center">
                                <?= $route['icon'] ?>
                            </div>
                            <span class="mt-2"><?= __($route['label']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>
        </div>
    </div>
</div>
