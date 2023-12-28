<?php defined('_ENGINE') or die('Access Denied'); return array (
  'class' => 'Zend_Mail_Transport_Smtp',
  'args' => 
  array (
    0 => 'mail.consl2.com',
    1 => 
    array (
      'port' => 465,
      'ssl' => 'ssl',
      'auth' => 'login',
      'username' => 'noreply@consl2.com',
      'password' => 'Noreply@12345',
    ),
  ),
); ?>