<?php
    $menu = [
        [
            'link' => front_path('/dashboard'),
            'label' => __('Home'),
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"> <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /> </svg>',
            'available_to' => [ 'FAMILY_MANAGER' ],
            'should_exact_match' => true
        ],
        [
            'link' => front_path('/dashboard/family'),
            'label' => __('Family'),
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"> <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /> </svg>',
            'available_to' => [ 'FAMILY_MANAGER' ]
        ],
        [
            'link' => front_path('/dashboard/doctors'),
            'label' => __('Doctors'),
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"> <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /> </svg>',
            'available_to' => [ 'FAMILY_MANAGER' ]
        ],
        [
            'link' => front_path('/dashboard/drugs'),
            'label' => __('Drugs'),
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"> <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /> </svg>',
            'available_to' => [ 'FAMILY_MANAGER' ]
        ],
        [
            'link' => front_path('/dashboard/settings'),
            'label' => __('Settings'),
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"> <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /> <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /> </svg>',
            'available_to' => [ 'FAMILY_MANAGER' ]
        ]
    ];
?>
<!-- Off-canvas menu for mobile, show/hide based on off-canvas menu state. -->
<div class="relative z-40 lg:hidden" role="dialog">
    <!--
        Off-canvas menu backdrop, show/hide based on off-canvas menu state.

        Entering: "transition-opacity ease-linear duration-300"
        From: "opacity-0"
        To: "opacity-100"
        Leaving: "transition-opacity ease-linear duration-300"
        From: "opacity-100"
        To: "opacity-0"
    -->
    <div class="fixed inset-0 bg-slate-600 bg-opacity-75"></div>

    <div class="fixed inset-0 flex z-40">
        <!--
            Off-canvas menu, show/hide based on off-canvas menu state.

            Entering: "transition ease-in-out duration-300 transform"
            From: "-translate-x-full"
            To: "translate-x-0"
            Leaving: "transition ease-in-out duration-300 transform"
            From: "translate-x-0"
            To: "-translate-x-full"
        -->
        <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white focus:outline-none">
            <!--
                Close button, show/hide based on off-canvas menu state.

                Entering: "ease-in-out duration-300"
                From: "opacity-0"
                To: "opacity-100"
                Leaving: "ease-in-out duration-300"
                From: "opacity-100"
                To: "opacity-0"
            -->
            <div class="absolute top-0 right-0 -mr-12 pt-4">
                <button type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <span class="sr-only">Close sidebar</span>
                    <!-- Heroicon name: outline/x -->
                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="pt-5 pb-4">
                <div class="flex-shrink-0 flex items-center px-4">
                    <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/workflow-mark.svg?color=blue&shade=600" alt="Workflow">
                </div>
                <nav  class="mt-5">
                    <div class="px-2 space-y-1">
                        <a href="#" class="group p-2 rounded-md flex items-center text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                            <!-- Heroicon name: outline/home -->
                            <svg class="mr-4 h-6 w-6 text-slate-400 group-hover:text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Home
                        </a>

                        <a href="#" class="group p-2 rounded-md flex items-center text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                            <!-- Heroicon name: outline/fire -->
                            <svg class="mr-4 h-6 w-6 text-slate-400 group-hover:text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                            </svg>
                            Trending
                        </a>

                        <a href="#" class="group p-2 rounded-md flex items-center text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                            <!-- Heroicon name: outline/bookmark-alt -->
                            <svg class="mr-4 h-6 w-6 text-slate-400 group-hover:text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 4v12l-4-2-4 2V4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Bookmarks
                        </a>

                        <a href="#" class="group p-2 rounded-md flex items-center text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                            <!-- Heroicon name: outline/inbox -->
                            <svg class="mr-4 h-6 w-6 text-slate-400 group-hover:text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            Messages
                        </a>

                        <a href="#" class="group p-2 rounded-md flex items-center text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                            <!-- Heroicon name: outline/user -->
                            <svg class="mr-4 h-6 w-6 text-slate-400 group-hover:text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Profile
                        </a>
                    </div>
                </nav>
            </div>
            <div class="flex-shrink-0 flex border-t border-slate-200 p-4">
                <a href="#" class="flex-shrink-0 group block">
                    <div class="flex items-center">
                        <div>
                            <img class="inline-block h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1550525811-e5869dd03032?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2.5&w=256&h=256&q=80" alt="">
                        </div>
                        <div class="ml-3">
                            <p class="text-base font-medium text-slate-700 group-hover:text-slate-900">Lisa Marie</p>
                            <p class="text-sm font-medium text-slate-500 group-hover:text-slate-700">Account Settings</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="flex-shrink-0 w-14" >
            <!-- Force sidebar to shrink to fit close icon -->
        </div>
    </div>
</div>

<!-- DESKTOP SIDE BAR -->
<div class="hidden lg:flex lg:flex-shrink-0">
    <div class="flex flex-col w-24">
        <div class="flex-1 flex flex-col min-h-0 overflow-y-auto bg-teal-600">
            <div class="flex-1">

                <!-- LOGO -->
                <div 
                    class="bg-teal-700 py-4 flex items-center justify-center cursor-pointer"
                    data-href="<?= front_path('/dashboard') ?>"
                >
                    <img class="h-8 w-auto "src="<?= front_asset_path('/images/logo.png') ?>" alt="Logo" />
                </div>

                <!-- ACTUAL MENU -->
                <nav class="py-6 flex flex-col items-center space-y-3 px-2">
                    <?php foreach($menu as $route): ?>
                        <a 
                            href="<?= $route['link'] ?>" 

                            <?php 
                                $is_active = false; 

                                if ( isset($route['should_exact_match'] ) && $route['should_exact_match'] )
                                    $is_active = $route['link'] === $params['current_path'];
                                else
                                    $is_active = str_starts_with($params['current_path'], $route['link']); 
                            ?>

                            <?php if ( $is_active  ): ?>
                                class="text-teal-100 bg-teal-800 text-white group w-full p-3 rounded-md flex flex-col items-center text-xs font-medium"
                            <?php else: ?>
                                class="text-teal-100 hover:bg-teal-800 hover:text-white group w-full p-3 rounded-md flex flex-col items-center text-xs font-medium"
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
            <div class="flex-shrink-0 flex pb-5">
                <!-- AVATAR : PROFILE LINK -->
                <a 
                    href="<?= front_path('/dashboard/me') ?>" 
                    class="flex-shrink-0 w-full flex justify-center"
                >
                    <div class="rounded-full hover:ring-2 hover:ring-teal-700 p-0.5">
                        <?php HC('Avatar', [ 'size' => 10 ]); ?>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

        <!-- Mobile top navigation -->
        <div class="lg:hidden">
            <div class="bg-blue-600 py-2 px-4 flex items-center justify-between sm:px-6">
                <div>
                    <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/workflow-mark.svg?color=white" alt="Workflow">
                </div>
                <div>
                    <button type="button" class="-mr-3 h-12 w-12 inline-flex items-center justify-center bg-blue-600 rounded-md text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <span class="sr-only">Open sidebar</span>
                        <!-- Heroicon name: outline/menu -->
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
