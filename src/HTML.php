<?php

/**
 * Class HTML
 *
 */
class HTML
{
    protected static $documentType = 11;

    protected static $autoEscape = true;

    const HTML32 = 1;

    const HTML401_STRICT = 2;

    const HTML401_TRANSITIONAL = 3;

    const HTML401_FRAMESET = 4;

    const HTML5 = 5;

    const XHTML10_STRICT = 6;

    const XHTML10_TRANSITIONAL = 7;

    const XHTML10_FRAMESET = 8;

    const XHTML11 = 9;

    const XHTML20 = 10;

    const XHTML5 = 11;

    /**
     * Renders parameters keeping order in their HTML attributes
     * @param $code
     * @param array $attributes
     */
    public static function renderAttributes($code, array $attributes = [])
    {
    }

    /**
     * Set autoescape mode in generated html
     * @param bool $autoescape
     */
    public static function setAutoescape($autoescape)
    {
    }

    /**
     * Assigns default values to generated tags by helpers
     * @param string $id
     * @param mixed $value
     */
    public static function setDefault($id, $value)
    {
    }

    /**
     * Assigns default values to generated tags by helpers
     * @param array $values
     * @param bool $merge
     */
    public static function setDefaults(array $values, $merge)
    {
    }

    /**
     * Builds generic INPUT tags
     * @param string $type
     * @param mixed $parameters
     * @param bool $asValue
     * @return string
     */
    protected static function inputField($type, $parameters, $asValue = false)
    {
    }

    /**
     * Builds INPUT tags that implements the checked attribute
     * @param string $type
     * @param mixed $parameters
     * @return string
     */
    protected static function inputFieldChecked($type, $parameters)
    {
    }

    /**
     * Builds a HTML input[type="color"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function colorField($parameters)
    {
        return self::inputField("color", $parameters);
    }

    /**
     * Builds a HTML input[type="text"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function textField($parameters)
    {
        return self::inputField("text", $parameters);
    }

    /**
     * Builds a HTML input[type="number"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function numericField($parameters)
    {
        return self::inputField("number", $parameters);
    }

    /**
     * Builds a HTML input[type="range"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function rangeField($parameters)
    {
        return self::inputField("range", $parameters);
    }

    /**
     * Builds a HTML input[type="email"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function emailField($parameters)
    {
        return self::inputField("email", $parameters);
    }

    /**
     * Builds a HTML input[type="date"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function dateField($parameters)
    {
        return self::inputField("date", $parameters);
    }

    /**
     * Builds a HTML input[type="datetime"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function dateTimeField($parameters)
    {
        return self::inputField("datetime", $parameters);
    }

    /**
     * Builds a HTML input[type="datetime-local"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function dateTimeLocalField($parameters)
    {
        return self::inputField("datetime-local", $parameters);
    }

    /**
     * Builds a HTML input[type="month"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function monthField($parameters)
    {
        return self::inputField("month", $parameters);
    }

    /**
     * Builds a HTML input[type="time"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function timeField($parameters)
    {
        return self::inputField("time", $parameters);
    }

    /**
     * Builds a HTML input[type="week"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function weekField($parameters)
    {
        return self::inputField("week", $parameters);
    }

    /**
     * Builds a HTML input[type="password"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function passwordField($parameters)
    {
        return self::inputField("password", $parameters);
    }

    /**
     * Builds a HTML input[type="hidden"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function hiddenField($parameters)
    {
        return self::inputField("hidden", $parameters);
    }

    /**
     * Builds a HTML input[type="file"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function fileField($parameters)
    {
        return self::inputField("file", $parameters);
    }

    /**
     * Builds a HTML input[type="search"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function searchField($parameters)
    {
        return self::inputField("search", $parameters);
    }

    /**
     * Builds a HTML input[type="tel"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function telField($parameters)
    {
        return self::inputField("tel", $parameters);
    }

    /**
     * Builds a HTML input[type="url"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function urlField($parameters)
    {
        return self::inputField("url", $parameters);
    }

    /**
     * Builds a HTML input[type="check"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function checkField($parameters)
    {
        return self::inputFieldChecked("checkbox", $parameters);
    }

    /**
     * Builds a HTML input[type="radio"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function radioField($parameters)
    {
        return self::inputFieldChecked("radio", $parameters);
    }

    /**
     * Builds a HTML input[type="image"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function imageInput($parameters)
    {
        return self::inputField("image", $parameters, true);
    }

    /**
     * Builds a HTML input[type="submit"] tag
     * @param mixed $parameters
     * @return string
     */
    public static function submitButton($parameters)
    {
        return self::inputField("submit", $parameters, true);
    }

    /**
     * Builds a HTML SELECT tag using a PHP array for options
     * @param mixed $parameters
     * @param mixed $data
     * @return string
     */
    public static function select($parameters, $data)
    {
    }
    
    /**
     * Builds a HTML TEXTAREA tag
     * @param mixed $parameters
     * @return string
     */
    public static function textArea($parameters)
    {
    }

    /**
     * Builds a HTML FORM tag
     * @param mixed $parameters
     * @return string
     */
    public static function form($parameters)
    {
    }

    /**
     * Builds a HTML close FORM tag
     * @return string
     */
    public static function endForm()
    {
        return "</form>";
    }

}
