        <!-- CHARSET -->
        <meta charset="utf8" />

        <!-- RESPONSIVE -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- TITLE -->
        <title><?= $title ?></title>

        <!-- DESCRIPTION -->
        <meta name="description" content="<?= $description ?>" />

        <!-- CSS RESET -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@0.6.4/dist/preflight.min.css" />

        <!-- DEFAULT CSS -->
        <link rel="stylesheet" href="<?= front_asset_path('/styles/style.min.css') ?>" />

        <?php if(!empty($styles)): ?>
            <!-- PAGE STYLES -->
            <?php foreach($styles as $style): ?>
                <link 
                    rel="stylesheet" 
                    type="text/css" 
                    <?php if(empty($params['module'])): ?>
                        href="<?= front_asset_path('/assets/styles/' . $style) ?>" 
                    <?php else: ?>
                        href="<?= front_module_asset_path($params['module'], '/assets/styles/' . $style) ?>" 
                    <?php endif; ?>
                />
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- DEFAULT JS -->
        <script type="application/javascript" src="<?= front_asset_path('/scripts/core.js') ?>"></script>

        <?php if(!empty($scripts)): ?>
            <!-- PAGE SCRIPTS -->
            <?php foreach($scripts as $script): ?>
                <script
                    type="application/javascript"
                    <?php if(empty($script['type']) || $script['type'] === 'internal'): ?>
                        <?php if(empty($params['module'])): ?>
                            src="<?= front_asset_path('/scripts' . $script['url']) ?>"
                        <?php else: ?>
                            src="<?= front_module_asset_path($params['module'], '/scripts' . $script['url']) ?>"
                        <?php endif; ?>
                    <?php elseif($script['type'] === 'external'): ?>
                        src="<?= $script['url'] ?>"
                    <?php elseif($script['type'] === 'component'): ?>
                        src="<?= front_asset_path('/scripts/components' . $script['url']) ?>"
                    <?php endif; ?>

                    <?php if(isset($script['loading'])): ?>
                        <?= $script['loading'] ?>
                    <?php endif; ?>
                >
                </script>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- TAILWIND -->
        <script type="application/javascript" src="https://cdn.tailwindcss.com/?plugins=forms"></script>

        <!-- Iodine -->
        <script type="application/javascript" src="<?= front_asset_path('/scripts/dependencies/iodine.min.js') ?>" defer></script>

        <!-- ALPINE -->
        <script type="application/javascript" src="<?= front_asset_path('/scripts/dependencies/alpine.min.js') ?>" defer></script>

        <!-- FAVICON -->
        <link rel="icon" type="image/png" href="<?= front_asset_path('/images/favicon.ico') ?>" />
