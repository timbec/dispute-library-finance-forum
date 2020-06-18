<?php
echo "<h2>Testing Postfix...</h2>";
$to = 'timothybenjaminbeckett@gmail.com';
$subject = 'This is the subject!';
$body = 'This is the email body.';
$from = 'From: From Address <from.timbeckett2323@gmail.com>' . "\r\n";
$option = "-ffrom.address@gmail.com";
mail($to, $subject, $body, $from, $option);