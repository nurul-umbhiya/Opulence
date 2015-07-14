<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the different token types
 */
namespace RDev\Views\Compilers\Lexers\Tokens;

class TokenTypes
{
    /** Defines an expression token type */
    const T_EXPRESSION = "T_EXPRESSION";
    /** Defines a directive open token type */
    const T_DIRECTIVE_OPEN = "T_DIRECTIVE_OPEN";
    /** Defines a directive name token type */
    const T_DIRECTIVE_NAME = "T_DIRECTIVE_NAME";
    /** Defines a directive close token type */
    const T_DIRECTIVE_CLOSE = "T_DIRECTIVE_CLOSE";
    /** Defines a sanitized tag open token type */
    const T_SANITIZED_TAG_OPEN = "T_SANITIZED_TAG_OPEN";
    /** Defines a sanitized tag close token type */
    const T_SANITIZED_TAG_CLOSE = "T_SANITIZED_TAG_CLOSE";
    /** Defines an unsanitized tag open token type */
    const T_UNSANITIZED_TAG_OPEN = "T_UNSANITIZED_TAG_OPEN";
    /** Defines an unsanitized tag close token type */
    const T_UNSANITIZED_TAG_CLOSE = "T_UNSANITIZED_TAG_CLOSE";
}