<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Redis bridge
 */
namespace Opulence\Cache;

use Opulence\Redis\Server;
use Opulence\Redis\TypeMapper;
use Opulence\Tests\Redis\Mocks\OpulencePHPRedis;
use Redis;

class RedisBridgeTest extends \PHPUnit_Framework_TestCase
{
    /** @var RedisBridge The bridge to use in tests */
    private $bridge = null;
    /** @var OpulencePHPRedis|\PHPUnit_Framework_MockObject_MockObject The Redis driver */
    private $redis = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $methodsToMock = ["decrBy", "del", "flushAll", "get", "incrBy", "setEx"];
        $constructorParams = [$this->getMock(Server::class), $this->getMock(TypeMapper::class)];
        $this->redis = $this->getMock(OpulencePHPRedis::class, $methodsToMock, $constructorParams);
        $this->bridge = new RedisBridge($this->redis, "dave:");
    }

    /**
     * Tests checking if a key exists
     */
    public function testCheckingIfKeyExists()
    {
        $this->redis->expects($this->at(0))->method("get")->will($this->returnValue(false));
        $this->redis->expects($this->at(1))->method("get")->will($this->returnValue("bar"));
        $this->assertFalse($this->bridge->has("foo"));
        $this->assertTrue($this->bridge->has("foo"));
    }

    /**
     * Tests decrementing returns correct values
     */
    public function testDecrementingReturnsCorrectValues()
    {
        $this->redis->expects($this->at(0))->method("decrBy")->with("dave:foo", 1)->will($this->returnValue(10));
        $this->redis->expects($this->at(1))->method("decrBy")->with("dave:foo", 5)->will($this->returnValue(5));
        // Test using default value
        $this->assertEquals(10, $this->bridge->decrement("foo"));
        // Test using a custom value
        $this->assertEquals(5, $this->bridge->decrement("foo", 5));
    }

    /**
     * Tests deleting a key
     */
    public function testDeletingKey()
    {
        $this->redis->expects($this->once())->method("del")->with("dave:foo");
        $this->bridge->delete("foo");
    }

    /**
     * Tests that the driver is the correct instance of Redis
     */
    public function testDriverIsCorrectInstance()
    {
        $this->assertSame($this->redis, $this->bridge->getRedis());
    }

    /**
     * Tests flushing the database
     */
    public function testFlushing()
    {
        $this->redis->expects($this->once())->method("flushAll");
        $this->bridge->flush();
    }

    /**
     * Tests that getting a value works
     */
    public function testGetWorks()
    {
        $this->redis->expects($this->once())->method("get")->will($this->returnValue("bar"));
        $this->assertEquals("bar", $this->bridge->get("foo"));
    }

    /**
     * Tests incrementing returns correct values
     */
    public function testIncrementingReturnsCorrectValues()
    {
        $this->redis->expects($this->at(0))->method("incrBy")->with("dave:foo", 1)->will($this->returnValue(2));
        $this->redis->expects($this->at(1))->method("incrBy")->with("dave:foo", 5)->will($this->returnValue(7));
        // Test using default value
        $this->assertEquals(2, $this->bridge->increment("foo"));
        // Test using a custom value
        $this->assertEquals(7, $this->bridge->increment("foo", 5));
    }

    /**
     * Tests that null is returned on cache miss
     */
    public function testNullIsReturnedOnMiss()
    {
        $this->redis->expects($this->once())->method("get")->will($this->returnValue(false));
        $this->assertNull($this->bridge->get("foo"));
    }

    /**
     * Tests setting a value
     */
    public function testSettingValue()
    {
        $this->redis->expects($this->once())->method("setEx")->with("dave:foo", "bar", 60);
        $this->bridge->set("foo", "bar", 60);
    }

    /**
     * Tests using a base Redis instance
     */
    public function testUsingBaseRedisInstance()
    {
        /** @var Redis|\PHPUnit_Framework_MockObject_MockObject $redis */
        $redis = $this->getMock(Redis::class);
        $bridge = new RedisBridge($redis);
        $this->assertSame($redis, $bridge->getRedis());
    }
}