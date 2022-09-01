<?php 
    declare(strict_types=1);

    use PHPUnit\Framework\TestCase;

    require_once __DIR__ . '/../autoloader.php';

    use native\libs\Arrays;

    final class ArraysTest extends TestCase
    {
        public function testGet() : void
        {
            $input = [ 
                'human' => [
                    'name' => 'Joe',
                    'age' => 30,
                    'spouse' => [
                        'name' => 'Jane',
                        'age' => 40,
                        'best-friend' => [
                            'name' => 'Chad',
                            'age' => 38
                        ]
                    ]
                ]
            ];

            $this->assertEquals(
                38,
                Arrays::get($input, 'human.spouse.best-friend.age')
            );

            $this->assertEquals(
                'Joe',
                Arrays::get($input, 'human.name')
            );

            $this->assertEquals(
                null,
                Arrays::get($input, 'human.spouse.best-friend.son')
            );
        }

        public function testAny() : void 
        {
            $this->assertEquals(
                true,
                Arrays::any([ 0 ], fn($e) => $e < 2),
            );

            $this->assertEquals(
                false,
                Arrays::any([ 2 ], fn($e) => $e < 2),
            );

            $this->assertEquals(
                true,
                Arrays::any([ 1 , 2 , 3 ], fn($e) => $e < 2),
            );

            $this->assertEquals(
                false,
                Arrays::any([], fn($e) => $e < 2),
            );
        }

        public function testAll() : void
        {
            $this->assertEquals(
                false,
                Arrays::all([ ], fn($e) => $e < 2),
            );

            $this->assertEquals(
                true,
                Arrays::all([ 1 ], fn($e) => $e < 2),
            );

            $this->assertEquals(
                false,
                Arrays::all([ 1 , 3 ], fn($e) => $e < 2),
            );

            $this->assertEquals(
                true,
                Arrays::all([ 3 , 6 , 9 ], fn($e) => $e % 3 === 0),
            );
        }

        public function testFind() : void
        {
            $items = [
                [
                    'name' => 'Joe',
                    'car' => 'red'
                ],
                [
                    'name' => 'Jane',
                    'car' => 'blue'
                ]
            ];

            $this->assertEqualsCanonicalizing(
                $items[0],
                Arrays::find($items, fn($i) => 'red' === $i['car'])
            );

            $this->assertEqualsCanonicalizing(
                $items[1],
                Arrays::find($items, fn($i) => 'blue' === $i['car'])
            );

            $this->assertEquals(
                "purple",
                Arrays::find($items, fn($i) => 'purple' === $i['car'], 'purple')
            );

            $this->assertEquals(
                "purple",
                Arrays::find([], fn($i) => 'purple' === $i['car'], 'purple')
            );
        }

        public function testIsMultidimensional() : void
        {
            $this->assertEquals(
                false,
                Arrays::is_multi([ 1 , 2 ,3 ])
            );

            $this->assertEquals(
                true,
                Arrays::is_multi(
                    [
                        [
                            'car' => 'red'
                        ]
                    ]
                )
            );

            $this->assertEquals(
                true,
                Arrays::is_multi(
                    [
                        [
                        ]
                    ]
                )
            );

            $this->assertEquals(
                false,
                Arrays::is_multi([])
            );

            $this->assertEquals(
                false,
                Arrays::is_multi([ 2 => [ 1 , 2 ] ])
            );

            $this->assertEquals(
                true,
                Arrays::is_multi([ 0 => [ 1 , 2 ] ])
            );
        }

        public function testIsAssociative() : void
        {
            $this->assertEquals(
                false,
                Arrays::is_associative([ 1 , 2 , 3 ])
            );

            $this->assertEquals(
                false,
                Arrays::is_associative([ 'a' , 'b' , 'c' ])
            );

            $this->assertEquals(
                false,
                Arrays::is_associative([])
            );

            $this->assertEquals(
                true,
                Arrays::is_associative([ 'a' => 'b' ])
            );
        }

        public function testWhitelist() : void
        {
            $array = [ 1 , 2 , 3 , 4 , 5 , 6 ];
            $dict = [ 'a' => 30 , 'b' => 60 , 'c' => [ '38' ] ];

            $this->assertEqualsCanonicalizing(
                [ 4 , 5 , 6  ],
                Arrays::whitelist($array, [ 4 , 5 , 6 ])
            );

            $this->assertEqualsCanonicalizing(
                [ 4 , 5 , 6  ],
                Arrays::whitelist($array, [ 6 , 5 , 4 ])
            );

            $this->assertEqualsCanonicalizing(
                [ 1 ],
                Arrays::whitelist($array, [ 1 ])
            );

            $this->assertEqualsCanonicalizing(
                [ ],
                Arrays::whitelist($array, [ ])
            );

            $this->assertEqualsCanonicalizing(
                [ 'a' => 30 ],
                Arrays::whitelist($dict, [ 'a' ])
            );

            $this->assertEqualsCanonicalizing(
                [  ],
                Arrays::whitelist($dict, [ ])
            );

            $this->assertEqualsCanonicalizing(
                [ 'a' => 30 , 'c' => [ '38' ] ],
                Arrays::whitelist($dict, [ 'a' , 'c'])
            );
        }

        public function testBlacklist() : void
        {
            $array = [ 1 , 2 , 3 , 4 , 5 , 6 ];
            $dict = [ 'a' => 30 , 'b' => 60 , 'c' => [ '38' ] ];

            $this->assertEqualsCanonicalizing(
                [ 1 , 2 , 3  ],
                Arrays::blacklist($array, [ 4 , 5 , 6 ])
            );

            $this->assertEqualsCanonicalizing(
                [ 1 ],
                Arrays::blacklist($array, [ 2 , 3 , 4 , 5 , 6 ])
            );

            $this->assertEqualsCanonicalizing(
                [ ],
                Arrays::blacklist($array, [ 1 , 2 , 3 , 4 , 5 , 6 ])
            );

            $this->assertEqualsCanonicalizing(
                [  ],
                Arrays::blacklist($dict, [ 'a' , 'b' , 'c' ])
            );

            $this->assertEqualsCanonicalizing(
                $dict,
                Arrays::blacklist($dict, [ ])
            );

            $this->assertEqualsCanonicalizing(
                [ 'a' => 30 ],
                Arrays::blacklist($dict, [ 'b' , 'c' ])
            );

            $this->assertEqualsCanonicalizing(
                [ 'c' => [ '38' ] ],
                Arrays::blacklist($dict, [ 'a' , 'b' ])
            );
        }

        public function testCombine() : void
        {
            $array = [
                [
                    'a' => 30,
                    'b' => 'Italy'
                ],
                [
                    'a' => 31,
                    'b' => 'Japan'
                ],
                [
                    'a' => 32,
                    'b' => 'Germany'
                ]
            ];

            $this->assertEqualsCanonicalizing(
                [ 30 => 'Italy' , 31 => 'Japan' , 32 => 'Germany' ],
                Arrays::combine($array, 'a' , 'b')
            );
        }
    }
