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
class Twi18n_Twig_Node_Trans extends Twig_Node
{
    public function __construct(Twig_NodeInterface $body, Twig_NodeInterface $domain = null, Twig_NodeInterface $plural = null, Twig_Node_Expression $count = null, Twig_Node_Expression $vars = null, $lineno = 0, $tag = null)
    {
        parent::__construct(array('count' => $count, 'body' => $body, 'domain' => $domain, 'plural' => $plural, 'vars' => $vars), array(), $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $vars = $this->getNode('vars');
        $defaults = new Twig_Node_Expression_Array(array(), -1);
        if ($vars instanceof Twig_Node_Expression_Array) {
            $defaults = $this->getNode('vars');
            $vars = null;
        }
        list($msg, $defaults) = $this->compileString($this->getNode('body'), $defaults);

        $method = $this->getNode('plural') ? 'transPlural' : ($this->getNode('count') ? 'transChoice' : 'trans');

        $compiler
            ->write('echo $this->env->getExtension(\'translator\')->'.$method.'(')
        ;

        if ($method == 'trans') {
            $compiler
                ->subcompile($msg)
                ->raw(', ')
            ;
        } elseif ($method == 'transPlural') {
            list($plural) = $this->compileString($this->getNode('plural'), $defaults);

            $compiler
                ->subcompile($this->getNode('count'))
                ->raw(', ')
                ->subcompile($msg)
                ->raw(', ')
                ->subcompile($plural)
                ->raw(', ')
            ;
        } elseif ($method == 'transChoice') {
            $compiler
                ->subcompile($msg)
                ->raw(', ')
                ->subcompile($this->getNode('count'))
                ->raw(', ')
            ;
        }

        if (null !== $vars) {
            $compiler
                ->raw('array_merge(')
                ->subcompile($defaults)
                ->raw(', ')
                ->subcompile($this->getNode('vars'))
                ->raw(')')
            ;
        } else {
            $compiler->subcompile($defaults);
        }

        if (null !== $this->getNode('domain')) {
            $compiler
                ->raw(', ')
                ->subcompile($this->getNode('domain'))
            ;
        }
        $compiler->raw(");\n");
    }

    protected function compileString(Twig_NodeInterface $body, Twig_Node_Expression_Array $vars)
    {
        if ($body instanceof Twig_Node_Expression_Constant) {
            $msg = $body->getAttribute('value');
        } elseif ($body instanceof Twig_Node_Text) {
            $msg = $body->getAttribute('data');
        } else {
            return array($body, $vars);
        }

        preg_match_all('/(?<!%)%([^%]+)%/', $msg, $matches);

        if (version_compare(Twig_Environment::VERSION, '1.5', '>=')) {
            foreach ($matches[1] as $var) {
                $key = new Twig_Node_Expression_Constant('%'.$var.'%', $body->getLine());
                if (!$vars->hasElement($key)) {
                    $vars->addElement(new Twig_Node_Expression_Name($var, $body->getLine()), $key);
                }
            }
        } else {
            $current = array();
            foreach ($vars as $name => $var) {
                $current[$name] = true;
            }
            foreach ($matches[1] as $var) {
                if (!isset($current['%'.$var.'%'])) {
                    $vars->setNode('%'.$var.'%', new Twig_Node_Expression_Name($var, $body->getLine()));
                }
            }
        }

        return array(new Twig_Node_Expression_Constant(str_replace('%%', '%', trim($msg)), $body->getLine()), $vars);
    }
}
