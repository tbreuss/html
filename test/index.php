<?php

ini_set('display_errors', 1);

require (__DIR__ . '/../src/HTML.php');

echo HTML::getDocType();
echo HTML::tag("html");
echo HTML::tag("body");

if (!empty($_POST)) {
    echo HTML::tag("pre");
    print_r($_POST);
    echo HTML::endTag("pre");
}

HTML::setDefaults([
    "date" => "2016-01-01",
    "dateTime" => "2018-02-01 03:04",
    "dateTimeLocalField" => "2018-02-01T03:04",
    "color" => "#ff0000",
    "text" => "Text",
    "month" => "2016-03",
    "select" => [2, 4],
    "search" => "Das ist eine Suche"
]);

echo HTML::form(["index.php", "method" => "post"]);

echo HTML::dateField(["date"]) . HTML::tag("br");
echo HTML::dateTimeField(['dateTime']) . HTML::tag("br");
echo HTML::colorField(['color']) . HTML::tag("br");
echo HTML::dateTimeLocalField(['dateTimeLocal']) . HTML::tag("br");
echo HTML::checkField(["check", "value" => "yes"]) . HTML::tag("br");
echo HTML::radioField(["radio", "value" => "yes"]) . HTML::radioField(["radio", "value" => "no"]) . HTML::tag("br");
echo HTML::emailField(["email"]) . HTML::tag("br");
echo HTML::fileField(["file"]) . HTML::tag("br");
echo HTML::hiddenField(["hidden"]) . HTML::tag("br");
echo HTML::monthField(["month"]) . HTML::tag("br");
echo HTML::passwordField(["password"]) . HTML::tag("br");
echo HTML::numberField(["number"]) . HTML::tag("br");
echo HTML::rangeField(["range"]) . HTML::tag("br");
echo HTML::weekField(["week"]) . HTML::tag("br");
echo HTML::urlField(["url"]) . HTML::tag("br");
echo HTML::timeField(["time"]) . HTML::tag("br");
echo HTML::textField(["text", "autofocus"]) . HTML::tag("br");
echo HTML::textArea(["textArea", "disabled"]) . HTML::tag("br");
echo HTML::searchField(["search", "readonly"]) . HTML::tag("br");
echo HTML::select(["select[]", "multiple" => "1", "useEmpty" => true, "emptyValue" => -1, "emptyText" => "Bitte wählen"], ["a", "b", "c", "d", "e", "f", "g"]) . HTML::tag("br");

echo HTML::pushButton(["button"]) . HTML::tag("br");
echo HTML::imageButton(["image"]) . HTML::tag("br");
echo HTML::submitButton(["submit"]) . HTML::tag("br");
echo HTML::resetButton(["reset"]) . HTML::tag("br");

echo HTML::endForm();

echo HTML::endTag('body');
echo HTML::endTag('html');
