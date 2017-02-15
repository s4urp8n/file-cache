<?php
use Zver\FileCache;

class FileCacheTest extends PHPUnit\Framework\TestCase
{

    public static function getExpectedCacheDir()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
    }

    public static function setUpBeforeClass()
    {
        FileCache::setDirectory(static::getExpectedCacheDir());
    }

    public static function tearDownAfterClass()
    {

    }

    public function testExceptionDirectoryNotSet()
    {
        FileCache::unregisterDirectory();
        $this->expectException(Exception::class);
        FileCache::set('key', 'value');
    }

    public function testSetUnregisterDirectory()
    {
        $this->assertSame(FileCache::getDirectory(), static::getExpectedCacheDir());
        FileCache::unregisterDirectory();
        $this->expectException(Exception::class);
        FileCache::set('key', 'value');
    }

    public function testGetDirectory()
    {
        $directory = FileCache::getDirectory();
        $this->assertEquals($directory, static::getExpectedCacheDir());
        FileCache::clearAll();
    }

    public function testClearNotExistedDir()
    {
        FileCache::clearAll();
    }

    public function testReturnNull()
    {
        $this->assertTrue(is_null(FileCache::get('NotExistedKey')));
        $this->assertTrue(is_null(FileCache::get('NotexistedGroup', 'NotExistedKey')));
        FileCache::clearAll();
    }

    public function testOverwriteValue()
    {
        FileCache::set('key', 4);
        $this->assertEquals(4, FileCache::get('key'));
        FileCache::set('key', 5);
        $this->assertEquals(5, FileCache::get('key'));
        FileCache::clearAll();
    }

    public function testSetGetVariants()
    {
        FileCache::set('key', 'value');
        FileCache::setGroup('group', 'key', 'value');
        $this->assertEquals(FileCache::get('key'), FileCache::getGroup('group', 'key'), 'value');

        //second time
        FileCache::set('key', 'value');
        FileCache::setGroup('group', 'key', 'value');
        $this->assertEquals(FileCache::get('key'), FileCache::getGroup('group', 'key'), 'value');

        //changed
        FileCache::set('key', '1');
        FileCache::setGroup('group', 'key', '2');
        $this->assertEquals(FileCache::get('key'), '1');
        $this->assertEquals(FileCache::getGroup('group', 'key'), '2');

        //changed again
        FileCache::set('key22', '22');
        FileCache::set('key33', '33');
        FileCache::set('key44', '44');
        FileCache::setGroup('group', 'key', '3');
        FileCache::setGroup('group', 'key4', '4');
        FileCache::setGroup('group', 'key6', '6');

        $this->assertEquals(FileCache::get('key'), '1');
        $this->assertEquals(FileCache::get('key22'), '22');
        $this->assertEquals(FileCache::get('key33'), '33');
        $this->assertEquals(FileCache::get('key44'), '44');

        $this->assertEquals(FileCache::getGroup('group', 'key'), '3');
        $this->assertEquals(FileCache::getGroup('group', 'key4'), '4');
        $this->assertEquals(FileCache::getGroup('group', 'key6'), '6');
        FileCache::clearAll();
    }

    public function testUnsetGet()
    {
        $this->assertEquals(FileCache::get('43rfyew9fhsdoifsdlfdsffgroup', 'k32ffwefwfwefwefey6'), null);
        FileCache::clearAll();
    }

    public function testTypeAndValueIsSafe()
    {
        $values = [
            true,
            false,
            [
                'true',
                'false',
                null,
            ],
            'string',
            null,
            0,
            2.4,
            [
                1,
                'key2' => 2,
                3,
                0,
                [
                    0,
                    2,
                    [2],
                ],
            ],
            new DateTime(),
            0x101,
        ];

        foreach ($values as $key => $value) {
            FileCache::setGroup('typesTest', 'value' . $key, $value);
            $this->assertEquals(FileCache::getGroup('typesTest', 'value' . $key), $values[$key]);
            $this->assertEquals(
                gettype(FileCache::getGroup('typesTest', 'value' . $key)), gettype($values[$key])
            );
        }
        FileCache::clearAll();
    }

    public function testClear()
    {
        FileCache::set('key', 1);
        FileCache::setGroup('group', 'key', 2);

        $this->assertEquals(FileCache::get('key'), 1);
        $this->assertEquals(FileCache::getGroup('group', 'key'), 2);
        FileCache::clearAll();
        $this->assertEquals(FileCache::get('key'), null);
        $this->assertEquals(FileCache::getGroup('group', 'key'), null);
        FileCache::clearAll();
    }

    public function testClearDefaultGroup()
    {
        FileCache::set('key', 1);
        FileCache::setGroup('group', 'key', 2);
        $this->assertEquals(FileCache::get('key'), 1);
        $this->assertEquals(FileCache::getGroup('group', 'key'), 2);

        FileCache::clearGroup();
        $this->assertEquals(FileCache::get('key'), null);
        $this->assertEquals(FileCache::getGroup('group', 'key'), 2);
        FileCache::clearAll();
    }

    public function testClearGroup()
    {
        FileCache::set('key', 1);
        FileCache::setGroup('group', 'key', 2);
        $this->assertEquals(FileCache::get('key'), 1);
        $this->assertEquals(FileCache::getGroup('group', 'key'), 2);

        FileCache::clearGroup('group');
        $this->assertEquals(FileCache::get('key'), 1);
        $this->assertEquals(FileCache::getGroup('group', 'key'), null);

        FileCache::clearAll();
    }

    public function testClearKey()
    {
        FileCache::set('key1', 1);
        FileCache::set('key2', 2);
        FileCache::setGroup('group', 'key1', 11);
        FileCache::setGroup('group', 'key2', 22);

        $this->assertEquals(FileCache::get('key1'), 1);
        $this->assertEquals(FileCache::get('key2'), 2);
        $this->assertEquals(FileCache::getGroup('group', 'key1'), 11);
        $this->assertEquals(FileCache::getGroup('group', 'key2'), 22);

        FileCache::clearKey('key1');
        $this->assertEquals(FileCache::get('key1'), null);
        $this->assertEquals(FileCache::get('key2'), 2);
        $this->assertEquals(FileCache::getGroup('group', 'key1'), 11);
        $this->assertEquals(FileCache::getGroup('group', 'key2'), 22);

        FileCache::clearKey('key2');
        $this->assertEquals(FileCache::get('key1'), null);
        $this->assertEquals(FileCache::get('key2'), null);
        $this->assertEquals(FileCache::getGroup('group', 'key1'), 11);
        $this->assertEquals(FileCache::getGroup('group', 'key2'), 22);

        FileCache::clearGroupKey('group', 'key2');
        $this->assertEquals(FileCache::get('key1'), null);
        $this->assertEquals(FileCache::get('key2'), null);
        $this->assertEquals(FileCache::getGroup('group', 'key1'), 11);
        $this->assertEquals(FileCache::getGroup('group', 'key2'), null);

        FileCache::clearGroupKey('group', 'key1');
        $this->assertEquals(FileCache::get('key1'), null);
        $this->assertEquals(FileCache::get('key2'), null);
        $this->assertEquals(FileCache::getGroup('group', 'key1'), null);
        $this->assertEquals(FileCache::getGroup('group', 'key2'), null);

        FileCache::clearAll();
    }

    public function testEnableDisable()
    {
        FileCache::set('key', 'value');
        $this->assertEquals(FileCache::get('key'), 'value');

        FileCache::disable();
        $this->assertEquals(FileCache::get('key'), null);

        FileCache::enable();
        $this->assertEquals(FileCache::get('key'), 'value');
    }

    public function testExceptionsSet()
    {
        $catched = 0;
        try {
            FileCache::set(null, 'value');
        }
        catch (InvalidArgumentException $e) {
            $catched++;
        }

        try {
            FileCache::setGroup(null, null, 'value');
        }
        catch (InvalidArgumentException $e) {
            $catched++;
        }

        FileCache::clearAll();

        if ($catched != 2) {
            $this->fail('Expected exception not raised!');
        }

    }

    public function testExceptionRetrieve()
    {
        try {
            FileCache::retrieve(null, 'value');
        }
        catch (InvalidArgumentException $e) {
            return;
        }
        $this->fail('Expected exception not raised!');
    }

    public function testExceptionRetrieveGroup()
    {
        try {
            FileCache::retrieveGroup(null, null, 'value');
        }
        catch (InvalidArgumentException $e) {
            return;
        }
        $this->fail('Expected exception not raised!');
    }

    public function testRetrieve()
    {
        FileCache::clearAll();

        $value = FileCache::retrieve(
            'key', function () {
            return 'value';
        }
        );

        $this->assertEquals($value, FileCache::get('key'));

        FileCache::clearAll();

        $value = FileCache::retrieveGroup(
            'group', 'key', function () {
            return 'value';
        }
        );

        $this->assertEquals($value, FileCache::getGroup('group', 'key'));

        FileCache::clearAll();

        $value = FileCache::retrieveGroup('group', 'key', 'value');

        $this->assertEquals($value, FileCache::getGroup('group', 'key'));

        FileCache::clearAll();
    }

    public function testIsReallyCached()
    {
        ob_start();
        $generate = function () {
            echo 'GENERATED';

            return 1;
        };
        $key = 'key';
        $generateValue = $generate(); //first output
        $first = FileCache::retrieve($key, $generate); //second output
        $second = FileCache::retrieve(
            $key, $generate
        ); //no output is indicates that value gets from cache - not from generate function
        $output = ob_get_clean();
        $this->assertEquals('GENERATEDGENERATED', $output);
    }

    public function testIsReallyCachedGroup()
    {
        ob_start();
        $generate = function () {
            echo 'GENERATED';

            return 1;
        };
        $key = 'key';
        $group = 'group';
        $generateValue = $generate(); //first output
        $first = FileCache::retrieveGroup($group, $key, $generate); //second output
        $second = FileCache::retrieveGroup(
            $group, $key, $generate
        ); //no output is indicates that value gets from cache - not from generate function
        $output = ob_get_clean();
        $this->assertEquals('GENERATEDGENERATED', $output);
    }

    public function testCacheIsFaster()
    {
        $operation = $this->_getLongOperation();
        $startTime = microtime(true);
        $startResult = $operation();
        $endTime = microtime(true);
        $startDuration = $endTime - $startTime;
        FileCache::set('key', $startResult);
        $testStartTime = microtime(true);
        $testResult = FileCache::get('key');
        $testEndTime = microtime(true);
        $testDuration = $testEndTime - $testStartTime;
        $this->assertEquals($startResult, $testResult);
        $this->assertTrue($testDuration < $startDuration);
        FileCache::clearAll();
    }

    public function _getLongOperation()
    {
        $that = $this;

        return function () use ($that) {
            $values = range(0, 10000);
            foreach ($values as $key => $value) {
                $values[$key] = array_reverse(array_unique(preg_split('#\d+#', md5($key) . sha1($key))));
                $values[$key] = array_combine(array_flip($values[$key]), $values[$key]);
                ksort($values[$key]);
                shuffle($values[$key]);
                sort($values[$key]);
                $values[$key] = sha1(implode('', $values[$key]));
            }

            return md5(implode('', $values));
        };
    }

    public function testRetrieveCallbackIsFaster()
    {
        $that = $this;
        $key = 'key';
        $generate = function () use ($that) {
            $func = $that->_getLongOperation();

            return $func();
        };
        $firstStartTime = microtime(true);
        $firstValue = FileCache::retrieve($key, $generate);
        $firstEndTime = microtime(true);
        $firstDuration = $firstEndTime - $firstStartTime;
        $secondStartTime = microtime(true);
        $secondValue = FileCache::retrieve($key, $generate);
        $secondEndTime = microtime(true);
        $secondDuration = $secondEndTime - $secondStartTime;
        $this->assertEquals($firstValue, $secondValue);
        $this->assertTrue($secondDuration < $firstDuration);
        FileCache::clearAll();
    }

    public function testRemoveMultipleTimes()
    {
        FileCache::set('key', 'value');
        FileCache::clearKey('key');
        FileCache::clearKey('key');
        FileCache::clearKey('key');
        FileCache::clearKey('key');
        FileCache::clearAll();
    }

    public function testGroupAndSimpleNotIntersectsSet()
    {
        FileCache::set('key', 1);
        for ($i = 2; $i < 30; $i++) {
            FileCache::set('group', 'key', $i);
            $this->assertNotEquals(FileCache::get('group', 'key'), FileCache::get('key'));
        }
        FileCache::clearAll();

        FileCache::set('group', 'key', 1);
        for ($i = 2; $i < 30; $i++) {
            FileCache::set('key', $i);
            $this->assertNotEquals(FileCache::get('group', 'key'), FileCache::get('key'));
        }
        FileCache::clearAll();
    }

    public function testNotExistedGroupDeletion()
    {
        FileCache::clearAll();
        FileCache::clearGroup('notExistedGroup');
        FileCache::clearAll();
    }

}