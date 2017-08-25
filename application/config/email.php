<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

$config['mailtype'] = 'html'; // default text
$config['charset'] = 'utf-8';
$config['protocol'] = 'smtp';
$config['smtp_port'] = 465; // default is 25 (no ssl)
$config['newline'] = "\r\n"; // essential to use double quotes here!!!
/*
// MailShot serttings
// Warning! MailShot can be quite slow at sending email to the recepient (like 4 minutes).
$config['smtp_host'] = 'ssl://smtp.mailgun.org';
$config['smtp_user'] = 'postmaster@sandbox*********.mailgun.org';
$config['smtp_pass'] = '*************';
*/
// Gmail (remember to use gmail's unsafe mode for using unverified applications)
$config['smtp_host'] = 'ssl://smtp.gmail.com';
$config['smtp_user'] = 'your.email@gmail.com';
$config['smtp_pass'] = '*************';
