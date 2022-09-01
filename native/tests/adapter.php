<?php
    declare(strict_types=1);

    use PHPUnit\Framework\TestCase;

    require_once __DIR__ . '/../autoloader.php';

    use native\libs\Adapter;

    final class AdapterTest extends TestCase
    {
        private Adapter $adapter;

        protected function setUp() : void 
        {
            $this->adapter = new Adapter();
        }

        public function testExcluders() : void 
        {
            $input = [ 'a' => 30 , 'b' => 'hello' , 'c' => 0 ];
            $this->adapter->set_excluder('default', [ 'a' ,'b' ]);
            $this->adapter->set_excluder('specific', [ 'c' ]);

            $output = $input; $this->adapter->apply_one($output);
            $this->assertEquals(
                [ 'c' => 0 ],
                $output
            );

            $output = $input; $this->adapter->apply_one($output, 'specific');
            $this->assertEquals(
                [ 'a' => 30 , 'b' => 'hello' ],
                $output
            );
        }

        public function testMappers() : void 
        {
            $input = [ 'a' => 30 , 'b' => 'hello' , 'c' => 0 ];
            $this->adapter->set_mapper('default', [ 'a' => 'id' ,'b' => 'word' ]);
            $this->adapter->set_mapper('specific', [ 'c' => 'age' ]);

            $output = $input; $this->adapter->apply_one($output);
            $this->assertEquals(
                [ 'id' => 30 , 'word' => 'hello' , 'c' => 0 ],
                $output
            );

            $output = $input; $this->adapter->apply_one($output, 'specific');
            $this->assertEquals(
                [ 'a' => 30 , 'b' => 'hello' , 'age' => 0 ],
                $output
            );
        }

        public function testComputers() : void 
        {
            $input = [ 'a' => 30 , 'b' => 'hello' , 'c' => 0 ];
            $this->adapter->set_computer('default', [ 'a' => fn($arr) => $arr['a'] + 30 , 'b' => fn($arr) => $arr['b'] . ' world!' ]);
            $this->adapter->set_computer('specific', [ 'c' => fn($arr) => $arr['c'] ]);

            $output = $input; $this->adapter->apply_one($output);
            $this->assertEquals(
                [ 'a' => 60 , 'b' => 'hello world!' , 'c' => 0 ],
                $output
            );

            $output = $input; $this->adapter->apply_one($output, 'specific');
            $this->assertEquals(
                $input,
                $output
            );
        }

        public function testFull() : void
        {
            $input = [
                'pk' => 1,
                'secret' => 999,
                'firstname' => 'jimmy',
                'lastname' => 'hendrix',
                'fk_clone' => 2
            ];

            $excluder = [ 'fk_clone' , 'secret' ];
            $mapper = [ 'pk' => 'id' ];
            $computer = [ 
                'firstname' => fn($arr) => ucfirst($arr['firstname']),
                'lastname' => fn($arr) => ucfirst($arr['lastname']),
                'fullname' => fn($arr) => $arr['firstname'] . ' ' . $arr['lastname'],
            ];

            $this->adapter->set_excluder('default', $excluder);
            $this->adapter->set_mapper('default', $mapper);
            $this->adapter->set_computer('default', $computer);

            $output = $input; $this->adapter->apply_one($output);
            $this->assertEquals(
                [ 'id' => 1 , 'firstname' => 'Jimmy' , 'lastname' => 'Hendrix' , 'fullname' => 'Jimmy Hendrix' ],
                $output,
            );
        }
    }

