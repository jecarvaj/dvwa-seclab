<?php

define( 'DVWA_WEB_PAGE_TO_ROOT', '../../' );
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaPageStartup( array( 'authenticated', 'phpids' ) );

$page = dvwaPageNewGrab();
$page[ 'title' ]   = 'Vulnerability: SQL Injection' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'sqli';
$page[ 'help_button' ]   = 'sqli';
$page[ 'source_button' ] = 'sqli';

dvwaDatabaseConnect();

$method            = 'GET';
$vulnerabilityFile = '';
switch( $_COOKIE[ 'security' ] ) {

	case 'medium':
		$vulnerabilityFile = 'medium.php';
		$method = 'POST';
		break;
	case 'high':
		$vulnerabilityFile = 'high.php';
		break;
	case 'imposible':
		$vulnerabilityFile = 'imposible.php';
		break;
	default:
		$vulnerabilityFile = 'low.php';
		break;
}

require_once DVWA_WEB_PAGE_TO_ROOT . "vulnerabilities/sqli/source/{$vulnerabilityFile}";

// Is PHP function magic_quotee enabled?
$WarningHtml = '';
if( ini_get( 'magic_quotes_gpc' ) == true ) {
	$WarningHtml .= "<div class=\"warning\">The PHP function \"<em>Magic Quotes</em>\" is enabled.</div>";
}
// Is PHP function safe_mode enabled?
if( ini_get( 'safe_mode' ) == true ) {
	$WarningHtml .= "<div class=\"warning\">The PHP function \"<em>Safe mode</em>\" is enabled.</div>";
}

$page[ 'body' ] .= "
<div class=\"body_padded\">
	<h1>Inyección SQL a una aplicación web</h1>
<p> el campo id muestra por cada valor ingresado la fila dentro de la tabla a la que corresponde el dato consultado. El objetivo es utilizar este campo, que interactúa con la base de datos para obtener información de esta utilizando las vulnerabilidades indicadas </p>

	{$WarningHtml}

	<div class=\"vulnerable_code_area\">";
if( $vulnerabilityFile == 'high.php' ) {
	$page[ 'body' ] .= "Click <a href=\"#\" onClick=\"javascript:popUp('session-input.php');return false;\">here to change your ID</a>.";
}
else {
	$page[ 'body' ] .= "
		<form action=\"#\" method=\"{$method}\">
			<p>
				User ID:";
	if( $vulnerabilityFile == 'medium.php' ) {
		$page[ 'body' ] .= "\n				<select name=\"id\">";
		$query  = "SELECT COUNT(*) FROM users;";
		$result = mysql_query( $query ) or die( '<pre>' . mysql_error() . '</pre>' );
		$num    = mysql_result( $result, 0 );
		$i      = 0;
		while( $i < $num ) { $i++; $page[ 'body' ] .= "<option value=\"{$i}\">{$i}</option>"; }
		$page[ 'body' ] .= "</select>";
	}
	else
		$page[ 'body' ] .= "\n				<input type=\"text\" size=\"15\" name=\"id\">";

	$page[ 'body' ] .= "\n				<input type=\"submit\" name=\"Submit\" value=\"Aceptar\">
			</p>\n";

	if( $vulnerabilityFile == 'impossible.php' )
		$page[ 'body' ] .= "			" . tokenField();

	$page[ 'body' ] .= "
		</form>";
}
$page[ 'body' ] .= "
		{$html}
	</div>

	<h2>Información</h2>

<p> Para este ataque en necesario tener en consideración los siguientes factores
<li> 1. Utilizar caracteres de comentario para omitir el resto de la secuencias
dentro de la consulta.</li>
<li> 2.obtener el nombre de la  base de datos y su versión </li>
<li> 3. usar el comando UNION para combinar las tablas 
<li> 4. utilizar la base de datos predeterminada de mysql information_shema para obtener las tablas de la base de datos </li>
<li> 5. conociendo en nombre de la tablas, obtener el nombre de los usuarios y sus contraseñas </li>
<li> 6. Utilizar John the Ripper para descifrar las contraseñas </li> </p>
<!--	<h2>More Information</h2>
	<ul>
		<li>" . dvwaExternalLinkUrlGet( 'http://www.securiteam.com/securityreviews/5DP0N1P76E.html' ) . "</li>
		<li>" . dvwaExternalLinkUrlGet( 'https://en.wikipedia.org/wiki/SQL_injection' ) . "</li>
		<li>" . dvwaExternalLinkUrlGet( 'http://ferruh.mavituna.com/sql-injection-cheatsheet-oku/' ) . "</li>
		<li>" . dvwaExternalLinkUrlGet( 'http://pentestmonkey.net/cheat-sheet/sql-injection/mysql-sql-injection-cheat-sheet' ) . "</li>
		<li>" . dvwaExternalLinkUrlGet( 'https://www.owasp.org/index.php/SQL_Injection' ) . "</li>
		<li>" . dvwaExternalLinkUrlGet( 'http://bobby-tables.com/' ) . "</li>
	</ul>-->
</div>\n";

dvwaHtmlEcho( $page );

?>
