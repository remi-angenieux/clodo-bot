#!/usr/bin/php
<?php

$timeout = 10;

$alsa_username = 'user';
$alsa_password = 'password';

$cookies_file = __DIR__.'/cookies.txt';

/**************************************************
Première requête : Connexion
**************************************************/

$url = 'http://www.clodogame.fr/login/check/';

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

if (preg_match('`^https://`i', $url))
{
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
}

curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);

// Forcer cURL à utiliser un nouveau cookie de session
curl_setopt($ch, CURLOPT_COOKIESESSION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:6.0.2) Gecko/20100101 Firefox/6.0.2');

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, array(
  'username' => $alsa_username,
  'password' => $alsa_password,
  'submitForm' => 'Connexion'
));

// Fichier dans lequel cURL va écrire les cookies
// (pour y stocker les cookies de session)
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies_file);

curl_exec($ch);

curl_close($ch);

/**************************************************
Seconde requête : Récupération les tickets
**************************************************/

$url = 'http://www.clodogame.fr/activities/bottle/';

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

if (preg_match('`^https://`i', $url))
{
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
}

curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIESESSION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:6.0.2) Gecko/20100101 Firefox/6.0.2');
curl_setopt($ch, CURLOPT_NOBODY, true);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, array(
  'type' => 1,
  'time' => 10,
  'bottlecollect_pending' => 'True',
  'Submit2' => 'Vider ton caddie.'
));

// Fichier dans lequel cURL va lire les cookies
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies_file);

$page_content = curl_exec($ch);

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

/**************************************************
Trsoisième requête : Aller chercher des tickets
**************************************************/

$url = 'http://www.clodogame.fr/activities/bottle/';

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

if (preg_match('`^https://`i', $url))
{
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
}

curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIESESSION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:6.0.2) Gecko/20100101 Firefox/6.0.2');
curl_setopt($ch, CURLOPT_NOBODY, true);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, array(
  'type' => 1,
  'time' => 10,
  'Submit2' => 'Partir en récolte'
));

// Fichier dans lequel cURL va lire les cookies
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies_file);

$page_content = curl_exec($ch);

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

/**************************************************
Troisème requête : Déconnexion
**************************************************/

$url = 'http://www.clodogame.fr/logout/';

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

if (preg_match('`^https://`i', $url))
{
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
}

curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_COOKIESESSION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:6.0.2) Gecko/20100101 Firefox/6.0.2');

curl_exec($ch);

curl_close($ch);

// Effacement du fichier de stockage des cookies

if (file_exists($cookies_file))
  unlink($cookies_file);

/****************************************
Affichage
****************************************/

if ($http_code == 200)
{
 echo "OK";
}
else
{
 echo 'Une erreur est survenue : '. $http_code;

}
?>