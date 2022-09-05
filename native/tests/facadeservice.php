<?php
    declare(strict_types=1);

    use native\facades\Service;
    use native\libs\Options;
    use native\libs\Database;
    use PHPUnit\Framework\TestCase;

    require_once __DIR__ . '/../autoloader.php';

    class TestsParents extends Service {  }
    class TestsPets extends Service {  }
    class TestsChildren extends Service {
        protected array $relations = [
            'parent_1' => [ 'type' => 'ONE-TO-ONE' , 'table' => 'tests_parents' , 'local_column' => 'fk_parent_1' ],
            'parent_2' => [ 'type' => 'ONE-TO-ONE' , 'table' => 'tests_parents' , 'local_column' => 'fk_parent_2' ],
            'friends' => [ 'type' => 'MANY-TO-MANY' , 'table' => 'tests_children' ,  'dictionary' => 'tests_children_friends' , 'local_column' => 'fk_child_1' , 'foreign_column' => 'fk_child_2' ],
            'pets' => [ 'type' => 'MANY-TO-ONE' , 'table' => 'tests_pets' ,  'foreign_column' => 'fk_child' ]
        ];
    }
    class TestsChildrenFriends extends Service {  }


    final class FacadeServiceTest extends TestCase
    {
        public static function setUpBeforeClass() : void 
        {
            // Retrieve configuration
            Options::load(__DIR__ . '/../../configuration/.custom.env');

            // Connect to database
            Database::load();

            // Set up tests structure
            if(!Options::get('ENCRYPTION_ENABLED')) {
                Database::query(
                    'CREATE TABLE IF NOT EXISTS tests_parents ( 
                        pk INT GENERATED ALWAYS AS IDENTITY,
                        name TEXT NOT NULL,
                        age INT NOT NULL,

                        PRIMARY KEY (pk)
                    )'
                );

                Database::query(
                    'CREATE TABLE IF NOT EXISTS tests_children ( 
                        pk INT GENERATED ALWAYS AS IDENTITY,
                        name TEXT NOT NULL,
                        age INT NOT NULL,

                        fk_parent_1 INT NOT NULL,
                        fk_parent_2 INT NULL DEFAULT NULL,

                        PRIMARY KEY (pk),
                        FOREIGN KEY (fk_parent_1) REFERENCES tests_parents (pk),
                        FOREIGN KEY (fk_parent_2) REFERENCES tests_parents (pk)
                    )'
                );

                Database::query(
                    'CREATE TABLE IF NOT EXISTS tests_children_friends ( 
                        pk INT GENERATED ALWAYS AS IDENTITY,
                        fk_child_1 INT NOT NULL,
                        fk_child_2 INT NOT NULL,

                        PRIMARY KEY (pk),
                        FOREIGN KEY (fk_child_1) REFERENCES tests_children (pk) ON DELETE CASCADE,
                        FOREIGN KEY (fk_child_2) REFERENCES tests_children (pk) ON DELETE CASCADE
                    )'
                );

                Database::query(
                    'CREATE TABLE IF NOT EXISTS tests_pets ( 
                        pk INT GENERATED ALWAYS AS IDENTITY,
                        name TEXT NOT NULL,
                        fk_child INT NOT NULL,

                        PRIMARY KEY (pk),
                        FOREIGN KEY (fk_child) REFERENCES tests_children (pk) ON DELETE CASCADE
                    )'
                );
            } else {
                Database::query(
                    'CREATE TABLE IF NOT EXISTS tests_parents ( 
                        pk BYTEA NOT NULL,
                        name BYTEA NOT NULL,
                        age BYTEA NOT NULL,

                        PRIMARY KEY (pk)
                    )'
                );

                Database::query(
                    'CREATE TABLE IF NOT EXISTS tests_children ( 
                        pk BYTEA NOT NULL,
                        name BYTEA NOT NULL,
                        age BYTEA NOT NULL,

                        fk_parent_1 BYTEA NOT NULL,
                        fk_parent_2 BYTEA NULL DEFAULT NULL,

                        PRIMARY KEY (pk)
                    )'
                );

                Database::query(
                    'CREATE TABLE IF NOT EXISTS tests_children_friends ( 
                        pk BYTEA,
                        fk_child_1 BYTEA NOT NULL,
                        fk_child_2 BYTEA NOT NULL,

                        PRIMARY KEY (pk)
                    )'
                );

                Database::query(
                    'CREATE TABLE IF NOT EXISTS tests_pets ( 
                        pk BYTEA NOT NULL,
                        name BYTEA NOT NULL,
                        fk_child BYTEA NOT NULL,

                        PRIMARY KEY (pk)
                    )'
                );
            }
        }

        protected function setUp() : void 
        {
            // Fill with data
            if(!Options::get('ENCRYPTION_ENABLED')) {
                Database::query("DROP EXTENSION IF EXISTS pgcrypto;");

                Database::query("INSERT INTO tests_parents ( name , age ) VALUES ( 'Joe' , 30  )");
                Database::query("INSERT INTO tests_parents ( name , age ) VALUES ( 'Jane' , 34  )");
                Database::query("INSERT INTO tests_parents ( name , age ) VALUES ( 'Paul' , 48  )");

                Database::query("INSERT INTO tests_children ( name , age , fk_parent_1 , fk_parent_2 ) VALUES ( 'Harry' , 2 , 1 , 2)");
                Database::query("INSERT INTO tests_children ( name , age , fk_parent_1 , fk_parent_2 ) VALUES ( 'Ron' , 4 , 1 , 2)");
                Database::query("INSERT INTO tests_children ( name , age , fk_parent_1 ) VALUES ( 'Emma' , 4 , 1 )");

                Database::query("INSERT INTO tests_children_friends ( fk_child_1, fk_child_2 ) VALUES ( 1 , 2 )");
                Database::query("INSERT INTO tests_children_friends ( fk_child_1, fk_child_2 ) VALUES ( 1 , 3 )");

                Database::query("INSERT INTO tests_pets ( name , fk_child ) VALUES ( 'Dogo' , 1 )");
                Database::query("INSERT INTO tests_pets ( name , fk_child ) VALUES ( 'Dugu' , 1 )");
            } else {
                $key = Options::get('ENCRYPTION_KEY');

                Database::query("CREATE EXTENSION IF NOT EXISTS pgcrypto;");

                Database::query("INSERT INTO tests_parents ( pk , name , age ) VALUES ( pgp_sym_encrypt( 1::text , '$key' ), pgp_sym_encrypt( 'Joe'  , '$key'  ), pgp_sym_encrypt( 30::text , '$key' ) )");
                Database::query("INSERT INTO tests_parents ( pk , name , age ) VALUES ( pgp_sym_encrypt( 2::text , '$key' ), pgp_sym_encrypt( 'Jane' , '$key'  ), pgp_sym_encrypt( 34::text , '$key' ) )");
                Database::query("INSERT INTO tests_parents ( pk , name , age ) VALUES ( pgp_sym_encrypt( 3::text , '$key' ), pgp_sym_encrypt( 'Paul' , '$key'  ), pgp_sym_encrypt( 48::text , '$key' ) )");

                Database::query("INSERT INTO tests_children ( pk , name , age , fk_parent_1 , fk_parent_2 ) VALUES ( pgp_sym_encrypt( 1::text , '$key' ), pgp_sym_encrypt( 'Harry' , '$key' ), pgp_sym_encrypt( 2::text , '$key' ) , pgp_sym_encrypt( 1::text , '$key' ) , pgp_sym_encrypt( 2::text , '$key' ))");
                Database::query("INSERT INTO tests_children ( pk , name , age , fk_parent_1 , fk_parent_2 ) VALUES ( pgp_sym_encrypt( 2::text , '$key' ), pgp_sym_encrypt( 'Ron' , '$key' ), pgp_sym_encrypt( 4::text , '$key' ) , pgp_sym_encrypt( 1::text , '$key' ) , pgp_sym_encrypt( 2::text , '$key' ))");
                Database::query("INSERT INTO tests_children ( pk , name , age , fk_parent_1 ) VALUES ( pgp_sym_encrypt( 3::text , '$key' ), pgp_sym_encrypt( 'Emma' , '$key' ), pgp_sym_encrypt( 4::text , '$key' ) , pgp_sym_encrypt( 1::text , '$key' ) )");

                Database::query("INSERT INTO tests_children_friends ( pk , fk_child_1, fk_child_2 ) VALUES ( pgp_sym_encrypt( 1::text , '$key' ), pgp_sym_encrypt( 1::text , '$key' ) , pgp_sym_encrypt( 2::text , '$key' ) )");
                Database::query("INSERT INTO tests_children_friends ( pk , fk_child_1, fk_child_2 ) VALUES ( pgp_sym_encrypt( 2::text , '$key' ), pgp_sym_encrypt( 1::text , '$key' ) , pgp_sym_encrypt( 3::text , '$key' ) )");

                Database::query("INSERT INTO tests_pets ( pk , name , fk_child ) VALUES ( pgp_sym_encrypt( 1::text , '$key' ), pgp_sym_encrypt( 'Dogo' , '$key' ), pgp_sym_encrypt( 1::text , '$key' ) )");
                Database::query("INSERT INTO tests_pets ( pk , name , fk_child ) VALUES ( pgp_sym_encrypt( 2::text , '$key' ), pgp_sym_encrypt( 'Dugu' , '$key' ), pgp_sym_encrypt( 1::text , '$key' ) )");
            }
        }

        protected function tearDown() : void 
        {
            // Clear tables
            Database::query("TRUNCATE tests_pets RESTART IDENTITY CASCADE");
            Database::query("TRUNCATE tests_children_friends RESTART IDENTITY CASCADE");
            Database::query("TRUNCATE tests_children RESTART IDENTITY CASCADE");
            Database::query("TRUNCATE tests_parents RESTART IDENTITY CASCADE");
        }

        public static function tearDownAfterClass() : void 
        {
            // Remove tables completely
            Database::query('DROP TABLE tests_pets');
            Database::query('DROP TABLE tests_children_friends');
            Database::query('DROP TABLE tests_children');
            Database::query('DROP TABLE tests_parents');
        }

        public function testExists() : void
        {
            $this->assertTrue(TestsParents::exists(1));
            $this->assertTrue(TestsParents::exists(2));
            $this->assertFalse(TestsParents::exists(4));
        }

        public function testExistsOne() : void
        {
            $this->assertTrue(TestsParents::exists_one([ 'name' => 'Joe' ]));

            $this->assertTrue(
                TestsParents::exists_one([
                    [
                        'column' => 'name',
                        'operator' => '=',
                        'value' => 'Joe'
                    ]
                ])
            );

            $this->assertTrue(
                TestsParents::exists_one([
                    [
                        'column' => 'name',
                        'operator' => 'IN',
                        'value' => [ 'Rick' , 'Jane' ]
                    ]
                ])
            );

            $this->assertTrue(
                TestsParents::exists_one([
                    [
                        'column' => 'name',
                        'operator' => 'LIKE',
                        'value' => 'J%'
                    ]
                ])
            );
        }

        public function testGet() : void
        {
            $this->assertEquals(
                [ 'pk' => 1 , 'name' => 'Joe' , 'age' => 30 ],
                TestsParents::get('1')
            );

            $this->assertEquals(
                [ 'pk' => 1 , 'name' => 'Joe' , 'age' => 30 ],
                TestsParents::get(1)
            );
        }

        public function testGetCount() : void
        {
            $this->assertEquals(
                3,
                TestsParents::get_count()
            );
        }

        public function testGetAll() : void
        {
            $this->assertEquals(
                [
                    [
                        'pk' => 1,
                        'name' => 'Joe',
                        'age' => 30
                    ],
                    [
                        'pk' => 2,
                        'name' => 'Jane',
                        'age' => 34
                    ],
                    [
                        'pk' => 3,
                        'name' => 'Paul',
                        'age' => 48
                    ],
                ],
                TestsParents::get_all()
            );

            $this->assertEquals(
                [
                    [
                        'pk' => 3,
                        'name' => 'Paul',
                        'age' => 48
                    ],
                    [
                        'pk' => 2,
                        'name' => 'Jane',
                        'age' => 34
                    ],
                    [
                        'pk' => 1,
                        'name' => 'Joe',
                        'age' => 30
                    ],
                ],
                TestsParents::get_all([ 'order' => [ 'age' => 'DESC' ] ])
            );

            $this->assertEquals(
                [
                    [
                        'pk' => 3,
                        'name' => 'Paul',
                        'age' => 48
                    ],
                ],
                TestsParents::get_all([  'per_page' => 1 , 'page' => 1,  'order' => [ 'age' => 'DESC' ] ])
            );
        }

        public function testDelete() : void
        {
            TestsChildren::delete(1);
            $this->assertEquals(
                2,
                TestsChildren::get_count()
            );
        }

        public function testUpdate() : void
        {
            $this->assertEquals(
                [ 'pk' => 1 , 'name' => 'Lord Voldemort', 'age' => 2, 'fk_parent_1' => 1, 'fk_parent_2' => 2 ],
                TestsChildren::update(1, [ 'name' => 'Lord Voldemort' ])
            );

            if(!Options::get('ENCRYPTION_ENABLED'))
                $this->assertEquals(
                    [ 'pk' => 1 , 'name' => 'Lord Voldemort', 'age' => 18, 'fk_parent_1' => 1, 'fk_parent_2' => 2 ],
                    TestsChildren::update(1, [ 'age' => '[age + 16]' ])
                );
            else  {
                $this->expectException(Exception::class);
                TestsChildren::update(1, [ 'age' => '[age + 16]' ]);
            }
        }

        public function testFindAndDelete() : void
        {
            TestsChildren::find_and_delete([ 'name' => 'Harry' ]);

            $this->assertEquals(
                2,
                TestsChildren::get_count()
            );

            if(Options::get('ENCRYPTION_ENABLED'))
                $this->expectException(Exception::class);

            TestsChildren::find_and_delete(
                [ 
                    [
                        'column' => '[LENGTH(name)]',
                        'operator' => '>',
                        'alias' => 'name',
                        'value' => '1'
                    ]
                ]
            );

            $this->assertEquals(
                0,
                TestsChildren::get_count()
            );
        }

        public function testFindOne() : void
        {
            $this->assertEquals(
                [
                    'pk' => 1,
                    'name' => 'Harry',
                    'age' => 2,
                    'fk_parent_1' => 1,
                    'fk_parent_2' => 2
                ],
                TestsChildren::find_one([])
            );

            $this->assertEquals(
                [
                    'pk' => 1,
                    'name' => 'Harry',
                    'age' => 2,
                    'fk_parent_1' => 1,
                    'fk_parent_2' => 2
                ],
                TestsChildren::find_one([ 'age' => 2 ])
            );

            $this->assertEquals(
                [
                    'pk' => 1,
                    'name' => 'Harry',
                    'age' => 2,
                    'fk_parent_1' => 1,
                    'fk_parent_2' => 2
                ],
                TestsChildren::find_one(
                    [
                        [
                            'column' => 'age',
                            'operator' => '=',
                            'value' => 48
                        ],
                        [
                            'column' => 'age',
                            'operator' => '=',
                            'value' => 49
                        ],
                        [
                            'column' => 'age',
                            'operator' => '=',
                            'value' => 2
                        ]
                    ],
                    false
                )
            );
        }

        public function testFindAll() : void
        {
            $this->assertCount(
                3,
                TestsParents::find_all([])
            );

            $this->assertCount(
                1,
                TestsParents::find_all([], [ 'per_page' => 1 , 'page' => 1 ])
            );

            $this->assertCount(
                2,
                TestsParents::find_all([], [ 'per_page' => 2 , 'page' => 1 ])
            );

            $this->assertCount(
                1,
                TestsParents::find_all([], [ 'per_page' => 2 , 'page' => 2 ])
            );

            $this->assertEquals(
                [ [ 'pk' => 1 , 'name' => 'Joe' , 'age' => 30 ] ],
                TestsParents::find_all([], [ 'per_page' => 1 , 'page' => 1 , 'order' => [ 'pk' => 'ASC' ] ])
            );

            $this->assertEquals(
                [ [ 'pk' => 3 , 'name' => 'Paul' , 'age' => 48 ] ],
                TestsParents::find_all([], [ 'per_page' => 1 , 'page' => 1 , 'order' => [ 'pk' => 'DESC' ] ])
            );
        }

        public function testFindCount() : void
        {
            $this->assertEquals(
                3,
                TestsParents::find_count([])
            );

            $this->assertEquals(
                1,
                TestsParents::find_count([ 'pk' => 1 ])
            );

            $this->assertEquals(
                3,
                TestsParents::find_count([ [ 'column' => 'age' , 'operator' => '>' , 'value' => 1 ] ])
            );

            $this->assertEquals(
                0,
                TestsParents::find_count([ [ 'column' => 'pk' , 'value' => 1 ], [ 'column' => 'pk' , 'value' => 2 ] ])
            );
        }

        public function testGetMany() : void
        {
            $this->assertEquals(
                [ 'pk' => 1 , 'name' => 'Joe' , 'age' => 30 ],
                TestsParents::get_many([ 'order' => [ 'pk' => 'ASC' ] ])[0]
            );

            $this->assertEquals(
                [ 'pk' => 3 , 'name' => 'Paul' , 'age' => 48 ],
                TestsParents::get_many([ 'order' => [ 'pk' => 'DESC' ] ])[0]
            );

            $this->assertEquals(
                [ 'pk' => 2 , 'name' => 'Jane' , 'age' => 34 ],
                TestsParents::get_many([  'page' => 2 ,  'per_page' => 1, 'order' => [ 'pk' => 'DESC' ] ])[0]
            );

            $this->assertCount(
                1,
                TestsParents::get_many([ 'page' => 1 ,  'per_page' => 1 ])
            );

            $this->assertCount(
                1,
                TestsParents::get_many([ 'page' => 3 ,  'per_page' => 1 ])
            );
        }

        public function testPopulate() : void
        {
            $child = TestsChildren::get(1);
            TestsChildren::populate($child, 'parent_1');
            TestsChildren::populate($child, 'parent_2');
            TestsChildren::populate($child, 'friends');
            TestsChildren::populate($child, 'pets');

            $this->assertEquals(
                [ 'pk' => 1 , 'name' => 'Joe' , 'age' => 30 ],
                $child['parent_1']
            );

            $this->assertEquals(
                [ 'pk' => 2 , 'name' => 'Jane' , 'age' => 34 ],
                $child['parent_2']
            );

            if(!Options::get('ENCRYPTION_ENABLED')) {
                $this->assertContains(
                    [ 'pk' => 2 , 'name' => 'Ron' , 'age' => 4 , 'fk_parent_1' => 1 , 'fk_parent_2' => 2 ],
                    $child['friends']
                );

                $this->assertContains(
                    [ 'pk' => 3 , 'name' => 'Emma' , 'age' => 4 , 'fk_parent_1' => 1 , 'fk_parent_2' => NULL ],
                    $child['friends']
                );

                $this->assertContains(
                    [ 'pk' => 1 , 'name' => 'Dogo' , 'fk_child' => 1 ],
                    $child['pets']
                );

                $this->assertContains(
                    [ 'pk' => 2 , 'name' => 'Dugu' , 'fk_child' => 1 ],
                    $child['pets']
                );
            }
            else {
                $this->assertContains(
                    [ 'pk' => "2" , 'name' => 'Ron' , 'age' => "4" , 'fk_parent_1' => "1" , 'fk_parent_2' => "2" ],
                    $child['friends']
                );

                $this->assertContains(
                    [ 'pk' => "3" , 'name' => 'Emma' , 'age' => "4" , 'fk_parent_1' => "1" , 'fk_parent_2' => NULL ],
                    $child['friends']
                );

                $this->assertContains(
                    [ 'pk' => "1" , 'name' => 'Dogo' , 'fk_child' => "1" ],
                    $child['pets']
                );

                $this->assertContains(
                    [ 'pk' => "2" , 'name' => 'Dugu' , 'fk_child' => "1" ],
                    $child['pets']
                );
            }
        }

        public function testFindAndUpdate() : void
        {
            $this->assertEquals(
                [ [ 'pk' => 1 , 'name' => 'Merlin' , 'age' => 30 ] ],
                TestsParents::find_and_update([ 'pk' => 1 ], [ 'name' => 'Merlin' ])
            );

            $this->assertEquals(
                [ [ 'pk' => 2 , 'name' => 'Eva' , 'age' => 60 ] ],
                TestsParents::find_and_update([ 'pk' => 2 ], [ 'name' => 'Eva' , 'age' => 60 ])
            );

            $this->assertEquals(
                [ [ 'pk' => 1 , 'name' => 'Zombie' , 'age' => 100 ] , [ 'pk' => 2 , 'name' => 'Zombie' , 'age' => 100 ] ],
                TestsParents::find_and_update([ 'pk' => [ 1 , 2 ] ], [ 'name' => 'Zombie' , 'age' => 100 ])
            );

            if(!Options::get('ENCRYPTION_ENABLED'))
                $this->assertEquals(
                    [ [ 'pk' => 1 , 'name' => 'Zombie' , 'age' => 200 ] , [ 'pk' => 2 , 'name' => 'Zombie' , 'age' => 200 ] ],
                    TestsParents::find_and_update([ 'pk' => [ 1 , 2 ] ], [ 'age' => '[age + 100]' ])
                );
            else {
                $this->expectException(Exception::class);
                TestsParents::find_and_update([ 'pk' => [ 1 , 2 ] ], [ 'age' => '[age + 100]' ]);
            }
        }

        public function testFindMany() : void
        {
            $this->assertCount(
                3,
                TestsParents::find_many([])
            );

            $this->assertEquals(
                [ [ 'pk' => 1 , 'name' => 'Joe' , 'age' => 30 ] ],
                TestsParents::find_many([ 'pk' => 1 ])
            );

            $this->assertEquals(
                [ [ 'pk' => 1 , 'name' => 'Joe' , 'age' => 30 ] , [ 'pk' => 2 , 'name' => 'Jane' , 'age' => 34] ],
                TestsParents::find_many([ 'pk' => [ 1 , 2 ] ])
            );

            $this->assertEquals(
                [ [ 'pk' => 1 , 'name' => 'Joe' , 'age' => 30 ] ],
                TestsParents::find_many([ 'pk' => [ 1 , 2 ] ], [ 'page' => 1 , 'per_page' => 1 ])
            );

            $this->assertEquals(
                [ [ 'pk' => 2 , 'name' => 'Jane' , 'age' => 34 ] ],
                TestsParents::find_many([ 'pk' => [ 1 , 2 ] ], [ 'page' => 1 , 'per_page' => 1, 'order' => [ 'pk' => 'DESC' ] ])
            );

            $this->assertEquals(
                [ [ 'pk' => 2 , 'name' => 'Jane' , 'age' => 34 ] ],
                TestsParents::find_many([ 'pk' => [ 1 , 2 ] ], [ 'page' => 1 , 'per_page' => 1, 'order' => [ 'name' => 'ASC' ] ])
            );
        }

        public function testUseRecords() : void
        {
            $parents = TestsParents::as_records()->get_all();

            $this->assertEquals(
                1,
                $parents[0]->get('pk')
            );
        }

        public function testPluck() : void
        {
            $this->assertEqualsCanonicalizing(
                [ 'Joe' , 'Jane' , 'Paul' ],
                TestsParents::pluck( 'name' )
            );

            $this->assertEqualsCanonicalizing(
                [ 'Joe' , 'Jane' , 'Paul' ],
                TestsParents::pluck( 'name' , [] )
            );

            $this->assertEqualsCanonicalizing(
                [ 'Joe' , 'Jane' , 'Paul' ],
                TestsParents::pluck( 'name' , [] , [] )
            );

            $this->assertEquals(
                [ 'Joe' ],
                TestsParents::pluck( 'name' , [] , [ 'per_page' => 1 , 'page' => 2 , 'order' => [ 'name' => 'DESC' ]] )
            );

            $this->assertEquals(
                [ 'Paul' ],
                TestsParents::pluck( 'name' , [] , [ 'per_page' => 2 , 'page' => 2 , 'order' => [ 'name' => 'ASC' ] ] )
            );

            $this->assertEquals(
                [ 'Paul' ],
                TestsParents::pluck( 'name' , [ 'pk' => 3 ] , [ ] )
            );
        }
    }
