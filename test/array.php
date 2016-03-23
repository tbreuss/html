<?php

ini_set('display_errors', 1);

require (__DIR__ . '/../src/HTML.php');

echo HTML::getDocType();
echo HTML::tag("html");
echo HTML::tag("body");


$defaults = [
    "text" => [
        "test" => [
            "a" => "Test A",
            "b" => "Test B"
        ]
    ],
    "select" => [2, 4]
];
HTML::setDefaults($defaults);

echo HTML::tag("pre");
print_r($defaults);
echo HTML::endTag("pre");

if (!empty($_POST)) {
    echo HTML::tag("pre");
    print_r($_POST);
    echo HTML::endTag("pre");
}

echo HTML::form([$_SERVER["PHP_SELF"], "method" => "post"]);

echo HTML::textField(["text[test][a]"]) . HTML::tag("br");
echo HTML::textField(["text[test][b]"]) . HTML::tag("br");
echo HTML::select(["select[]", "multiple" => "1", "useEmpty" => true, "emptyValue" => -1, "emptyText" => "Bitte wählen"], ["a", "b", "c", "d", "e", "f", "g"]) . HTML::tag("br");

echo HTML::submitButton(["submit"]) . HTML::tag("br");
echo HTML::endForm();

echo HTML::endTag('body');
echo HTML::endTag('html');
