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
echo HTML::dateField(["date", "name" => "date"]) . HTML::tag("br");
echo HTML::dateTimeField(['dateTime', "name" => "dateTime"]) . HTML::tag("br");
echo HTML::colorField(['color', "name" => "color"]) . HTML::tag("br");
echo HTML::dateTimeLocalField(['dateTimeLocalField', "name" => "dateTimeLocalField"]) . HTML::tag("br");
echo HTML::checkField(["check", "name" => "check"]) . HTML::tag("br");
echo HTML::emailField(["email", "name" => "email"]) . HTML::tag("br");
echo HTML::fileField(["file", "name" => "file"]) . HTML::tag("br");
echo HTML::hiddenField(["hidden", "name" => "hidden"]) . HTML::tag("br");
echo HTML::imageInput(["image", "name" => "image"]) . HTML::tag("br");
echo HTML::monthField(["month", "name" => "month"]) . HTML::tag("br");
echo HTML::passwordField(["password", "name" => "password"]) . HTML::tag("br");
echo HTML::numericField(["numeric", "name" => "numeric"]) . HTML::tag("br");
echo HTML::rangeField(["range", "name" => "range"]) . HTML::tag("br");
echo HTML::weekField(["week", "name" => "week"]) . HTML::tag("br");
echo HTML::urlField(["url", "name" => "url"]) . HTML::tag("br");
echo HTML::timeField(["time", "name" => "time"]) . HTML::tag("br");
echo HTML::textField(["text", "name" => "text"]) . HTML::tag("br");
echo HTML::textArea(["textArea", "name" => "textArea"]) . HTML::tag("br");
echo HTML::searchField(["search", "name" => "search"]) . HTML::tag("br");
echo HTML::submitButton(["submit"]) . HTML::tag("br");
echo HTML::endForm();

echo HTML::tagClose('body');
echo HTML::tagClose('html');
