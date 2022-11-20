<?php
    /**
     * Parameters :
     * - url : string
     * - type : string (internal / external)
     */
?>
<?php
    $type = $params['type'] ?? 'internal';
?>
<script
    type="application/javascript"

    <?php if ( 'internal' === $type ): ?>
        src="<?= front_asset_path('/scripts' . $params['url']) ?>"
    <?php endif; ?>
>

</script>
