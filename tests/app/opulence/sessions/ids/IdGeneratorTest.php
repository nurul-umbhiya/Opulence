<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Id generator
 */
namespace Opulence\Sessions\Ids;

use Opulence\Cryptography\Utilities\Strings;

class IdGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var IdGenerator The Id generator to use in tests */
    private $generator = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->generator = new IdGenerator(new Strings());
    }

    /**
     * Tests generating an Id with a length specified
     */
    public function testGeneratingWithLength()
    {
        $id = $this->generator->generate(28);
        $this->assertTrue(is_string($id));
        $this->assertEquals(28, strlen($id));
    }

    /**
     * Tests generating an Id without a length specified
     */
    public function testGeneratingWithoutLength()
    {
        $id = $this->generator->generate();
        $this->assertTrue(is_string($id));
        $this->assertEquals(IdGenerator::DEFAULT_LENGTH, strlen($id));
    }

    /**
     * Tests validating an invalid Id
     */
    public function testValidatingInvalidId()
    {
        // Invalid characters
        $id = str_repeat("#", IdGenerator::DEFAULT_LENGTH);
        $this->assertFalse($this->generator->isIdValid($id));
        // Too short
        $id = str_repeat(1, IdGenerator::MIN_LENGTH - 1);
        $this->assertFalse($this->generator->isIdValid($id));
        // Incorrect type
        $id = ["foo"];
        $this->assertFalse($this->generator->isIdValid($id));
        // Longer than max length
        $id = str_repeat(1, IdGenerator::MAX_LENGTH + 1);
        $this->assertFalse($this->generator->isIdValid($id));
    }

    /**
     * Tests validating a valid Id
     */
    public function testValidatingValidId()
    {
        // Default length
        $id = str_repeat("1", IdGenerator::DEFAULT_LENGTH);
        $this->assertTrue($this->generator->isIdValid($id));
        // The min length
        $id = str_repeat(1, IdGenerator::MIN_LENGTH);
        $this->assertTrue($this->generator->isIdValid($id));
        // The max length
        $id = str_repeat(1, IdGenerator::MAX_LENGTH);
        $this->assertTrue($this->generator->isIdValid($id));
        // Mix of characters
        $id = "aA1" . str_repeat(2, IdGenerator::DEFAULT_LENGTH - 3);
        $this->assertTrue($this->generator->isIdValid($id));
    }
}