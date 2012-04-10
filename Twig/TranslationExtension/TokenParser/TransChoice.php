<?php

/*
 * This file is part of the unofficial Twig TranslationExtension.
 * URL: http://github.com/jhogervorst/Twig-TranslationExtension
 * 
 * This file was part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) 2012 Jonathan Hogervorst
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Token Parser for the 'transchoice' tag.
 */
class Twig_TranslationExtension_TokenParser_TransChoice extends Twig_TranslationExtension_TokenParser_Trans
{
    /**
     * Parses a token and returns a node.
     *
     * @param  Twig_Token $token A Twig_Token instance
     *
     * @return Twig_NodeInterface A Twig_NodeInterface instance
     */
    public function parse(Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $vars = new Twig_Node_Expression_Array(array(), $lineno);

        $count = $this->parser->getExpressionParser()->parseExpression();

        $domain = null;

        if ($stream->test('with')) {
            // {% transchoice count with vars %}
            $stream->next();
            $vars = $this->parser->getExpressionParser()->parseExpression();
        }

        if ($stream->test('from')) {
            // {% transchoice count from "messages" %}
            $stream->next();
            $domain = $this->parser->getExpressionParser()->parseExpression();
        }

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse(array($this, 'decideTransChoiceFork'), true);

        if (!$body instanceof Twig_Node_Text && !$body instanceof Twig_Node_Expression) {
            throw new Twig_Error_Syntax('A message must be a simple text.');
        }

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new Twig_TranslationExtension_Node_Trans($body, $domain, $count, $vars, $lineno, $this->getTag());
    }

    public function decideTransChoiceFork($token)
    {
        return $token->test(array('endtranschoice'));
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'transchoice';
    }
}
