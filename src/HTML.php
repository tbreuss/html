<?php

namespace Tebe;

/**
 * Class HTML
 *
 */
class HTML
{
    protected static $defaults = [];

    protected static $encoding = "utf-8";

    protected static $documentType = 11;

    protected static $autoEscape = true;

    protected static $escaper = null;

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
     * Obtains the 'escaper' callback if required. If no escaper was registered
     * a default escaper will be returned. So if you wish to use NO escaper, you
     * have to explicitly disable the escaper by calling setEscaper(null);
     *
     * @param array $params
     * @return callable|null
     */
    public static function getEscaper(array $params = [])
    {
        $autoescape = self::$autoEscape;
        if (isset($params["escape"])) {
            $autoescape = $params["escape"];
        }

        if ($autoescape) {

            if (is_null(self::$escaper)) {
                // Register the default escaper
                $callback = function ($attribute) {
                    return htmlspecialchars($attribute, ENT_QUOTES, self::$encoding);
                };
                self::setEscaper($callback);
            }

        }

        return self::$escaper;
    }

    /**
     * Set the escaper. If param $callback is NULL an callback is registered as a "null" callback.
     *
     * @param callable|null $callback
     * @throws Exception
     */
    public static function setEscaper($callback)
    {
        if (is_null($callback)) {
            $callback = function ($value) { return $value; };
        }
        if (!is_callable($callback)) {
            throw new Exception('Param $callback is not callable.');
        }
        self::$escaper = $callback;
    }

    /**
     * Renders parameters keeping order in their HTML attributes
     *
     * @param string $code
     * @param array $attributes
     * @return string
     * @throws Exception
     */
    public static function renderAttributes($code, array $attributes = [])
    {
        $order = ["rel", "type", "for", "src", "href", "action", "id", "name", "value", "class"];

        $attrs = [];

        foreach ($order as $key) {
            if (isset($attributes[$key])) {
                $attrs[$key] = $attributes[$key];
            }
        }

        foreach ($attributes as $key => $value) {
            if (!isset($attrs[$key])) {
                $attrs[$key] = $value;
            }
        }

        $escaperCallback = self::getEscaper($attributes);
        unset ($attrs["escape"]);

        $newCode = $code;
        foreach ($attrs as $key => $value) {
            if (is_null($value)) {
                continue;
            }
            if (is_string($key)) {
                if (!is_scalar($value)) {
                    throw new Exception("Value at index: '" . $key . "' type: '" . gettype($value) . "' cannot be rendered");
                }
                $escaped = call_user_func($escaperCallback, $value);
                $newCode .= " " . $key . "=\"" . $escaped . "\"";
            } elseif (is_numeric($key)) {
                $newCode .= " " . $value;
            }
        }

        return $newCode;
    }

    /**
     * Set encoding
     *
     * @param $encoding
     */
    public static function setEncoding($encoding)
    {
        self::$encoding = $encoding;
    }

    /**
     * Set autoescape mode in generated html
     *
     * @param bool $autoEscape
     */
    public static function setAutoescape($autoEscape)
    {
        self::$autoEscape = (bool)$autoEscape;
    }

    /**
     * Assigns default values to generated tags by helpers
     *
     * @param string $name
     * @param mixed $value
     * @throws Exception
     */
    public static function setDefault($name, $value)
    {
        if (!(is_scalar($value) || is_array($value))) {
            throw new Exception("Only scalar and array values can be assigned to UI components (" . gettype($value) . " given).");
        }
        if ($value !== null) {
            self::$defaults[$name] = $value;
        }
    }

    /**
     * Assigns default values to generated tags by helpers
     *
     * @param array $defaults
     * @param bool $merge
     */
    public static function setDefaults(array $defaults, $merge = false)
    {
        if (false === $merge) {
            self::$defaults = [];
        }
        foreach ($defaults as $name => $value) {
            self::setDefault($name, $value);
        }
    }

    /**
     * Return all default values
     *
     * @return array
     */
    public static function getDefaults()
    {
        return self::$defaults;
    }

    /**
     * Check if a helper has a default value using HTML::setDefault or value from _POST
     *
     * @param string $name
     * @return boolean
     */
    public static function hasValue($name)
    {
        $value = self::getValue($name);
        return $value !== null;
    }

    /**
     * Every helper calls this function to check whether a component has a predefined
     * value using self::setDefault or value from _POST
     *
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public static function getValue($name, array $params = [])
    {
        $value = null;

        if (isset($params["value"])) {

            // an explicit element value
            $value = $params["value"];

        } elseif (!empty($_POST)) {

            // a POST value
            $value = self::resolveValue($name, $_POST);

        } else {

            // a previously set default value
            $value = self::resolveValue($name, self::$defaults);

        }

        return $value;
    }

    /**
     * Evaluate the value from the data (i.e. $_POST or $defaults)
     *
     * @param string $name
     * @param array $data
     * @return mixed|null
     */
    protected static function resolveValue($name, array $data)
    {
        if (($pos = strpos($name, '[')) !== false) {
            if ($pos === 0) // [a]name[b][c], should ignore [a]
            {
                if (preg_match('/\](\w+(\[.+)?)/', $name, $matches)) {
                    $name = $matches[1]; // we get: name[b][c]
                }
                if (($pos = strpos($name, '[')) === false) {
                    return $data[$name];
                }
            }

            $value = self::fetch($data, substr($name, 0, $pos));

            foreach (explode('][', rtrim(substr($name, $pos + 1), ']')) as $id) {
                if (is_array($value) && isset($value[$id])) {
                    $value = $value[$id];
                } elseif (strlen($id) == 0) {
                    return $value;
                } else {
                    return null;
                }
            }
            return $value;
        } else {
            return self::fetch($data, $name);
        }
    }

    /**
     * Fetch an array value, return null if key doesn't exist
     *
     * @param array $data
     * @param string $key
     * @param mixed $default
     * @return mixed|null
     */
    protected static function fetch(array $data, $key, $default = null)
    {
        if (isset($data[$key])) {
            return $data[$key];
        }
        return $default;
    }

    /**
     * Builds a generic INPUT tag
     *
     * @param string $type
     * @param mixed $parameters
     * @param bool $asValue
     * @return string
     */
    protected static function inputField($type, $parameters, $asValue = false)
    {
        if (is_array($parameters)) {
            $params = $parameters;
        } else {
            $params = [$parameters];
        }

        if ($asValue == false) {

            $name = $params[0] ?: null;

            if (is_null($name)) {
                $params[0] = $params["id"] ?: null;
            }

            if (isset($params["name"])) {
                if (empty($params["name"])) {
                    $params["name"] = $name;
                }
            } else {
                $params["name"] = $name;
            }

            /**
             * Automatically assign the id if the name is not an array
             * --> Does this make sense?
             */
            if (is_string($name)) {
                if ((strpos($name, "[") === false) && !isset($params["id"])) {
                    $params["id"] = $name;
                }
            }

            $params["value"] = self::getValue($name, $params);

        } else {
            /**
             * Use the "id" as value if the user hadn't set it
             */
            if (!isset($params["value"])) {
                if (isset($params[0])) {
                    $params["value"] = $params[0];
                }
            }
        }

        unset($params[0]);
        $params["type"] = $type;
        $code = self::renderAttributes("<input", $params);

        /**
         * Check if Doctype is XHTML
         */
        if (self::$documentType > self::HTML5) {
            $code .= " />";
        } else {
            $code .= ">";
        }

        return $code;
    }

    /**
     * Builds an INPUT tag that implements the checked attribute
     *
     * @param string $type
     * @param mixed $parameters
     * @return string
     */
    protected static function inputFieldChecked($type, $parameters)
    {
        if (is_array($parameters)) {
            $params = $parameters;
        } else {
            $params = [$parameters];
        }

        if (!isset($params[0])) {
            $params[0] = $params["id"];
        }

        $id = $params[0];
        if (!isset($params["name"])) {
            $params["name"] = $id;
        } else {
            $name = $params["name"];
            if (empty($name)) {
                $params["name"] = $id;
            }
        }

        /**
         * Automatically assign the id if the name is not an array
         * --> Does this make sense?
         */
        if (false === strpos($id, "[")) {
            if (!isset($params["id"])) {
                $params["id"] = $id;
            }
        }

        /**
         * Automatically check inputs
         */
        if (isset($params["value"])) {

            $currentValue = $params["value"];
            unset($params["value"]);

            $value = self::getValue($id, $params);

            if ($value && ($currentValue == $value)) {
                $params["checked"] = "checked";
            }
            $params["value"] = $currentValue;

        } else {

            $value = self::getValue($id, $params);

            /**
             * Evaluate the value in POST
             */
            if ($value) {
                $params["checked"] = "checked";
            }

            /**
             * Update the value anyways
             */
            $params["value"] = $value;
        }

        unset($params[0]);
        $params["type"] = $type;
        $code = self::renderAttributes("<input", $params);

        /**
         * Check if Doctype is XHTML
         */
        if (self::$documentType > self::HTML5) {
            $code .= " />";
        } else {
            $code .= ">";
        }

        return $code;
    }

    /**
     * Builds a HTML input[type="color"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function colorField($parameters)
    {
        return self::inputField("color", $parameters);
    }

    /**
     * Builds a HTML input[type="text"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function textField($parameters)
    {
        return self::inputField("text", $parameters);
    }

    /**
     * Builds a HTML input[type="number"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function numberField($parameters)
    {
        return self::inputField("number", $parameters);
    }

    /**
     * Builds a HTML input[type="range"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function rangeField($parameters)
    {
        return self::inputField("range", $parameters);
    }

    /**
     * Builds a HTML input[type="email"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function emailField($parameters)
    {
        return self::inputField("email", $parameters);
    }

    /**
     * Builds a HTML input[type="date"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function dateField($parameters)
    {
        return self::inputField("date", $parameters);
    }

    /**
     * Builds a HTML input[type="datetime"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function dateTimeField($parameters)
    {
        return self::inputField("datetime", $parameters);
    }

    /**
     * Builds a HTML input[type="datetime-local"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function dateTimeLocalField($parameters)
    {
        return self::inputField("datetime-local", $parameters);
    }

    /**
     * Builds a HTML input[type="month"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function monthField($parameters)
    {
        return self::inputField("month", $parameters);
    }

    /**
     * Builds a HTML input[type="time"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function timeField($parameters)
    {
        return self::inputField("time", $parameters);
    }

    /**
     * Builds a HTML input[type="week"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function weekField($parameters)
    {
        return self::inputField("week", $parameters);
    }

    /**
     * Builds a HTML input[type="password"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function passwordField($parameters)
    {
        return self::inputField("password", $parameters);
    }

    /**
     * Builds a HTML input[type="hidden"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function hiddenField($parameters)
    {
        return self::inputField("hidden", $parameters);
    }

    /**
     * Builds a HTML input[type="file"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function fileField($parameters)
    {
        return self::inputField("file", $parameters);
    }

    /**
     * Builds a HTML input[type="search"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function searchField($parameters)
    {
        return self::inputField("search", $parameters);
    }

    /**
     * Builds a HTML input[type="tel"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function telField($parameters)
    {
        return self::inputField("tel", $parameters);
    }

    /**
     * Builds a HTML input[type="url"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function urlField($parameters)
    {
        return self::inputField("url", $parameters);
    }

    /**
     * Builds a HTML input[type="check"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function checkField($parameters)
    {
        return self::inputFieldChecked("checkbox", $parameters);
    }

    /**
     * Builds a HTML input[type="radio"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function radioField($parameters)
    {
        return self::inputFieldChecked("radio", $parameters);
    }

    /**
     * Builds a HTML input[type="image"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function imageButton($parameters)
    {
        return self::inputField("image", $parameters, true);
    }

    /**
     * Builds a HTML input[type="button"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function pushButton($parameters)
    {
        return self::inputField("button", $parameters, true);
    }

    /**
     * Builds a HTML input[type="reset"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function resetButton($parameters)
    {
        return self::inputField("reset", $parameters, true);
    }

    /**
     * Builds a HTML input[type="submit"] tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function submitButton($parameters)
    {
        return self::inputField("submit", $parameters, true);
    }

    /**
     * Builds a HTML SELECT tag using a PHP array for options
     *
     * @param mixed $parameters
     * @param array $data
     * @return string
     */
    public static function select($parameters, array $data)
    {
        $params = self::processFormElementParams($parameters);

        if (isset($params["value"])) {
            $value = $params["value"];
            unset($params["value"]);
        } else {
            $name = $params["name"];
            $value = self::getValue($name, $params);
        }

        $useEmpty = "";
        $emptyValue = "";
        $emptyText = "";

        if (isset($params["useEmpty"])) {
            $useEmpty = $params["useEmpty"];
            if (isset($params["emptyValue"])) {
                $emptyValue = $params["emptyValue"];
                unset($params["emptyValue"]);
            }
            if (isset($params["emptyText"])) {
                $emptyText = $params["emptyText"];
                unset($params["emptyText"]);
            } else {
                $emptyText = "Choose...";
            }
            unset($params["useEmpty"]);
        }

        unset($params[0]);
        $code = self::renderAttributes("<select", $params) . ">";

        if ($useEmpty) {
            $code .= "<option value=\"" . $emptyValue . "\">" . $emptyText . "</option>";
        }

        $code .= self::optionsFromArray($data, $value, "</option>");
        $code .= "</select>";

        return $code;
    }

    /**
     * Generate the OPTION tags based on an array
     *
     * @param array $data
     * @param string $value
     * @param string $closeOption
     * @return string
     */
    public static function optionsFromArray(array $data, $value, $closeOption)
    {
        $code = "";

        foreach ($data as $optionValue => $optionText) {

            $escaped = htmlspecialchars($optionValue);

            if (is_array($optionText)) {
                $code .= "<optgroup label=\"" . $escaped . "\">" . self::optionsFromArray($optionText, $value, $closeOption) . "</optgroup>";
                continue;
            }

            if (is_array($value)) {
                if (in_array($optionValue, $value)) {
                    $code .= "<option selected=\"selected\" value=\"" . $escaped . "\">" . $optionText . $closeOption;
                } else {
                    $code .= "<option value=\"" . $escaped . "\">" . $optionText . $closeOption;
                }
            } else {
                $strOptionValue = (string)$optionValue;
                $strValue = (string)$value;

                if ($strOptionValue === $strValue) {
                    $code .= "<option selected=\"selected\" value=\"" . $escaped . "\">" . $optionText . $closeOption;
                } else {
                    $code .= "<option value=\"" . $escaped . "\">" . $optionText . $closeOption;
                }
            }
        }

        return $code;
    }

    /**
     * Builds a HTML TEXTAREA tag
     *
     * @param mixed $parameters
     * @return string
     */
    public static function textArea($parameters)
    {
        $params = self::processFormElementParams($parameters);

        $name = $params["name"];

        if (isset($params["value"])) {
            $content = $params["value"];
            unset($params["value"]);
        } else {
            $content = self::getValue($name, $params);
        }

        unset($params[0]);
        $code = self::renderAttributes("<textarea", $params);
        $code .= ">" . $content . "</textarea>";

        return $code;
    }

    /**
     * Process the parameters for a form element
     *
     * @param mixed $parameters
     * @return array
     */
    protected static function processFormElementParams($parameters)
    {
        if (is_array($parameters)) {
            $params = $parameters;
        } else {
            $params = [$parameters];
        }

        if (!isset($params[0])) {
            if (isset($params["id"])) {
                $params[0] = $params["id"];
            }
        }

        $id = $params[0]; // throw exception if key not exist?

        /**
         * Automatically assign the id if the name is not an array
         * --> Does this make sense?
         */
        if (!isset($params["id"])) {
            if (false === strpos($id, "[")) {
                $params["id"] = $id;
            }
        }

        if (!isset($params["name"])) {
            $params["name"] = $id;
        }

        #$params["value"] = self::getValue($id, $params);

        return $params;
    }

    /**
     * Builds a HTML FORM tag
     *
     * <code>
     * echo HTML::form("posts/save");
     * echo HTML::form(array("posts/save", "method" => "post"));
     * </code>
     *
     * @param mixed $parameters
     * @return string
     */
    public static function form($parameters)
    {
        if (is_array($parameters)) {
            $params = $parameters;
        } else {
            $params = [$parameters];
        }

        /**
         * By default the method is POST
         */
        if (!isset($params["method"])) {
            $params["method"] = "post";
        }

        $action = "";
        if (isset($params[0])) {
            $action = $params[0];
            unset($params[0]);
        }
        if (isset($params["action"])) {
            $action = $params["action"];
        }

        /**
         * Check for extra parameters
         */
        if (isset($params["parameters"])) {
            $action .= "?" . $params["parameters"];
        }

        if (!empty($action)) {
            $params["action"] = $action;
        }

        unset($params[0]);
        $code = self::renderAttributes("<form", $params);
        $code .= ">";

        return $code;
    }

    /**
     * Builds a HTML close FORM tag
     *
     * @return string
     */
    public static function endForm()
    {
        return "</form>";
    }


    /**
     * Set the document type of content
     *
     * @param int $docType
     * @return void
     */
    public static function setDocType($docType)
    {
        if ($docType < self::HTML32 || $docType > self::XHTML5) {
            self::$documentType = self::HTML5;
        } else {
            self::$documentType = $docType;
        }
    }

    /**
     * Get the document type declaration of content
     *
     * @return string
     */
    public static function getDocType()
    {
        switch (self::$documentType) {
            case 1:
                return "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 3.2 Final//EN\">" . PHP_EOL;

            case 2:
                return "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01//EN\"" . PHP_EOL . "\t\"http://www.w3.org/TR/html4/strict.dtd\">" . PHP_EOL;

            case 3:
                return "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"" . PHP_EOL . "\t\"http://www.w3.org/TR/html4/loose.dtd\">" . PHP_EOL;

            case 4:
                return "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\"" . PHP_EOL . "\t\"http://www.w3.org/TR/html4/frameset.dtd\">" . PHP_EOL;

            case 6:
                return "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"" . PHP_EOL . "\t\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">" . PHP_EOL;

            case 7:
                return "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"" . PHP_EOL . "\t\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">" . PHP_EOL;

            case 8:
                return "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\"" . PHP_EOL . "\t\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">" . PHP_EOL;

            case 9:
                return "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"" . PHP_EOL . "\t\"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">" . PHP_EOL;

            case 10:
                return "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 2.0//EN\"" . PHP_EOL . "\t\"http://www.w3.org/MarkUp/DTD/xhtml2.dtd\">" . PHP_EOL;

            case 5:
            case 11:
                return "<!DOCTYPE html>" . PHP_EOL;
        }

        return "";
    }

    /**
     * Builds a HTML tag
     *
     *<code>
     * echo HTML::tag(name, parameters, selfClose, onlyStart, eol);
     *</code>
     *
     * @param string $tagName
     * @param mixed $parameters
     * @param bool $selfClose
     * @param bool $onlyStart
     * @param bool $useEol
     * @return string
     * @throws Exception
     */
    public static function tag($tagName, $parameters = null, $selfClose = false, $onlyStart = false, $useEol = false)
    {
        if (is_null($parameters)) {
            $params = [];
        } elseif (is_array($parameters)) {
            $params = $parameters;
        } else {
            $params = [$parameters];
        }

        $code = self::renderAttributes("<" . $tagName, $params);

        /**
         * Check if Doctype is XHTML
         */
        if (self::$documentType > self::HTML5) {
            if ($selfClose) {
                $code .= " />";
            } else {
                $code .= ">";
            }
        } else {
            if ($onlyStart) {
                $code .= ">";
            } else {
                $code .= "></" . $tagName . ">";
            }
        }

        if ($useEol) {
            $code .= PHP_EOL;
        }

        return $code;
    }

    /**
     * Builds a HTML closing tag
     *
     *<code>
     * echo HTML::endTag("script", true)
     *</code>
     *
     * @param string $tagName
     * @param bool $useEol
     * @return string
     */
    public static function endTag($tagName, $useEol = false)
    {
        if ($useEol) {
            return "</" . $tagName . ">" . PHP_EOL;
        }
        return "</" . $tagName . ">";
    }

}
