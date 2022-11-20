<?php
    /**
     * Name : Table
     */

    /**
     * Parameters :
     * - headers : array<string>
     * - rows : array<field, render, escape> 
     * - row_link_format : string (i.e. /my/path/:accessor/my/path)
     */
?>
<table class="min-w-full divide-y divide-gray-300">
    <thead class="bg-gray-50">
        <tr>
            <?php foreach($params['headers'] as $header): ?>
                <th class="uppercase py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-gray-500"><?= $header ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 bg-white">
        <?php foreach($params['rows'] as $index => $row): ?>
            <tr
                <?php if ( ($index + 1) % 2 === 0): ?>
                    class="bg-gray-50 hover:bg-gray-100 cursor-pointer"
                <?php else: ?>
                    class="hover:bg-gray-100 cursor-pointer"
                <?php endif; ?>
    
                <?php if ( !empty($params['row_link_format']) ): ?>
                    <?php 
                        $matches = []; 
                        preg_match('/:([a-zA-Z_-]+[^\/])/', $params['row_link_format'], $matches);
                        $accessor = array_find($params['accessors'], fn($a) => $a['field'] === $matches[1]) ;
                        $link = str_replace(':' . $matches[1], $row[$accessor['field']], $params['row_link_format']);
                    ?>
                    data-href="<?= $link ?>"
                <?php endif; ?>
            >
                <?php foreach($params['accessors'] as $accessor): ?>
                    <?php if ( isset($accessor['hidden']) && $accessor['hidden'] ) continue; ?>

                    <?php 
                        $cell_class = !empty($accessor['class']) ? $accessor['class'] : 'text-gray-500'; 
                        $cell_value = isset($accessor['render']) ? $accessor['render']($row) : $row[$accessor['field']];

                        if( isset($accessor['escape']) && $accessor['escape'] === true )
                            $cell_value = _e($cell_value);
                    ?>

                    <td class="<?= $cell_class ?> max-w-0 pl-4 py-4 pr-3 text-sm sm:max-w-none">
                        <?= $cell_value ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
