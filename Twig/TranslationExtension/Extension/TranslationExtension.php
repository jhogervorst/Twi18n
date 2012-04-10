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
 * Provides integration of the Translation component with Twig.
 */
class Twig_TranslationExtension_Extension_TranslationExtension extends Twig_Extension
{
    protected $locale;
    private $selector;

    /**
     * Constructor.
     *
     * @param string          $locale   The locale
     */
    public function __construct($locale = null)
    {
        if (empty($locale)) {
            // Get the current locale
            $locale = setlocale(LC_MESSAGES, '0');
        }

        $this->locale = $locale;
        $this->selector = new Twig_TranslationExtension_SymfonyComponents_Translation_MessageSelector();
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(
            'trans' => new Twig_Filter_Method($this, 'trans'),
            'transchoice' => new Twig_Filter_Method($this, 'transchoice'),
        );
    }

    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return array An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
     */
    public function getTokenParsers()
    {
        return array(
            // {% trans %}Symfony is great!{% endtrans %}
            new Twig_TranslationExtension_TokenParser_Trans(),

            // {% transchoice count %}
            //     {0} There is no apples|{1} There is one apple|]1,Inf] There is {{ count }} apples
            // {% endtranschoice %}
            new Twig_TranslationExtension_TokenParser_TransChoice(),
        );
    }

    /**
     * Translates the given message.
     *
     * @param string $message    The message id
     * @param array  $arguments  An array of parameters for the message
     * @param string $domain     The domain for the message
     *
     * @return string The translated string
     */
    public function trans($message, array $arguments = array(), $domain = null)
    {
        // Get the translated message from gettext
        $message = !empty($domain) ? dgettext($domain, $message) : gettext($message);

        // Fill in the arguments
        $message = strtr($message, $arguments);

        return $message;
    }

    /**
     * Translates the given choice message by choosing a translation according to a number.
     *
     * @param string  $message    The message id
     * @param integer $number     The number to use to find the indice of the message
     * @param array   $arguments  An array of parameters for the message
     * @param string  $domain     The domain for the message
     *
     * @return string The translated string
     */
    public function transChoice($message, $number, array $arguments = array(), $domain = null)
    {
        // Get the translated message from gettext
        $message = !empty($domain) ? dgettext($domain, $message) : gettext($message);

        // Pick the right text for the current count
        $message = $this->selector->choose($message, (int) $number, $this->locale);

        // Add the specified number to the array of arguments
        $arguments = array_merge($arguments, array('%count%' => $number));

        // Fill in the arguments
        $message = strtr($message, $arguments);

        return $message;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'translator';
    }
}
