<?php

/*
 * This file is part of the the Twig extension Twi18n.
 * URL: http://github.com/jhogervorst/Twi18n
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
 * Token Parser for the 'transplural' tag.
 */
class Twi18n_Twig_TokenParser_TransPlural extends Twi18n_Twig_TokenParser_Trans
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

        $count = null;
        $domain = null;
        $plural = null;

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

        $body = $this->parser->subparse(array($this, 'decideTransPluralFork'));

        if (!$body instanceof Twig_Node_Text && !$body instanceof Twig_Node_Expression) {
            throw new Twig_Error_Syntax('A message must be a simple text.');
        }

        if ($stream->next()->getValue() !== 'plural') {
            throw new Twig_Error_Syntax('A plural tag is required.');
        }

        $count = $this->parser->getExpressionParser()->parseExpression();

        $stream->expect(Twig_Token::BLOCK_END_TYPE);
        $plural = $this->parser->subparse(array($this, 'decideTransPluralEnd'), true);

        if (!$plural instanceof Twig_Node_Text && !$plural instanceof Twig_Node_Expression) {
            throw new Twig_Error_Syntax('A message (plural) must be a simple text');
        }

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new Twi18n_Twig_Node_Trans($body, $domain, $plural, $count, $vars, $lineno, $this->getTag());
    }

    public function decideTransPluralFork($token)
    {
        return $token->test(array('plural', 'endtransplural'));
    }

    public function decideTransPluralEnd($token)
    {
        return $token->test(array('endtransplural'));
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'transplural';
    }
}
