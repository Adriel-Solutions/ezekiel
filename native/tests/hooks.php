<?php
    declare(strict_types=1);

    use PHPUnit\Framework\TestCase;

    require_once __DIR__ . '/../autoloader.php';

    use native\libs\Hooks;

    final class HooksTest extends TestCase
    {
        public function testEverythingWorks() : void 
        {
            $local_var = '';

            Hooks::register('signal' , function(&$var) { $var = 'handler 1'; });
            Hooks::fire('signal', $local_var);

            $this->assertEquals($local_var, 'handler 1');

            $local_var = '';

            Hooks::register('signal' , function(&$var) { $var .= ' and handler 2'; });
            Hooks::fire('signal', $local_var);

            $this->assertEquals($local_var, 'handler 1 and handler 2');
        }
    }
