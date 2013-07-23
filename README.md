CakeEmailMini
=============

CakePHP(2.3.8)からCakeEmailをむりやりぬきだしたものです

# 使用例

<?php
require_once('repos/lib/CakeEmailMini.php');

Configure::write("App.encoding", "UTF-8");
Configure::write('Security.salt', 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi');

$email = new CakeEmailMini();
$email->transport('Mail');
$email->from(array("foo@example.com" => "Full Name Comment"));
$email->to("bar@example.com");
$email->subject("Test! Test! Test!");
$email->charset("ISO-2022-JP");
$email->headerCharset("ISO-2022-JP");
$email->send("Test");


