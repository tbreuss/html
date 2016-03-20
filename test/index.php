<?php

ini_set('display_errors', 1);

require (__DIR__ . '/../src/HTML.php');

echo HTML::getDocType();
echo HTML::tag("html");
echo HTML::tag("body");

if (!empty($_POST)) {
    echo HTML::tag("pre");
    print_r($_POST);
    echo HTML::tagClose("pre");
}

echo HTML::form(["index.php", "method" => "post"]);
echo HTML::dateField(["date"]) . HTML::tag("br");
echo HTML::dateTimeField(['dateTime']) . HTML::tag("br");
echo HTML::colorField(['color']) . HTML::tag("br");
echo HTML::dateTimeLocalField(['dateTimeLocalField']) . HTML::tag("br");
echo HTML::checkField(["check", "value" => "yes"]) . HTML::tag("br");
echo HTML::radioField(["radio", "value" => "yes"]) . HTML::radioField(["radio", "value" => "no"]) . HTML::tag("br");
echo HTML::emailField(["email"]) . HTML::tag("br");
echo HTML::fileField(["file"]) . HTML::tag("br");
echo HTML::hiddenField(["hidden"]) . HTML::tag("br");
echo HTML::imageInput(["image"]) . HTML::tag("br");
echo HTML::monthField(["month"]) . HTML::tag("br");
echo HTML::passwordField(["password"]) . HTML::tag("br");
echo HTML::numericField(["numeric"]) . HTML::tag("br");
echo HTML::rangeField(["range"]) . HTML::tag("br");
echo HTML::weekField(["week"]) . HTML::tag("br");
echo HTML::urlField(["url"]) . HTML::tag("br");
echo HTML::timeField(["time"]) . HTML::tag("br");
echo HTML::textField(["text"]) . HTML::tag("br");
echo HTML::textArea(["textArea"]) . HTML::tag("br");
echo HTML::searchField(["search"]) . HTML::tag("br");
echo HTML::submitButton(["submit"]) . HTML::tag("br");
echo HTML::endForm();

echo HTML::tagClose('body');
echo HTML::tagClose('html');
