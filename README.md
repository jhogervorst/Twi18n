Twig TranslationExtension
=========================

This unofficial Twig TranslationExtension provides the functionality of Symfony's Translation component while using PHP's gettext functions as a data source, without the need of using Symfony.

This extension offers much more functionality than Twig's official [I18n extension](http://twig.sensiolabs.org/doc/extensions/i18n.html). It is possible to specify a custom domain, use better replacement techniques and use custom intervals depending on a numeric value.

Installation
------------

The first thing you should do is setting up gettext:

    // Set language to Dutch
    putenv('LC_ALL=nl_NL');
    setlocale(LC_ALL, 'nl_NL');
    
    // Specify the location of the translation tables
    bindtextdomain('myAppPhp', 'path/to/locale/files');
    bind_textdomain_codeset('myAppPhp', 'UTF-8');
    
    // Choose domain
    textdomain('myAppPhp');

You need to autoload the extension. It's best to so this after you've (auto)loaded Twig.

    require_once 'path/to/lib/Twig-TranslationExtension/Twig/TranslationExtension/Autoloader.php';
    Twig_TranslationExtension_Autoloader::register();

After setting up Twig, you need to add the extension to your Twig environment:

    $twig->addExtension(new Twig_TranslationExtension_Extension_TranslationExtension());

Now you should be ready to go.

Usage
-----

Using the Twig tags `trans` and `transchoice` you can translate static messages. The tag `transchoice` uses the `%count%` variable to determine which part of the message should be used.

    {% trans %}Hello %name%{% endtrans %}
    
    {% transchoice count %}
        {0} There are no apples|{1} There is one apple|]1,Inf] There are %count% apples
    {% endtranschoice %}

In the previous example, the domain chosen in your gettext configuration (first code snippet) is used. It is possible to specify a custom domain. Besides that you can pass additional variables. These two things can be combined:

    {% trans from "app" %}Hello %name%{% endtrans %}
    
    {% trans with {'%name%': 'Fabien'} %}Hello %name%{% endtrans %}
    
    {% trans with {'%name%': 'Fabien'} from "app" %}Hello %name%{% endtrans %}
    
    {% transchoice count from "app" %}
        {0} There is no apples|{1} There is one apple|]1,Inf] There are %count% apples
    {% endtranschoice %}
    
    {% transchoice count with {'%name%': 'Fabien'} %}
        {0} There is no apples, %name%|{1} There is one apple, %name%|]1,Inf] There are %count% apples, %name%
    {% endtranschoice %}
    
    {% transchoice count with {'%name%': 'Fabien'} from "app" %}
        {0} There is no apples, %name%|{1} There is one apple, %name%|]1,Inf] There are %count% apples, %name%
    {% endtranschoice %}

Additional resources
--------------------

Take a look at Symfony's documentation to read more about the [Twig syntaxis](http://symfony.com/doc/current/book/translation.html#twig-templates). Note that it is not possible to specify a custom locale using the `into` operator.

Take a look at the PHP documentation for more information about [gettext](http://nl.php.net/manual/en/book.gettext.php).

License and sources
-------------------

This extension is a modified version of parts of the Symfony package and the included Twig library. For the full copyright and license information, please view the `LICENSE` file that was distributed with this source code.

This documentation is based on the [Symfony documentation](http://symfony.com/doc/current/book/translation.html#twig-templates), [Twig documentation](http://twig.sensiolabs.org/doc/extensions/i18n.html) and [PHP documentation](http://nl.php.net/manual/en/function.gettext.php).
