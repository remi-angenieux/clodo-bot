<?php
/*
 * Constantes
 */
define('USERNAME', 'user');
define('PASSWORD', 'password');
define('TIMEOUT', 100);
define('NB_MAX_PAGE', 10);
define('COOKIE_DIR', __DIR__.'/cookies_battle.txt');
$CIBLES= array('http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr8596.jpg', //Cafard
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr8930.jpg', //Poisson rouge
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr8795.jpg', //Souris
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr5423.jpg', //Hamster
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr4591.jpg', //Perruche
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr1451.jpg', //Pigeon
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr3684.jpg', //Rat
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr7730.jpg', //Lapin
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr1903.jpg', //Furet
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr3735.jpg', //Chat
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr9386.jpg', //Faucon
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr1482.jpg', //Serpent
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr2474.jpg', //Chèvre
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr2758.jpg', //Caniche
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/73933.jpg', //Souris dressée
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/s1.jpg', //Moineau
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr4263.jpg', //Aigle
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr9051.jpg', //Berger allemand
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr5240.jpg', //Pitbull
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr1456.jpg', //Cocker
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr7563.jpg', //Chihuahua
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr0385.jpg', //Cheval
'http://static.pennergame.de/img/pv4/shop/fr_FR/tiere/fr2536.jpg' //Singe
);

/*
 * Gestion des pages internet via cURL
 */

class curl
{
	private $ch; // variable qui contient les ressources curl
	
	/**
	 * 
	 * Execute une requête cURL
	 * @param $url
	 * @param $post
	 * @param $no_body
	 * @param $keep_ch
	 */
	
	public function exec_curl($url, $post=array(), $no_body=TRUE, $keep_ch=FALSE)
	{
		global $ch;
		
		$this->ch = curl_init($url);
		
		curl_setopt($this->ch, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($this->ch, CURLOPT_TIMEOUT, TIMEOUT);
		curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, TIMEOUT);
		
		if (preg_match('`^https://`i', $url))
		{
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
		}
	
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_NOBODY, $no_body);
		
		// Forcer cURL � utiliser un nouveau cookie de session
		curl_setopt($this->ch, CURLOPT_COOKIESESSION, true);
		
		if (count($post) > 0)
		{
			curl_setopt($this->ch, CURLOPT_POST, true);
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post);
		}
		else
		{
			curl_setopt($this->ch, CURLOPT_POST, false);
		}
	
		// Fichier dans lequel cURL va �crire les cookies
		// (pour y stocker les cookies de session)
		curl_setopt($this->ch, CURLOPT_COOKIEJAR, COOKIE_DIR);
		curl_setopt($this->ch, CURLOPT_COOKIEFILE, COOKIE_DIR);
		curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:6.0.2) Gecko/20100101 Firefox/6.0.2');
	
		$return = curl_exec($this->ch);
		if ($return === FALSE)
		{
			die(curl_error($this->ch));
		}
	
		if ($keep_ch==FALSE)
			curl_close($this->ch);
		
		return $return;
	}
	
	public function get_real_url()
	{
		$return = curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL);
		curl_close($this->ch);
		
		return $return;
	}
}

$curl = new curl();

/*
 * Connexion
 */
$array = array(
  'username' => USERNAME,
  'password' => PASSWORD,
  'submitForm' => 'Connexion'
);
$connexion = $curl->exec_curl('http://www.clodogame.fr/login/check/', $array, FALSE);

/*
 * Vérification qu'une attaque n'est pas déjà en cours
 */
$connexion = str_replace(array("\n", "\r"), array('',''), $connexion);
// Si une attaque est déjà en cours
/*if (preg_match ('#^.*<li class="icon fight">[[:space:]]*<a href="/fight/" class="ttip" title="Bastons" rel="Un gladiateur se décide dans l\'arène\.">[[:space:]]*<script type="text/javascript">counter\(0\)</script>.*$#i', $connexion) == FALSE)
{
	/*
 	* Deconnexion
	*/
	/*$curl->exec_curl('http://www.clodogame.fr/logout/', NULL);
	if (file_exists(COOKIE_DIR))
  	unlink(COOKIE_DIR);
  	die; // Pour stopper le script
}*/


/*
 * Recherche de la meilleure cible et l'attaque
 */

// Cherche le lien du highscore
$page_attaque = $curl->exec_curl('http://www.clodogame.fr/fight/', NULL, FALSE);
preg_match('#^.*<a href="http://www\.clodogame\.fr/highscore/user/\?min=([0-9]+)&max=([0-9]+).*$#im', $page_attaque, $lien);

// Boucle des pages
$cible_trouvee = FALSE;
$i = 1;

while (!$cible_trouvee AND $i<NB_MAX_PAGE)
{
	// Recupère les victimes potentiel (dans le 19iem et sans bande)
	$highscore = $curl->exec_curl('http://www.clodogame.fr/highscore/user/'.$i.'/?min='.$lien[1].'&max='.$lien[2], NULL, FALSE);
	$highscore = str_replace(array("\n", "\r"), array('',''), $highscore);
	$highscore = str_replace(array('<tr class="odd">', '<tr class="even">'), array("\n".'<tr class="odd">', "\n".'<tr class="even">'), $highscore);
	preg_match_all('#^.*<tr class="(odd|even)">[[:space:]]*<td class="col1 (up|neutral|down)">[0-9]+\.</td>[[:space:]]*<td class="col2"><a href="/profil/id:([0-9]+)/" class="username ">([a-z0-9_]+)</a></td>[[:space:]]*<td class="col3"><a href="/profil/bande:None/"></a></td>[[:space:]]*<td class="col4">19<sup>e</sup></td>.*$#im', $highscore, $cibles);
	// Pour ne pas attaquer les personnes qui UP enlever down de col1(up|down|neutral)
	
	$j = 0;
	
	// Boucle pour les joueurs potentiels
	while (!$cible_trouvee AND ($j + 1) <= count($cibles[1]))
	{
		$profil_ennemi = $curl->exec_curl('http://www.clodogame.fr/profil/id:'.$cibles[3][$j].'/', NULL, FALSE);
		preg_match('#^.*<img src="(http://static\.pennergame\.de/img/pv4/shop/fr_FR/tiere/fr[0-9]+\.jpg)">.*$#im', $profil_ennemi, $profil);
		if (!empty($profil[1])) // Si l'animal de compagnie est affiché
		{
			if (in_array($profil[1], $CIBLES)) // Si l'animal est plus faible que le mien
			{
				//echo "Cible trouvée";
				$attaque = $curl->exec_curl('http://www.clodogame.fr/fight/attack/', array('f_toid' => $cibles[4][$j],
				'Submit2' => '  Attaque  '
				), FALSE, TRUE);
				if ($curl->get_real_url() == 'http://www.clodogame.fr/fight/?status=success')
					$cible_trouvee = TRUE;
			}
			//echo '<br />';
			//echo (!empty($profil[1])) ? '<img src="'.$profil[1].'">' : 'Pas d\'animal affiché';
			//echo $cibles[4][$j];
		}
		
		
		$j++;
	}
	
	$i++;
}

/*
 * Deconnexion
 */
$curl->exec_curl('http://www.clodogame.fr/logout/', NULL);
if (file_exists(COOKIE_DIR))
  unlink(COOKIE_DIR);
?>