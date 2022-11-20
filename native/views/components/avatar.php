<?php
    /**
     * Name : Avatar
     */

    /**
     * Parameters :
     * - url : string
     * - letters : string
     * - size : int
     * - href : string
     * - icon : string
     */
?>
<?php 
    $size = $params['size'] ?? 8; 
?>

<?php if ( !empty($params['href']) ): ?>
<a href="<?= $params['href'] ?>" class="rounded-full hover:ring-2 hover:ring-blue-500">
<?php endif; ?>
    <span 
        class="inline-flex items-center justify-center h-<?= $size ?> w-<?= $size ?> rounded-full overflow-hidden bg-gray-100"
    >
        <?php if(empty($params['url'])): ?>
            <?php if(isset($params['letters'])): ?>
                <span class="text-gray-300 text-xl text-uppercase">
                    <?php if ( str_contains($params['letters'], ' ') ): ?>
                        <?php $parts = explode(' ', $params['letters']); ?>
                        <?= $parts[0][0] . $parts[1][0] ?>
                    <?php else: ?>
                        <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    <?php endif; ?>
                </span>
            <?php else: ?>
                <?php if ( !isset($params['icon']) ): ?>
                    <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                <?php else: ?>
                    <?= $params['icon'] ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <img src="<?= $params['url'] ?>" alt="Avatar" />
        <?php endif; ?>
    </span>
<?php if ( !empty($params['href']) ): ?>
</a>
<?php endif; ?>
