<?php

/*
 * This file is part of the the Twig extension Twi18n.
 * URL: http://github.com/jhogervorst/Twi18n
 * 
 * This file was part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2012 Jonathan Hogervorst
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Autoloads Twig TranslationExtension classes.
 */
class Twi18n_Autoloader
{
    /**
     * Registers Twig_TranslationExtension_Autoloader as an SPL autoloader.
     */
    static public function register()
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self, 'autoload'));
    }

    /**
     * Handles autoloading of classes.
     *
     * @param  string  $class  A class name.
     *
     * @return boolean Returns true if the class has been loaded
     */
    static public function autoload($class)
    {
        if (0 !== strpos($class, 'Twi18n')) {
            return;
        }

        if (file_exists($file = dirname(__FILE__).'/../'.str_replace('_', '/', $class).'.php')) {
            require $file;
        }
    }
}
