<div class="min-h-full pt-16 pb-12 flex flex-col bg-white">
    <main class="flex-grow flex flex-col justify-center max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex-shrink-0 flex justify-center">
            <a href="<?= front_path('/') ?>" class="inline-flex">
                <?php 
                    HC(
                        'Image',
                        [
                            'class' => 'h-8 w-auto',
                            'src' => front_asset_path('/images/logo.png'),
                            'alt' => 'Logo'
                        ]
                    ); 
                ?>
            </a>
        </div>
        <div class="py-12">
            <div class="text-center">
                <p class="text-base font-semibold text-blue-600">
                    404
                </p>
                <h1 class="mt-2 text-4xl font-bold text-gray-900 tracking-tight sm:text-5xl">
                    <?= __('Page not found') ?>
                </h1>
                <p class="mt-4 text-base text-gray-500">
                    <?= ('Sorry, we couldn’t find the page you’re looking for') ?>
                </p>
                <div class="mt-6">
                    <?php HC('Link', [ 'href' => front_path('/') , 'text' => __('Go back home') . ' &rarr;' ]); ?>
                </div>
            </div>
        </div>
    </main>
</div>
