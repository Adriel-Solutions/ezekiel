<?php
    declare(strict_types=1);

    use PHPUnit\Framework\TestCase;

    require_once __DIR__ . '/../autoloader.php';
    require_once __DIR__ . '/../libs/sugar.php';

    use native\libs\I18n;

    final class I18nTest extends TestCase
    {
        public function testEverythingWorks() : void 
        {
            $this->assertEquals(
                'Hello',
                __('Hello')
            );

            $this->assertEquals(
                'Hello John',
                __('Hello %s', 'John')
            );

            $this->assertEquals(
                'Hello John and Cait',
                __('Hello %s and %s', 'John', 'Cait')
            );

            I18n::append_translation('ab-cd', [ 'Hello %s and %s' => 'I just saw %s and %s' ]);
            I18n::set_locale('ab-cd');

            $this->assertEquals(
                'I just saw John and Cait',
                __('Hello %s and %s', 'John', 'Cait')
            );
        }
    }

