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
 * Provides integration of the Translation component with Twig.
 */
class Twi18n_Twig_Extension_Twi18n extends Twig_Extension
{
    protected $locale;
    private $selector;

    /**
     * Constructor.
     *
     * @param string $localeThe locale
     */
    public function __construct($locale = null)
    {
        $this->locale = $locale;
        $this->selector = new Twi18n_Symfony_Component_Translation_MessageSelector();
    }

    /**
     * Set the locale.
     *
     * @param string $locale The locale
     */
    public function setLocale($locale = null)
    {
        $this->locale = $locale;
    }

    /**
     * Get the locale.
     *
     * @param bool $use_global Use PHP's global locale when no locale is set (default: false).
     *
     * @return string The locale
     */
    public function getLocale($use_global = false)
    {
        return !empty($this->locale) ? $this->locale : setlocale(LC_MESSAGES, '0');
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
            'transplural' => new Twig_Filter_Method($this, 'transPlural'),
            'transchoice' => new Twig_Filter_Method($this, 'transChoice'),
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
            new Twi18n_Twig_TokenParser_Trans(),

            // {% transplural %}
            //     There is one apple
            // {% plural %}
            //     There are {{ count }} apples
            // {% endtransplural %}
            new Twi18n_Twig_TokenParser_TransPlural(),

            // {% transchoice count %}
            //     {0} There are no apples|{1} There is one apple|]1,Inf] There are {{ count }} apples
            // {% endtranschoice %}
            new Twi18n_Twig_TokenParser_TransChoice(),
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
     * Translates the given plural message.
     *
     * @param integer $number    The number to use to find the indice of the message
     * @param string $message1   The singular message id
     * @param string $message2   The plural message id
     * @param array  $arguments  An array of parameters for the message
     * @param string $domain     The domain for the message
     *
     * @return string The translated string
     */
    public function transPlural($number, $message1, $message2, array $arguments = array(), $domain = null)
    {
        // Get the translated message from gettext
        $message = !empty($domain) ? dngettext($domain, $message1, $message2, abs($number)) : ngettext($message1, $message2, abs($number));

        // Add the specified number to the array of arguments
        $arguments = array_merge($arguments, array('%count%' => $number));

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
        $message = $this->selector->choose($message, (int) $number, $this->getLocale(true));

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
