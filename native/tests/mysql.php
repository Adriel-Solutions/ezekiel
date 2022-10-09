<?php 
    declare(strict_types=1);

    use PHPUnit\Framework\TestCase;

    require_once __DIR__ . '/../autoloader.php';

    use native\libs\Service;
    use native\libs\Database;
    use native\libs\Options;

    final class ServiceMySqlTest extends TestCase
    {
        private Service $s_parents;
        private Service $s_children;

        public static function setUpBeforeClass() : void 
        {
            // Retrieve configuration
            Options::load(__DIR__ . '/../../configuration/.custom.env');

            // Connect to database
            Database::load();

            Database::use('hesk');

            // Set up tests structure
            Database::query(
                'CREATE TABLE IF NOT EXISTS tests_parents ( 
                    pk INT AUTO_INCREMENT,
                    name TEXT NOT NULL,
                    age INT NOT NULL,

                    PRIMARY KEY (pk)
                )'
            );

            Database::query(
                'CREATE TABLE IF NOT EXISTS tests_children ( 
                    pk INT AUTO_INCREMENT,
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
                    pk INT AUTO_INCREMENT,
                    fk_child_1 INT NOT NULL,
                    fk_child_2 INT NOT NULL,

                    PRIMARY KEY (pk),
                    FOREIGN KEY (fk_child_1) REFERENCES tests_children (pk) ON DELETE CASCADE,
                    FOREIGN KEY (fk_child_2) REFERENCES tests_children (pk) ON DELETE CASCADE
                )'
            );

            Database::query(
                'CREATE TABLE IF NOT EXISTS tests_pets ( 
                    pk INT AUTO_INCREMENT,
                    name TEXT NOT NULL,
                    fk_child INT NOT NULL,

                    PRIMARY KEY (pk),
                    FOREIGN KEY (fk_child) REFERENCES tests_children (pk) ON DELETE CASCADE
                )'
            );
        }

        protected function setUp() : void 
        {
            // Setup services
            $this->s_parents = new Service();
            $this->s_parents->set_table('tests_parents');
            $this->s_parents->set_database('hesk');

            $this->s_children = new Service();
            $this->s_children->set_table('tests_children');
            $this->s_children->set_relation('parent_1', [ 'type' => 'ONE-TO-ONE' , 'table' => 'tests_parents' , 'local_column' => 'fk_parent_1' ]);
            $this->s_children->set_relation('parent_2', [ 'type' => 'ONE-TO-ONE' , 'table' => 'tests_parents' , 'local_column' => 'fk_parent_2' ]);
            $this->s_children->set_relation('friends', [ 'type' => 'MANY-TO-MANY' , 'table' => 'tests_children' ,  'dictionary' => 'tests_children_friends' , 'local_column' => 'fk_child_1' , 'foreign_column' => 'fk_child_2' ]);
            $this->s_children->set_relation('pets', [ 'type' => 'MANY-TO-ONE' , 'table' => 'tests_pets' ,  'foreign_column' => 'fk_child' ]);
            $this->s_children->set_database('hesk');

            // Fill with data
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
        }

        protected function tearDown() : void 
        {
            // Clear tables
            Database::query("SET FOREIGN_KEY_CHECKS = 0");
            Database::query("TRUNCATE tests_pets");
            Database::query("TRUNCATE tests_children_friends");
            Database::query("TRUNCATE tests_children");
            Database::query("TRUNCATE tests_parents");
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
            $this->assertTrue($this->s_parents->exists(1));
            $this->assertTrue($this->s_parents->exists(2));
            $this->assertFalse($this->s_parents->exists(4));
        }

        public function testCreate() : void
        {
            $this->assertEquals(
                [ 'pk' => 4 , 'name' => 'Robert' , 'age' => 40 ],
                $this->s_parents->create([ 'name' => 'Robert', 'age' => 40 ])
            );
        }

        public function testExistsOne() : void
        {
            $this->assertTrue($this->s_parents->exists_one([ 'name' => 'Joe' ]));

            $this->assertTrue(
                $this->s_parents->exists_one([
                    [
                        'column' => 'name',
                        'operator' => '=',
                        'value' => 'Joe'
                    ]
                ])
            );

            $this->assertTrue(
                $this->s_parents->exists_one([
                    [
                        'column' => 'name',
                        'operator' => 'IN',
                        'value' => [ 'Rick' , 'Jane' ]
                    ]
                ])
            );

            $this->assertTrue(
                $this->s_parents->exists_one([
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
                $this->s_parents->get('1')
            );

            $this->assertEquals(
                [ 'pk' => 1 , 'name' => 'Joe' , 'age' => 30 ],
                $this->s_parents->get(1)
            );
        }

        public function testGetCount() : void
        {
            $this->assertEquals(
                3,
                $this->s_parents->get_count()
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
                $this->s_parents->get_all()
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
                $this->s_parents->get_all([ 'order' => [ 'age' => 'DESC' ] ])
            );

            $this->assertEquals(
                [
                    [
                        'pk' => 3,
                        'name' => 'Paul',
                        'age' => 48
                    ],
                ],
                $this->s_parents->get_all([  'per_page' => 1 , 'page' => 1,  'order' => [ 'age' => 'DESC' ] ])
            );
        }

        public function testDelete() : void
        {
            $this->s_children->delete(1);
            $this->assertEquals(
                2,
                $this->s_children->get_count()
            );
        }

        public function testUpdate() : void
        {
            $this->assertEquals(
                [ 'pk' => 1 , 'name' => 'Lord Voldemort', 'age' => 2, 'fk_parent_1' => 1, 'fk_parent_2' => 2 ],
                $this->s_children->update(1, [ 'name' => 'Lord Voldemort' ])
            );

            if(!Options::get('ENCRYPTION_ENABLED'))
                $this->assertEquals(
                    [ 'pk' => 1 , 'name' => 'Lord Voldemort', 'age' => 18, 'fk_parent_1' => 1, 'fk_parent_2' => 2 ],
                    $this->s_children->update(1, [ 'age' => '[age + 16]' ])
                );
            else  {
                $this->expectException(Exception::class);
                $this->s_children->update(1, [ 'age' => '[age + 16]' ]);
            }
        }

        public function testFindAndDelete() : void
        {
            $this->s_children->find_and_delete([ 'name' => 'Harry' ]);

            $this->assertEquals(
                2,
                $this->s_children->get_count()
            );

            if(Options::get('ENCRYPTION_ENABLED'))
                $this->expectException(Exception::class);

            $this->s_children->find_and_delete(
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
                $this->s_children->get_count()
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
                $this->s_children->find_one([])
            );

            $this->assertEquals(
                [
                    'pk' => 1,
                    'name' => 'Harry',
                    'age' => 2,
                    'fk_parent_1' => 1,
                    'fk_parent_2' => 2
                ],
                $this->s_children->find_one([ 'age' => 2 ])
            );

            $this->assertEquals(
                [
                    'pk' => 1,
                    'name' => 'Harry',
                    'age' => 2,
                    'fk_parent_1' => 1,
                    'fk_parent_2' => 2
                ],
                $this->s_children->find_one(
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
                $this->s_parents->find_all([])
            );

            $this->assertCount(
                1,
                $this->s_parents->find_all([], [ 'per_page' => 1 , 'page' => 1 ])
            );

            $this->assertCount(
                2,
                $this->s_parents->find_all([], [ 'per_page' => 2 , 'page' => 1 ])
            );

            $this->assertCount(
                1,
                $this->s_parents->find_all([], [ 'per_page' => 2 , 'page' => 2 ])
            );

            $this->assertEquals(
                [ [ 'pk' => 1 , 'name' => 'Joe' , 'age' => 30 ] ],
                $this->s_parents->find_all([], [ 'per_page' => 1 , 'page' => 1 , 'order' => [ 'pk' => 'ASC' ] ])
            );

            $this->assertEquals(
                [ [ 'pk' => 3 , 'name' => 'Paul' , 'age' => 48 ] ],
                $this->s_parents->find_all([], [ 'per_page' => 1 , 'page' => 1 , 'order' => [ 'pk' => 'DESC' ] ])
            );
        }

        public function testFindCount() : void
        {
            $this->assertEquals(
                3,
                $this->s_parents->find_count([])
            );

            $this->assertEquals(
                1,
                $this->s_parents->find_count([ 'pk' => 1 ])
            );

            $this->assertEquals(
                3,
                $this->s_parents->find_count([ [ 'column' => 'age' , 'operator' => '>' , 'value' => 1 ] ])
            );

            $this->assertEquals(
                0,
                $this->s_parents->find_count([ [ 'column' => 'pk' , 'value' => 1 ], [ 'column' => 'pk' , 'value' => 2 ] ])
            );
        }

        public function testGetMany() : void
        {
            $this->assertEquals(
                [ 'pk' => 1 , 'name' => 'Joe' , 'age' => 30 ],
                $this->s_parents->get_many([ 'order' => [ 'pk' => 'ASC' ] ])[0]
            );

            $this->assertEquals(
                [ 'pk' => 3 , 'name' => 'Paul' , 'age' => 48 ],
                $this->s_parents->get_many([ 'order' => [ 'pk' => 'DESC' ] ])[0]
            );

            $this->assertEquals(
                [ 'pk' => 2 , 'name' => 'Jane' , 'age' => 34 ],
                $this->s_parents->get_many([  'page' => 2 ,  'per_page' => 1, 'order' => [ 'pk' => 'DESC' ] ])[0]
            );

            $this->assertCount(
                1,
                $this->s_parents->get_many([ 'page' => 1 ,  'per_page' => 1 ])
            );

            $this->assertCount(
                1,
                $this->s_parents->get_many([ 'page' => 3 ,  'per_page' => 1 ])
            );
        }

        public function testPopulate() : void
        {
            $child = $this->s_children->get(1);
            $this->s_children->populate($child, 'parent_1');
            $this->s_children->populate($child, 'parent_2');
            $this->s_children->populate($child, 'friends');
            $this->s_children->populate($child, 'pets');

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
                $this->s_parents->find_and_update([ 'pk' => 1 ], [ 'name' => 'Merlin' ])
            );

            $this->assertEquals(
                [ [ 'pk' => 2 , 'name' => 'Eva' , 'age' => 60 ] ],
                $this->s_parents->find_and_update([ 'pk' => 2 ], [ 'name' => 'Eva' , 'age' => 60 ])
            );

            $this->assertEquals(
                [ [ 'pk' => 1 , 'name' => 'Zombie' , 'age' => 100 ] , [ 'pk' => 2 , 'name' => 'Zombie' , 'age' => 100 ] ],
                $this->s_parents->find_and_update([ 'pk' => [ 1 , 2 ] ], [ 'name' => 'Zombie' , 'age' => 100 ])
            );

            if(!Options::get('ENCRYPTION_ENABLED'))
                $this->assertEquals(
                    [ [ 'pk' => 1 , 'name' => 'Zombie' , 'age' => 200 ] , [ 'pk' => 2 , 'name' => 'Zombie' , 'age' => 200 ] ],
                    $this->s_parents->find_and_update([ 'pk' => [ 1 , 2 ] ], [ 'age' => '[age + 100]' ])
                );
            else {
                $this->expectException(Exception::class);
                $this->s_parents->find_and_update([ 'pk' => [ 1 , 2 ] ], [ 'age' => '[age + 100]' ]);
            }
        }

        public function testFindMany() : void
        {
            $this->assertCount(
                3,
                $this->s_parents->find_many([])
            );

            $this->assertEquals(
                [ [ 'pk' => 1 , 'name' => 'Joe' , 'age' => 30 ] ],
                $this->s_parents->find_many([ 'pk' => 1 ])
            );

            $this->assertEquals(
                [ [ 'pk' => 1 , 'name' => 'Joe' , 'age' => 30 ] , [ 'pk' => 2 , 'name' => 'Jane' , 'age' => 34] ],
                $this->s_parents->find_many([ 'pk' => [ 1 , 2 ] ])
            );

            $this->assertEquals(
                [ [ 'pk' => 1 , 'name' => 'Joe' , 'age' => 30 ] ],
                $this->s_parents->find_many([ 'pk' => [ 1 , 2 ] ], [ 'page' => 1 , 'per_page' => 1 ])
            );

            $this->assertEquals(
                [ [ 'pk' => 2 , 'name' => 'Jane' , 'age' => 34 ] ],
                $this->s_parents->find_many([ 'pk' => [ 1 , 2 ] ], [ 'page' => 1 , 'per_page' => 1, 'order' => [ 'pk' => 'DESC' ] ])
            );

            $this->assertEquals(
                [ [ 'pk' => 2 , 'name' => 'Jane' , 'age' => 34 ] ],
                $this->s_parents->find_many([ 'pk' => [ 1 , 2 ] ], [ 'page' => 1 , 'per_page' => 1, 'order' => [ 'name' => 'ASC' ] ])
            );
        }

        public function testUseRecords() : void
        {
            $parents = $this->s_parents->as_records()->get_all();

            $this->assertEquals(
                1,
                $parents[0]->get('pk')
            );
        }

        public function testPluck() : void
        {
            $this->assertEqualsCanonicalizing(
                [ 'Joe' , 'Jane' , 'Paul' ],
                $this->s_parents->pluck( 'name' )
            );

            $this->assertEqualsCanonicalizing(
                [ 'Joe' , 'Jane' , 'Paul' ],
                $this->s_parents->pluck( 'name' , [] )
            );

            $this->assertEqualsCanonicalizing(
                [ 'Joe' , 'Jane' , 'Paul' ],
                $this->s_parents->pluck( 'name' , [] , [] )
            );

            $this->assertEquals(
                [ 'Joe' ],
                $this->s_parents->pluck( 'name' , [] , [ 'per_page' => 1 , 'page' => 2 , 'order' => [ 'name' => 'DESC' ]] )
            );

            $this->assertEquals(
                [ 'Paul' ],
                $this->s_parents->pluck( 'name' , [] , [ 'per_page' => 2 , 'page' => 2 , 'order' => [ 'name' => 'ASC' ]] )
            );

            $this->assertEqualsCanonicalizing(
                [ 'Paul' ],
                $this->s_parents->pluck( 'name' , [ 'pk' => 3 ] , [ ] )
            );
        }
    }

