<?php
/*

Drop Database If Exists sertificatetdd;
Create Database sertificatetdd CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' ;

CREATE USER 'sertificateuser'@'localhost' IDENTIFIED BY 'sertificatetddpass';
GRANT ALL PRIVILEGES ON sertificatetdd . * TO 'sertificateuser'@'localhost'  WITH GRANT OPTION;

CREATE USER 'sertificateuser'@'%' IDENTIFIED BY 'sertificatetddpass';
GRANT ALL PRIVILEGES ON sertificatetdd . * TO 'sertificateuser'@'%'  WITH GRANT OPTION;

FLUSH PRIVILEGES;

*/

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
];
