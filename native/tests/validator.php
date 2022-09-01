<?php 
    declare(strict_types=1);

    use PHPUnit\Framework\TestCase;

    require_once __DIR__ . '/../autoloader.php';

    use native\libs\Validator;

    final class ValidatorTest extends TestCase
    {
        public function testNumeric() : void
        {
            $this->assertTrue(Validator::is_numeric('0'));
            $this->assertTrue(Validator::is_numeric('0.1'));
            $this->assertTrue(Validator::is_numeric(10));
            $this->assertTrue(Validator::is_numeric(10.4));
            $this->assertTrue(Validator::is_numeric(-1));
            $this->assertTrue(Validator::is_numeric('-1'));
            $this->assertFalse(Validator::is_numeric('1 + 4'));
        }

        public function testMin() : void
        {
            $this->assertFalse(Validator::is_min(5 , 18));
            $this->assertTrue(Validator::is_min(50 , 18));
            $this->assertFalse(Validator::is_min(-1 , 18));
            $this->assertTrue(Validator::is_min(18, 18));
            $this->assertFalse(Validator::is_min(0, 18));
            $this->assertFalse(Validator::is_min('14', 18));
            $this->assertFalse(Validator::is_min('0', 18));
            $this->assertTrue(Validator::is_min('18', 18));
            $this->assertTrue(Validator::is_min('18.9', 18));
            $this->assertTrue(Validator::is_min(18.9, 18));
        }

        public function testMax() : void
        {
            $this->assertTrue(Validator::is_max(5 , 18));
            $this->assertFalse(Validator::is_max(50 , 18));
            $this->assertTrue(Validator::is_max(-1 , 18));
            $this->assertTrue(Validator::is_max(18, 18));
            $this->assertTrue(Validator::is_max(0, 18));
            $this->assertTrue(Validator::is_max('14', 18));
            $this->assertTrue(Validator::is_max('0', 18));
            $this->assertTrue(Validator::is_max('18', 18));
            $this->assertFalse(Validator::is_max('18.9', 18));
            $this->assertFalse(Validator::is_max(18.9, 18));
        }

        public function testBoolean() : void
        {
            $this->assertTrue(Validator::is_boolean(true));
            $this->assertTrue(Validator::is_boolean(false));
            $this->assertTrue(Validator::is_boolean(1));
            $this->assertTrue(Validator::is_boolean(0));
            $this->assertTrue(Validator::is_boolean('1'));
            $this->assertTrue(Validator::is_boolean('0'));
            $this->assertTrue(Validator::is_boolean('true'));
            $this->assertTrue(Validator::is_boolean('false'));
            $this->assertTrue(Validator::is_boolean('TRUE'));
            $this->assertTrue(Validator::is_boolean('NO'));
            $this->assertTrue(Validator::is_boolean('yes'));
            $this->assertTrue(Validator::is_boolean('no'));
            $this->assertFalse(Validator::is_boolean(148));
            $this->assertFalse(Validator::is_boolean(-1));
            $this->assertFalse(Validator::is_boolean('-1'));
            $this->assertFalse(Validator::is_boolean(null));
        }

        public function testEmail() : void
        {
            $this->assertTrue(Validator::is_email('jordan@peterson.co'));
            $this->assertTrue(Validator::is_email('josh.gilberts@gmail.com'));
            $this->assertTrue(Validator::is_email('tim+tom@gmail.com'));
            $this->assertTrue(Validator::is_email('tim+tom@my.domain.com'));
            $this->assertTrue(Validator::is_email('tim-tom+tommy_macaron@my.very-domain.xyz'));
            $this->assertFalse(Validator::is_email('tom@hopkins'));
            $this->assertFalse(Validator::is_email('tom@hopkins.'));
            $this->assertFalse(Validator::is_email(204));
        }

        public function testString() : void
        {
            $this->assertTrue(Validator::is_string('abcd'));
            $this->assertTrue(Validator::is_string(''));
            $this->assertFalse(Validator::is_string(false));
            $this->assertFalse(Validator::is_string(28923));
        }

        public function testRegex() : void
        {
            $this->assertTrue(Validator::is_regex('1234-5678', '/^\d{4}-\d{4}$/'));
            $this->assertFalse(Validator::is_regex('', '/^\d{4}-\d{4}$/'));
            $this->assertFalse(Validator::is_regex(null, '/^\d{4}-\d{4}$/'));
            $this->assertFalse(Validator::is_regex(0, '/^\d{4}-\d{4}$/'));
            $this->assertFalse(Validator::is_regex(1, '/^\d{4}-\d{4}$/'));
        }

        public function testIn() : void
        {
            $this->assertTrue(Validator::is_in('5', '5,4,3'));
            $this->assertTrue(Validator::is_in(5, '5,4,3'));
            $this->assertFalse(Validator::is_in(0, '5,4,3'));
        }

        public function testNotIn() : void
        {
            $this->assertTrue(Validator::is_not_in('5', '4,3'));
            $this->assertTrue(Validator::is_not_in(5, '4,3'));
            $this->assertTrue(Validator::is_not_in(0, '5,4,3'));
        }

        public function testDate() : void
        {
            $this->assertTrue(Validator::is_date('2022-01-01'));
            $this->assertFalse(Validator::is_date('01-01-2022'));
            $this->assertFalse(Validator::is_date(''));
            $this->assertFalse(Validator::is_date(null));
        }

        public function testRequired() : void
        {
            $this->assertTrue(Validator::is_required(''));
            $this->assertTrue(Validator::is_required(0));
            $this->assertTrue(Validator::is_required(false));
            $this->assertFalse(Validator::is_required(null));
        }

        public function testNotEmpty() : void
        {
            $this->assertTrue(Validator::is_not_empty('abcdefgh'));
            $this->assertFalse(Validator::is_not_empty(''));
            $this->assertFalse(Validator::is_not_empty(0));
            $this->assertTrue(Validator::is_not_empty(1));
            $this->assertTrue(Validator::is_not_empty(-1));
            $this->assertTrue(Validator::is_not_empty(true));
            $this->assertFalse(Validator::is_not_empty(false));
        }

        public function testOptional() : void
        {
            $this->assertTrue(Validator::is_optional(null));
            $this->assertFalse(Validator::is_optional('a'));
            $this->assertFalse(Validator::is_optional(1));
            $this->assertFalse(Validator::is_optional(0));
            $this->assertTrue(Validator::is_optional(''));
        }

        public function testMinLength() : void
        {
            $this->assertTrue(Validator::is_min_length('1234', 4));
            $this->assertTrue(Validator::is_min_length('1234', 3));
            $this->assertFalse(Validator::is_min_length('1234', 8));
            $this->assertFalse(Validator::is_min_length('', 8));
            $this->assertTrue(Validator::is_min_length('18', 0));
            $this->assertTrue(Validator::is_min_length(18, 2));
            $this->assertTrue(Validator::is_min_length(-18, 3));
        }

        public function testMaxLength() : void
        {
            $this->assertTrue(Validator::is_max_length('1234', 4));
            $this->assertFalse(Validator::is_max_length('1234', 3));
            $this->assertTrue(Validator::is_max_length('1234', 8));
            $this->assertTrue(Validator::is_max_length('', 8));
            $this->assertFalse(Validator::is_max_length('18', 0));
            $this->assertTrue(Validator::is_max_length(18, 2));
            $this->assertTrue(Validator::is_max_length(-18, 3));
        }

        public function testSchema() : void
        {
            $input_1 = [
                'name' => 'Joe',
                'age' => 34
            ];
            $input_2 = [
                'name' => 'Jane',
            ];
            $input_3 = [
                'name' => 'John',
                'age' => 47,
                'email' => 'john@mock.com'
            ];
            $input_4 = [
                'name' => 'Megs',
                'email' => 'megs@rocks.com'
            ];
            $input_5 = [
                'name' => 'Carl',
                'email' => 'carl@doesnt-rock'
            ];

            $schema = [
                'name' => [ 'required' , 'not_empty' , 'min_length:4' ],
                'age' => [ 'optional' , 'numeric' , 'min:31' ],
                'email' => [ 'optional' , 'email' ],
            ];

            $this->assertFalse(Validator::is_valid_schema($input_1, $schema));
            $this->assertTrue(Validator::is_valid_schema($input_2, $schema));
            $this->assertTrue(Validator::is_valid_schema($input_3, $schema));
            $this->assertTrue(Validator::is_valid_schema($input_4, $schema));
            $this->assertFalse(Validator::is_valid_schema($input_5, $schema));
        }

        public function testEnforceSchema() : void
        {
            $input = [ 'a' => 30 , 'b' => 'hello' , 'c' => '2022-01-01' ];
            $schema = [ 'a' => [ 'required' ] ];

            Validator::enforce_schema($input, $schema);
            $this->assertEquals(
                [ 'a' => 30 ],
                $input
            );
        }
    }

