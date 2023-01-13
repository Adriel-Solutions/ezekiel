<?php

    namespace native\thirdparties;

use native\libs\Options;
use Parsedown;

    class Markdown {
        public static function to_html(string $markdown): string
        {
            $parser = Parsedown::instance();

            // Interpolate variables looking like %VAR%
            $variables = []; $matches = [];
            preg_match_all('/%([a-zA-Z_-]+)%/m', $markdown, $matches);
            if(!empty($matches)) {
                $variables = $matches[1];
                foreach($variables as $variable) {
                    $markdown = str_replace("%$variable%", Options::get($variable), $markdown);
                }
            }

            return $parser->text($markdown);
        }
    }

