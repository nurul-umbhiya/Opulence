<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the route class
 */
namespace RDev\Models\Routing;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding multiple post-filters
     */
    public function testAddingMultiplePostFilters()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->addPostFilters(["foo", "bar"]);
        $this->assertEquals(["foo", "bar"], $route->getPostFilters());
    }

    /**
     * Tests adding multiple pre-filters
     */
    public function testAddingMultiplePreFilters()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->addPreFilters(["foo", "bar"]);
        $this->assertEquals(["foo", "bar"], $route->getPreFilters());
    }

    /**
     * Tests adding non-unique post-filters
     */
    public function testAddingNonUniquePostFilters()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->addPostFilters("foo");
        $route->addPostFilters("foo");
        $this->assertEquals(["foo"], $route->getPostFilters());
    }

    /**
     * Tests adding non-unique pre-filters
     */
    public function testAddingNonUniquePreFilters()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->addPreFilters("foo");
        $route->addPreFilters("foo");
        $this->assertEquals(["foo"], $route->getPreFilters());
    }

    /**
     * Tests adding a single post-filter
     */
    public function testAddingSinglePostFilter()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->addPostFilters("foo");
        $this->assertEquals(["foo"], $route->getPostFilters());
    }

    /**
     * Tests adding a single pre-filter
     */
    public function testAddingSinglePreFilter()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->addPreFilters("foo");
        $this->assertEquals(["foo"], $route->getPreFilters());
    }

    /**
     * Tests getting the controller method
     */
    public function testGettingControllerMethod()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $this->assertEquals("bar", $route->getControllerMethod());
    }

    /**
     * Tests getting the controller name
     */
    public function testGettingControllerName()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $this->assertEquals("foo", $route->getControllerName());
    }

    /**
     * Tests getting the default value for a variable without a default value
     */
    public function testGettingDefaultValueForVariableWithoutDefaultValue()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $this->assertNull($route->getDefaultValue("foo"));
    }

    /**
     * Tests getting the methods
     */
    public function testGettingMethods()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/foo", $options);
        $this->assertEquals(["get"], $route->getMethods());
    }

    /**
     * Tests getting the post-filter when it is a string
     */
    public function testGettingPostFilterWhenItIsAString()
    {
        $options = [
            "controller" => "foo@bar",
            "post" => "foo"
        ];
        $route = new Route("get", "/foo", $options);
        $this->assertEquals(["foo"], $route->getPostFilters());
    }

    /**
     * Tests getting the post-filters when they are an array
     */
    public function testGettingPostFiltersWhenTheyAreAnArray()
    {
        $options = [
            "controller" => "foo@bar",
            "post" => ["foo", "bar"]
        ];
        $route = new Route("get", "/foo", $options);
        $this->assertEquals(["foo", "bar"], $route->getPostFilters());
    }

    /**
     * Tests getting the pre-filter when it is a string
     */
    public function testGettingPreFilterWhenItIsAString()
    {
        $options = [
            "controller" => "foo@bar",
            "pre" => "foo"
        ];
        $route = new Route("get", "/foo", $options);
        $this->assertEquals(["foo"], $route->getPreFilters());
    }

    /**
     * Tests getting the pre-filters when they are an array
     */
    public function testGettingPreFiltersWhenTheyAreAnArray()
    {
        $options = [
            "controller" => "foo@bar",
            "pre" => ["foo", "bar"]
        ];
        $route = new Route("get", "/foo", $options);
        $this->assertEquals(["foo", "bar"], $route->getPreFilters());
    }

    /**
     * Tests getting the raw path
     */
    public function testGettingRawPath()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/foo/{id}", $options);
        $this->assertEquals("/foo/{id}", $route->getRawPath());
    }

    /**
     * Tests getting the regex for a variable
     */
    public function testGettingVariableRegex()
    {
        $options = [
            "controller" => "foo@bar",
            "variables" => ["bar" => "\d+"]
        ];
        $route = new Route("get", "/foo", $options);
        $this->assertEquals("\d+", $route->getVariableRegex("bar"));
    }

    /**
     * Tests getting the regex for a variable that does not have a regex
     */
    public function testGettingVariableRegexForParameterWithNoRegex()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/foo", $options);
        $this->assertNull($route->getVariableRegex("bar"));
    }

    /**
     * Tests not setting the controller
     */
    public function testNotSettingController()
    {
        $this->setExpectedException("\\RuntimeException");
        new Route("get", "/{foo}", []);
    }

    /**
     * Tests passing in a controller with no method
     */
    public function testPassingControllerWithNoMethod()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $options = [
            "controller" => "foo@"
        ];
        new Route("get", "/{foo}", $options);
    }

    /**
     * Tests passing in a controller with no name
     */
    public function testPassingControllerWithNoName()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $options = [
            "controller" => "@foo"
        ];
        new Route("get", "/{foo}", $options);
    }

    /**
     * Tests passing in an incorrectly formatted controller
     */
    public function testPassingIncorrectlyFormattedController()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $options = [
            "controller" => "foo"
        ];
        new Route("get", "/{foo}", $options);
    }

    /**
     * Tests passing in multiple methods to the constructor
     */
    public function testPassingMultipleMethodsToConstructor()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route(["get", "post"], "/foo", $options);
        $this->assertEquals(["get", "post"], $route->getMethods());
    }

    /**
     * Tests passing in a single method to the constructor
     */
    public function testPassingSingleMethodToConstructor()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/foo", $options);
        $this->assertEquals(["get"], $route->getMethods());
    }

    /**
     * Test prepending a post-filter
     */
    public function testPrependingPostFilter()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->addPostFilters("foo", false);
        $route->addPostFilters("bar", true);
        $this->assertEquals(["bar", "foo"], $route->getPostFilters());
    }

    /**
     * Test prepending a pre-filter
     */
    public function testPrependingPreFilter()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->addPreFilters("foo", false);
        $route->addPreFilters("bar", true);
        $this->assertEquals(["bar", "foo"], $route->getPreFilters());
    }

    /**
     * Tests setting a default value
     */
    public function testSettingADefaultValue()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->setDefaultValue("foo", 2);
        $this->assertEquals(2, $route->getDefaultValue("foo"));
    }

    /**
     * Tests setting the controller method
     */
    public function testSettingControllerMethod()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->setControllerMethod("blah");
        $this->assertEquals("blah", $route->getControllerMethod());
    }

    /**
     * Tests setting the controller name
     */
    public function testSettingControllerName()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->setControllerName("blah");
        $this->assertEquals("blah", $route->getControllerName());
    }

    /**
     * Tests setting the raw path
     */
    public function testSettingRawPath()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->setRawPath("blah");
        $this->assertEquals("blah", $route->getRawPath());
    }

    /**
     * Tests setting the regex
     */
    public function testSettingRegex()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/foo/{id}", $options);
        $route->setRegex("blah");
        $this->assertEquals("blah", $route->getRegex());
    }

    /**
     * Tests setting a variable regex
     */
    public function testSettingVariableRegex()
    {
        $options = [
            "controller" => "foo@bar"
        ];
        $route = new Route("get", "/{foo}", $options);
        $route->setVariableRegex("foo", "\d+");
        $this->assertEquals("\d+", $route->getVariableRegex("foo"));
    }
} 