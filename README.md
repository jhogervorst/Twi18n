Twi18n
======

Powerful Twig translation extension using PHP's gettext. This extension provides the functionality of Symfony's Translation component while using PHP's gettext functions as data source, without the need of using Symfony.

This extension offers much more functionality than Twig's official [i18n extension](http://twig.sensiolabs.org/doc/extensions/i18n.html). It is possible to specify a custom domain, use better replacement techniques and use custom intervals depending on a numeric value.

Configuration
-------------

You must configure the gettext extension before rendering any internationalized template. Here is a simple configuration example from the [PHP documentation](http://nl.php.net/manual/en/function.gettext.php):

    // Set language to Dutch
    putenv('LC_ALL=nl_NL');
    setlocale(LC_ALL, 'nl_NL');
    
    // Specify the location of the translation tables
    bindtextdomain('myAppPhp', 'path/to/locale/files');
    bind_textdomain_codeset('myAppPhp', 'UTF-8');
    
    // Choose domain
    textdomain('myAppPhp');

The first step to use Twi18n is to register its autoloader:

    require_once 'path/to/lib/Twi18n/Autoloader.php';
    Twi18n_Autoloader::register();

It's best to do this after registering Twig's autoloader.

The Twi18n extension adds gettext support to Twig. It defines two tag, `trans` and `transchoice`. You need to register this extension before using one of the blocks:

    $twig->addExtension(new Twi18n_Twig_Extension_Twi18n());

Now you should be ready to go.

Usage
-----

Twi18n provides specialized Twig tags (`trans` and `transchoice`) to help with message translation of static blocks of text:

    {% trans %}Hello %name%{% endtrans %}
    
    {% transchoice count %}
        {0} There are no apples|{1} There is one apple|]1,Inf] There are %count% apples
    {% endtranschoice %}

The `transchoice` tag automatically gets the `%count%` variable from the current context and passes it to the translator. This mechanism only works when you use a placeholder following the `%var%` pattern.

You can also specify the message domain and pass some additional variables:

    {% trans with {'%name%': 'Fabien'} from "app" %}Hello %name%{% endtrans %}
    
    {% transchoice count with {'%name%': 'Fabien'} from "app" %}
        {0} There is no apples|{1} There is one apple|]1,Inf] There are %count% apples
    {% endtranschoice %}

The trans and transchoice filters can be used to translate variable texts and complex expressions:

    {{ message|trans }}
    
    {{ message|transchoice(5) }}
    
    {{ message|trans({'%name%': 'Fabien'}, 'app') }}
    
    {{ message|transchoice(5, {'%name%': 'Fabien'}, 'app') }}

Learn more
----------

Take a look at Symfony's documentation to read more about the [Twig syntaxis](http://symfony.com/doc/current/book/translation.html#twig-templates). Note that it is not possible to specify a custom locale using the `into` operator.

Take a look at the PHP documentation for more information about [gettext](http://nl.php.net/manual/en/book.gettext.php).

License
-------

Twi18n is a modified version of parts of the Symfony package and it's included Twig library. For the full copyright and license information, please view the `LICENSE` file that is distributed with the source code.

Twi18n documentation is licensed under a Creative Commons Attribution-Share Alike 3.0 Unported [License](http://creativecommons.org/licenses/by-sa/3.0/) and is based on the [Symfony documentation](http://symfony.com/doc/current/book/translation.html#twig-templates), [Twig documentation](http://twig.sensiolabs.org/doc/extensions/i18n.html) and [PHP documentation](http://nl.php.net/manual/en/function.gettext.php).
