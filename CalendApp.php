<?php
/**
 * @package CalendApp
 * @version 1.2
 */
/*
Plugin Name: CalendApp
Plugin URI: http://wordpress.org/extend/plugins/calendapp/
Description: Questo modulo permette all'amministratore del sito di inserire eventi relativi a prove educative, consentendo a tutti gli utenti del sistema, autenticati e non, di prendere visione di tali eventi. Ogni evento inserito verrà postato sul calendario di google relativo all'account dell'amministratore.
Author: Francesco Altamura, Giovanni Marzulli, Maria Antonietta Fanelli, Floriano Fauzzi
Version: 1.2
Author URI: http://margiov.altervista.org
*/

add_action('admin_menu', 'Setting_calendapp');
add_action('admin_head', 'Setting_calendapp_css' );
add_action('admin_head', 'Setting_js_Action' );
add_action( 'widgets_init', 'load_widgets' );
add_action('wp_head', 'Setting_calendapp_css' );
add_action('wp_head', 'Setting_js_Action' );
add_action('wp_enqueue_scripts', 'include_jquery');
add_filter('the_content', 'publish_calend');

/*Funzione che determina il menu per la scelta delle azioni disponibili all'amministratore.*/
function Setting_calendapp()    {
    add_menu_page('CalendApp', 'CalendApp', 'administrator', 'CalendApp',null);
    add_submenu_page('CalendApp', 'Impostazioni', 'Impostazioni', 'administrator', 'CalendApp', 'show_settings_form');
    add_submenu_page('CalendApp', 'Inserisci appello', 'Inserisci appello', 'administrator', 'Enter_event', 'Insert_event');
    add_submenu_page('CalendApp','Calendario appelli','Calendario appelli','administrator','Vis_event','Vis_event');
    add_submenu_page('CalendApp','Imposta intervallo','Imposta intervallo','administrator','Set_Range_Event','Set_interval');
    add_submenu_page('CalendApp','Help','Help','administrator','Help','Help');
}
/*
 * Inclusione di JQuery
 */
function include_jquery(){
    wp_enqueue_script("jquery"); 
}


/*
 * Caricamento del widget
 */
function Load_widgets() {
    require_once('My_Widget.php');
    register_widget('My_Widget');
}

/*
Funzione che determina il form di inserimento e modifica degli eventi presente nel file Insert_event.php
*/
function Insert_event() {
    require_once('Insert_event.php');	
    Insert_event_form();
}

/*
Funzione che determina il form di impostazione dell'intervallo presente nel file Set_interval.php
*/
function Set_interval() {
    require_once('Set_interval.php');
    Set_interval_form();	
}
/*
Funzione che determina visualizza la tabella di visualizzazione degli eventi
*/
function Vis_event() {
    require_once('Vis_event.php');
    Vis_event_form();	
}

/*
Funzione che permette l'aggiunta del file Action.js nella head section delle pagine web che determinano le azioni del plugin
*/
function Setting_js_Action()    {
    echo "<script type='text/javascript' src=". plugins_url('Action.js', __FILE__) ."></script>";
}


/*
Funzione che permette l'aggiunta del file style_setting.css nella head section delle pagine web che determinano lo stile delle pagine web del plugin
*/
function setting_calendapp_css()    {
    echo " <link rel='stylesheet' type='text/css' href='" .  plugins_url('style_setting.css', __FILE__) ."'>";	
}

/*
Funzione che determina il form di inserimento delle credenziali necessarie per l'accesso all'account di Google.
*/
function show_settings_form()   {
    require_once('Gcal_login.php');
    Display_settings_form();
}

/*
Funzione che determina il form di inserimento delle credenziali necessarie per l'accesso all'account di Google.
*/
function Help()   {
    require_once('Help.php');
}

/*
 * Funzione che permette la pubblicazione della tabella degli appelli nei post e pagine del front-end inserendo una parola chiave
 */
function publish_calend($content)    {
    require_once('Publish_cal.php');
    return pub_cal($content);
}

?>