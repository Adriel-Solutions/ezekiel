<header class="w-full">
    <div class="relative z-10 flex-shrink-0 h-12 bg-white border-b flex">
        <div class="flex-1 flex justify-between px-4 sm:px-6">
            <!-- ITEMS -->
            <div class="flex items-center ml-auto space-x-4 sm:space-x-6">
                <?php if ( !empty($params['authenticated_user']) ): ?>
                    <?php
                        HNC(
                            'Avatar',
                            [
                                'size' => 8,
                                'url' => $params['authenticated_user']['avatar'] 
                                         ? front_upload_path($params['authenticated_user']['avatar'])
                                         : null,
                                'letters' => _e($params['authenticated_user']['fullname']),
                                'href' => front_path('/dashboard/me')
                            ]
                        );
                    ?>
                <?php endif;  ?>
            </div>
        </div>
    </div>
</header>
