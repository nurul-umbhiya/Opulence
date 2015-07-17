<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the tag sub-compiler
 */
namespace Opulence\Views\Compilers\SubCompilers;
use Opulence\HTTP\Requests\Request;
use Opulence\Tests\Mocks\User;
use Opulence\Tests\Views\Compilers\Tests\Compiler as CompilerTest;
use Opulence\Views\Compilers\ViewCompilerException;
use Opulence\Views\Template;

class TagCompilerTest extends CompilerTest
{
    /** @var TagCompiler The sub-compiler to test */
    private $subCompiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        parent::setUp();

        $this->subCompiler = new TagCompiler($this->compiler, $this->xssFilter);
    }

    /**
     * Tests calling a function on a variable
     */
    public function testCallingFunctionOnVariable()
    {
        // Test object
        $this->template->setVar("request", Request::createFromGlobals());
        $this->template->setContents('{{!$request->isPath("/foo/.*", true) ? \' class="current"\' : ""!}}');
        $this->assertEquals("", $this->subCompiler->compile($this->template, $this->template->getContents()));
        // Test class
        $this->template->setContents(
            '{{!Opulence\Tests\Views\Compilers\SubCompilers\Mocks\ClassWithStaticMethod::foo() == "bar" ? "y" : "n"!}}'
        );
        $this->assertEquals("y", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling an array variable inside tags
     */
    public function testCompilingArrayVariableInsideTags()
    {
        $delimiters = [
            [
                Template::DEFAULT_OPEN_SANITIZED_TAG_DELIMITER,
                Template::DEFAULT_CLOSE_SANITIZED_TAG_DELIMITER
            ],
            [
                Template::DEFAULT_OPEN_UNSANITIZED_TAG_DELIMITER,
                Template::DEFAULT_CLOSE_UNSANITIZED_TAG_DELIMITER
            ]
        ];
        $templateContents = '<?php foreach(["foo" => ["bar", "a&w"]] as $v): ?>%s$v[1]%s<?php endforeach; ?>';
        $this->template->setContents(sprintf($templateContents, $delimiters[0][0], $delimiters[0][1]));
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "a&amp;w",
                $this->subCompiler->compile($this->template, $this->template->getContents())
            )
        );
        $this->template->setContents(sprintf($templateContents, $delimiters[1][0], $delimiters[1][1]));
        $this->assertEquals("a&w", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling an escaped tag whose value is an unescaped tag
     */
    public function testCompilingEscapedTagWhoseValueIsUnescapedTag()
    {
        // Order here is important
        // We're testing setting the inner-most tag first, and then the outer tag
        $this->template->setContents("{{!content!}}");
        $this->template->setTag("message", "world");
        $this->template->setTag("content", "Hello, {{message}}!");
        $this->assertEquals(
            "Hello, world!",
            $this->subCompiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling a tag whose value is another tag
     */
    public function testCompilingTagWhoseValueIsAnotherTag()
    {
        // Order here is important
        // We're testing setting the inner-most tag first, and then the outer tag
        $this->template->setContents("{{!content!}}");
        $this->template->setTag("message", "world");
        $this->template->setTag("content", "Hello, {{!message!}}!");
        $this->assertEquals(
            "Hello, world!",
            $this->subCompiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling a template with PHP code
     */
    public function testCompilingTemplateWithPHPCode()
    {
        $contents = $this->fileSystem->read(__DIR__ . "/.." . self::TEMPLATE_PATH_WITH_PHP_CODE);
        $this->template->setContents($contents);
        $user1 = new User(1, "foo");
        $user2 = new User(2, "bar");
        $this->template->setTag("listDescription", "usernames");
        $this->template->setVar("users", [$user1, $user2]);
        $this->template->setVar("coolestGuy", "Dave");
        $functionResult = $this->registerFunction();
        $this->assertEquals(
            'TEST List of usernames on ' . $functionResult . ':
<ul>
    <li>foo</li><li>bar</li></ul> 2 items
<br>Dave is a pretty cool guy. Alternative syntax works! I agree. Fake closing PHP tag: ?>',
            $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling a variable inside tags
     */
    public function testCompilingVariableInsideTags()
    {
        $delimiters = [
            [
                Template::DEFAULT_OPEN_SANITIZED_TAG_DELIMITER,
                Template::DEFAULT_CLOSE_SANITIZED_TAG_DELIMITER
            ],
            [
                Template::DEFAULT_OPEN_UNSANITIZED_TAG_DELIMITER,
                Template::DEFAULT_CLOSE_UNSANITIZED_TAG_DELIMITER
            ]
        ];
        $templateContents = '<?php foreach(["a&w"] as $v): ?>%s$v%s<?php endforeach; ?>';
        $this->template->setContents(sprintf($templateContents, $delimiters[0][0], $delimiters[0][1]));
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "a&amp;w",
                $this->subCompiler->compile($this->template, $this->template->getContents())
            )
        );
        $this->template->setContents(sprintf($templateContents, $delimiters[1][0], $delimiters[1][1]));
        $this->assertEquals("a&w", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling an escaped string
     */
    public function testEscapedString()
    {
        $this->template->setContents('{{"a&w"}}');
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "a&amp;w",
                $this->subCompiler->compile($this->template, $this->template->getContents())
            )
        );
        $this->template->setContents("{{'a&w'}}");
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "a&amp;w",
                $this->subCompiler->compile($this->template, $this->template->getContents())
            )
        );
    }

    /**
     * Tests an escaped tag with quotes
     */
    public function testEscapedTagWithQuotes()
    {
        $this->template->setContents('\{{" "}}"');
        $this->assertEquals('{{" "}}"', $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling a template with a function that spans multiple lines
     */
    public function testFunctionThatSpansMultipleLines()
    {
        $this->compiler->registerTemplateFunction("foo", function ($input)
        {
            return $input . "bar";
        });
        $this->template->setContents("{{
        foo(
        'foo'
        )
        }}");
        $this->assertEquals("foobar", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling a template with a function that has spaces between the open and close tags
     */
    public function testFunctionWithSpacesBetweenTags()
    {
        $this->template->setContents('{{! foo("bar") !}}');
        $this->compiler->registerTemplateFunction("foo", function ($input)
        {
            echo $input;
        });
        $this->assertEquals("bar", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling a non-existent function
     */
    public function testInvalidFunction()
    {
        $this->setExpectedException(ViewCompilerException::class);
        $this->template->setContents('{{ foo() }}');
        $this->subCompiler->compile($this->template, $this->template->getContents());
    }

    /**
     * Tests compiling a template with multiple calls to the same function
     */
    public function testMultipleCallsOfSameFunction()
    {
        $this->compiler->registerTemplateFunction("foo",
            function ($param1 = null, $param2 = null)
            {
                if($param1 == null && $param2 == null)
                {
                    return "No params";
                }
                elseif($param1 == null)
                {
                    return "Param 2 set";
                }
                elseif($param2 == null)
                {
                    return "Param 1 set";
                }
                else
                {
                    return "Both params set";
                }
            }
        );
        $this->template->setContents(
            '{{!foo()!}}, {{!foo()!}}, {{!foo("bar")!}}, {{!foo(null, "bar")!}}, {{!foo("bar", "blah")!}}'
        );
        $this->assertEquals(
            'No params, No params, Param 1 set, Param 2 set, Both params set',
            $this->subCompiler->compile($this->template, $this->template->getContents())
        );
    }

    /**
     * Tests compiling nested PHP functions
     */
    public function testNestedPHPFunctions()
    {
        $this->template->setContents('{{!date(strtoupper("y"))!}}');
        $this->assertEquals(date("Y"), $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling nested template functions
     */
    public function testNestedTemplateFunctions()
    {
        $this->compiler->registerTemplateFunction("foo", function ()
        {
            return "bar";
        });
        $this->compiler->registerTemplateFunction("baz", function ($input)
        {
            return strrev($input);
        });
        $this->template->setContents('{{!baz(foo())!}}');
        $this->assertEquals("rab", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests that only outer quotes are stripped from string literals
     */
    public function testOnlyOuterQuotesGetStrippedFromStringLiterals()
    {
        $this->template->setVar("foo", true);
        $this->template->setContents('{{!$foo ? \' class="bar"\' : \'\'!}}');
        $this->assertEquals(' class="bar"', $this->subCompiler->compile($this->template, $this->template->getContents()));
        $this->template->setContents("{{!\$foo ? \" class='bar'\" : \"\"!}}");
        $this->assertEquals(" class='bar'", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling a PHP function
     */
    public function testPHPFunction()
    {
        $this->template->setContents('{{ date("Y") }}');
        $this->assertEquals(date("Y"), $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling a PHP function with template function
     */
    public function testPHPFunctionWithTemplateFunction()
    {
        $this->compiler->registerTemplateFunction("foo", function ()
        {
            return "Y";
        });
        $this->template->setContents('{{ date(foo()) }}');
        $this->assertEquals(date("Y"), $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling a string literal with escaped quotes
     */
    public function testStringLiteralWithEscapedQuotes()
    {
        // Test escaped strings
        $this->template->setContents("{{'fo\'o'}}");
        $this->assertTrue($this->stringsWithEncodedCharactersEqual(
            "fo&#039;o",
            $this->subCompiler->compile($this->template, $this->template->getContents())
        ));
        $this->template->setContents('{{"fo\"o"}}');
        $this->assertTrue($this->stringsWithEncodedCharactersEqual(
            'fo&quot;o',
            $this->subCompiler->compile($this->template, $this->template->getContents())
        ));
        // Test unescaped strings
        $this->template->setContents("{{!'fo\'o'!}}");
        $this->assertEquals("fo'o", $this->subCompiler->compile($this->template, $this->template->getContents()));
        $this->template->setContents('{{!"fo\"o"!}}');
        $this->assertEquals('fo"o', $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling a template with a tag that spans multiple lines
     */
    public function testTagThatSpansMultipleLines()
    {
        $this->template->setContents("{{
        foo
        }}");
        $this->template->setTag("foo", "bar");
        $this->assertEquals("bar", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests tag whose value is PHP code
     */
    public function testTagWithPHPValue()
    {
        $this->template->setTag("foo", '$bar->blah();');
        $this->template->setContents('{{!foo!}}');
        $this->assertEquals('$bar->blah();', $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling template function
     */
    public function testTemplateFunction()
    {
        $this->compiler->registerTemplateFunction("foo", function ()
        {
            return "a&w";
        });
        $this->template->setContents('{{foo()}}');
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "a&amp;w",
                $this->subCompiler->compile($this->template, $this->template->getContents())
            )
        );
        $this->template->setContents('{{!foo()!}}');
        $this->assertEquals("a&w", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling template function with string input
     */
    public function testTemplateFunctionWithStringInput()
    {
        $this->compiler->registerTemplateFunction("foo", function ($input)
        {
            return strrev($input);
        });
        $this->template->setContents('{{!foo("bar")!}}');
        $this->assertEquals("rab", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling a template that uses custom delimiters
     */
    public function testTemplateWithCustomDelimiters()
    {
        $contents = $this->fileSystem->read(__DIR__ . "/.." . self::TEMPLATE_PATH_WITH_CUSTOM_TAG_DELIMITERS);
        $this->template->setContents($contents);
        $this->template->setDelimiters(Template::DELIMITER_TYPE_UNSANITIZED_TAG, ["^^", "$$"]);
        $this->template->setDelimiters(Template::DELIMITER_TYPE_SANITIZED_TAG, ["++", "--"]);
        $this->template->setDelimiters(Template::DELIMITER_TYPE_DIRECTIVE, ["(*", "*)"]);
        $this->template->setTag("foo", "Hello");
        $this->template->setTag("bar", "world");
        $this->template->setTag("imSafe", "a&b");
        $this->template->setVar("today", time());
        $this->compiler->registerTemplateFunction("customDate", function ($date, $format, $args)
        {
            return "foo";
        });
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                'Hello, world! (*show("parttest")*). ^^blah$$. a&amp;b. me too. c&amp;d. e&f. ++"g&h"--. ++ "i&j" --. ++blah--. Today escaped is foo and unescaped is foo. (*part("parttest")*)It worked(*endpart*).',
                $this->subCompiler->compile($this->template, $this->template->getContents())
            )
        );
    }

    /**
     * Tests compiling a template that uses the default delimiters
     */
    public function testTemplateWithDefaultDelimiters()
    {
        $contents = $this->fileSystem->read(__DIR__ . "/.." . self::TEMPLATE_PATH_WITH_DEFAULT_TAG_DELIMITERS);
        $this->template->setContents($contents);
        $this->template->setTag("foo", "Hello");
        $this->template->setTag("bar", "world");
        $this->template->setTag("imSafe", "a&b");
        $this->template->setVar("today", time());
        $this->compiler->registerTemplateFunction("customDate", function ($date, $format, $args)
        {
            return "foo";
        });
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                'Hello, world! <%show("parttest")%>. {{!blah!}}. a&amp;b. me too. c&amp;d. e&f. {{"g&h"}}. {{ "i&j" }}. {{blah}}. Today escaped is foo and unescaped is foo. <%part("parttest")%>It worked<%endpart%>.',
                $this->subCompiler->compile($this->template, $this->template->getContents())
            )
        );
    }

    /**
     * Tests the ternary operator
     */
    public function testTernaryOperator()
    {
        $this->template->setVar("foo", true);
        $this->template->setContents('{{$foo ? "a&w" : ""}}');
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "a&amp;w",
                $this->subCompiler->compile($this->template, $this->template->getContents())
            )
        );
        $this->template->setContents('{{!$foo ? "a&w" : ""!}}');
        $this->assertEquals("a&w", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }

    /**
     * Tests compiling an unescaped string
     */
    public function testUnescapedString()
    {
        $this->template->setContents('{{!"foo"!}}');
        $this->assertEquals("foo", $this->subCompiler->compile($this->template, $this->template->getContents()));
        $this->template->setContents("{{!'foo'!}}");
        $this->assertEquals("foo", $this->subCompiler->compile($this->template, $this->template->getContents()));
    }
}