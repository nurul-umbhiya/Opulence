<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the template class
 */
namespace RDev\Views;
use InvalidArgumentException;
use RDev\Files\FileSystem;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /** The path to the test template with default tags */
    const TEMPLATE_PATH_WITH_DEFAULT_TAGS = "/files/TestWithDefaultTagDelimiters.html";
    /** The path to the test template with PHP code */
    const TEMPLATE_PATH_WITH_INVALID_PHP_CODE = "/files/TestWithInvalidPHP.html";

    /** @var Template The template to use in the tests */
    private $template = null;
    /** @var FileSystem The file system used to read templates */
    private $fileSystem = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->template = new Template();
        $this->fileSystem = new FileSystem();
    }

    /**
     * Tests getting the delimiters for a type that does not have any
     */
    public function testGettingDelimitersForTypeThatDoesNotHaveAny()
    {
        $this->assertEquals([null, null], $this->template->getDelimiters("foo"));
    }

    /**
     * Tests getting the directive delimiters
     */
    public function testGettingDirectiveDelimiters()
    {
        $directiveDelimiters = $this->template->getDelimiters(Template::DELIMITER_TYPE_DIRECTIVE);
        $this->assertEquals(Template::DEFAULT_OPEN_DIRECTIVE_DELIMITER, $directiveDelimiters[0]);
        $this->assertEquals(Template::DEFAULT_CLOSE_DIRECTIVE_DELIMITER, $directiveDelimiters[1]);
        $this->template->setDelimiters(Template::DELIMITER_TYPE_DIRECTIVE, ["foo", "bar"]);
        $this->assertEquals(["foo", "bar"], $this->template->getDelimiters(Template::DELIMITER_TYPE_DIRECTIVE));
    }

    /**
     * Tests getting an inherited part from the parent
     */
    public function testGettingInheritedPartFromParent()
    {
        $parent = clone $this->template;
        $parent->setPart("foo", "bar");
        $this->template->setParent($parent);
        $this->assertEquals("bar", $this->template->getPart("foo"));
        $this->assertEquals(["foo" => "bar"], $this->template->getParts());
    }

    /**
     * Tests getting an inherited tag from the parent
     */
    public function testGettingInheritedTagFromParent()
    {
        $parent = clone $this->template;
        $parent->setTag("foo", "bar");
        $this->template->setParent($parent);
        $this->assertEquals("bar", $this->template->getTag("foo"));
        $this->assertEquals(["foo" => "bar"], $this->template->getTags());
    }

    /**
     * Tests getting an inherited tag from the parent
     */
    public function testGettingInheritedVarFromParent()
    {
        $parent = clone $this->template;
        $parent->setVar("foo", "bar");
        $this->template->setParent($parent);
        $this->assertEquals("bar", $this->template->getVar("foo"));
        $this->assertEquals(["foo" => "bar"], $this->template->getVars());
    }

    /**
     * Tests getting a non-existent parent
     */
    public function testGettingNonExistentParent()
    {
        $this->assertNull($this->template->getParent());
    }

    /**
     * Tests getting a non-existent tag
     */
    public function testGettingNonExistentTag()
    {
        $this->assertNull($this->template->getTag("foo"));
    }

    /**
     * Tests getting a non-existent variable
     */
    public function testGettingNonExistentVariable()
    {
        $this->assertNull($this->template->getVar("foo"));
    }

    /**
     * Tests pushing a parent part
     */
    public function testGettingPartFromParent()
    {
        $parent = clone $this->template;
        $parent->setPart("foo", "bar");
        $this->template->setParent($parent);
        $this->assertEquals("bar", $this->template->getPart("foo"));
        $this->assertEquals(["foo" => "bar"], $this->template->getParts());
    }

    /**
     * Tests getting the sanitized tag delimiters
     */
    public function testGettingSanitizedTagDelimiters()
    {
        $sanitizedDelimiters = $this->template->getDelimiters(Template::DELIMITER_TYPE_SANITIZED_TAG);
        $this->assertEquals(Template::DEFAULT_OPEN_SANITIZED_TAG_DELIMITER, $sanitizedDelimiters[0]);
        $this->assertEquals(Template::DEFAULT_CLOSE_SANITIZED_TAG_DELIMITER, $sanitizedDelimiters[1]);
        $this->template->setDelimiters(Template::DELIMITER_TYPE_SANITIZED_TAG, ["foo", "bar"]);
        $sanitizedDelimiters = $this->template->getDelimiters(Template::DELIMITER_TYPE_SANITIZED_TAG);
        $this->assertEquals("foo", $sanitizedDelimiters[0]);
        $this->assertEquals("bar", $sanitizedDelimiters[1]);
    }

    /**
     * Tests getting a tag
     */
    public function testGettingTag()
    {
        $this->template->setTag("foo", "bar");
        $this->assertEquals("bar", $this->template->getTag("foo"));
    }

    /**
     * Tests getting the tags
     */
    public function testGettingTags()
    {
        $this->template->setTag("foo", "bar");
        $this->assertEquals(["foo" => "bar"], $this->template->getTags());
    }

    /**
     * Tests getting the unsanitized tag delimiters
     */
    public function testGettingUnsanitizedTagDelimiters()
    {
        $unsanitizedTagDelimiters = $this->template->getDelimiters(Template::DELIMITER_TYPE_UNSANITIZED_TAG);
        $this->assertEquals(Template::DEFAULT_OPEN_UNSANITIZED_TAG_DELIMITER, $unsanitizedTagDelimiters[0]);
        $this->assertEquals(Template::DEFAULT_CLOSE_UNSANITIZED_TAG_DELIMITER, $unsanitizedTagDelimiters[1]);
        $this->template->setDelimiters(Template::DELIMITER_TYPE_UNSANITIZED_TAG, ["foo", "bar"]);
        $this->assertEquals(["foo", "bar"], $this->template->getDelimiters(Template::DELIMITER_TYPE_UNSANITIZED_TAG));
    }

    /**
     * Tests getting a var
     */
    public function testGettingVar()
    {
        $this->template->setVar("foo", "bar");
        $this->assertEquals("bar", $this->template->getVar("foo"));
    }

    /**
     * Tests getting the bars
     */
    public function testGettingVars()
    {
        $this->template->setVar("foo", "bar");
        $this->assertEquals(["foo" => "bar"], $this->template->getVars());
    }

    /**
     * Tests not setting the contents in the constructor
     */
    public function testNotSettingContentsInConstructor()
    {
        $this->assertEmpty($this->template->getContents());
    }

    /**
     * Tests setting the contents
     */
    public function testSettingContents()
    {
        $this->template->setContents("blah");
        $this->assertEquals("blah", $this->template->getContents());
    }

    /**
     * Tests setting the contents in the constructor
     */
    public function testSettingContentsInConstructor()
    {
        $template = new Template("foo");
        $this->assertEquals("foo", $template->getContents());
    }

    /**
     * Tests setting the contents to a non-string
     */
    public function testSettingContentsToNonString()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->template->setContents(["Not a string"]);
    }

    /**
     * Tests setting delimiters
     */
    public function testSettingDelimiters()
    {
        $this->template->setDelimiters("foo", ["bar", "baz"]);
        $this->assertEquals(["bar", "baz"], $this->template->getDelimiters("foo"));
    }

    /**
     * Tests setting multiple tags in a template
     */
    public function testSettingMultipleTags()
    {
        $this->template->setTags(["foo" => "bar", "abc" => "xyz"]);
        $reflectionObject = new \ReflectionObject($this->template);
        $property = $reflectionObject->getProperty("tags");
        $property->setAccessible(true);
        $tags = $property->getValue($this->template);
        $this->assertEquals(["foo" => "bar", "abc" => "xyz"], $tags);
    }

    /**
     * Tests setting multiple variables in a template
     */
    public function testSettingMultipleVariables()
    {
        $this->template->setVars(["foo" => "bar", "abc" => ["xyz"]]);
        $reflectionObject = new \ReflectionObject($this->template);
        $property = $reflectionObject->getProperty("vars");
        $property->setAccessible(true);
        $vars = $property->getValue($this->template);
        $this->assertEquals(["foo" => "bar", "abc" => ["xyz"]], $vars);
    }

    /**
     * Tests setting the parent
     */
    public function testSettingParent()
    {
        $parent = clone $this->template;
        $this->template->setParent($parent);
        $this->assertSame($parent, $this->template->getParent());
    }

    /**
     * Tests setting a template part
     */
    public function testSettingPart()
    {
        $this->template->setPart("foo", "bar");
        $this->assertEquals("bar", $this->template->getPart("foo"));
    }

    /**
     * Tests setting a tag in a template
     */
    public function testSettingSingleTag()
    {
        $this->template->setTag("foo", "bar");
        $reflectionObject = new \ReflectionObject($this->template);
        $property = $reflectionObject->getProperty("tags");
        $property->setAccessible(true);
        $tags = $property->getValue($this->template);
        $this->assertEquals(["foo" => "bar"], $tags);
    }

    /**
     * Tests setting a variable in a template
     */
    public function testSettingSingleVariable()
    {
        $this->template->setVar("foo", "bar");
        $reflectionObject = new \ReflectionObject($this->template);
        $property = $reflectionObject->getProperty("vars");
        $property->setAccessible(true);
        $vars = $property->getValue($this->template);
        $this->assertEquals(["foo" => "bar"], $vars);
    }

    /**
     * Tests that nothing is output from an invalid template
     */
    public function testThatNothingIsOutputFromInvalidTemplate()
    {
        $output = "";
        $startOBLevel = ob_get_level();

        try
        {
            $contents = $this->fileSystem->read(__DIR__ . self::TEMPLATE_PATH_WITH_INVALID_PHP_CODE);
            $this->template->setContents($contents);
        }
        catch(\RuntimeException $ex)
        {
            // Don't do anything
        }
        finally
        {
            while(ob_get_level() > $startOBLevel)
            {
                $output .= ob_get_clean();
            }
        }

        $this->assertEmpty($output);
    }
} 