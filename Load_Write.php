<?php
include_once(dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR."wp-load.php");
    /*Si attiva quando viene richiesta la scrittura dell' accesstoken all'account di Google.*/


/*
Funzione che restituisce l' id dell' amministratore; questa funzione è usata quando occorre salvare le impostazioni del plugin associandole ad un amministratore
*/

function get_id_user() {
    return get_current_user_id();
}

/*
Funzione che restituisce l' id dell' amministratore a partire dall' indirizzo del blog
*/

function get_id_admin_from_blog($valore_blog) {
switch_to_blog($valore_blog);
    $users_query = new WP_User_Query( array( 
                'role' => 'administrator', 
                'orderby' => 'display_name'
                ) );
    $results = $users_query->get_results();
    foreach($results as $user)
    {
        $site_admin=$user->ID;
    }

    restore_current_blog();
	return $site_admin;
}

/*
Funzione che legge le credenziali dal db wordpress per effettuare il refreshtoken 
*/

function Load_token($valore_blog)    {
    require_once('security.php');
	$id_admin = get_id_admin_from_blog($valore_blog);
	$token = decrypt(get_user_meta($id_admin , "CalendApp_GToken", "true"));	            	
	return $token;
}

/*
Funzione che legge l' oggetto client dal  db wordpress
*/

function Load_client($valore_blog)    {
	$id_admin = get_id_admin_from_blog($valore_blog);
	$client = get_user_meta($id_admin, "CalendApp_GClient", "true");
	return $client ;
}

/*
Funzione che scrive le credenziali nel db wordpress per effettuare il refreshtoken in seguito
*/

function Write_token($token)    {
    require_once('security.php');
    try{        
        if(!add_user_meta(get_id_user(), "CalendApp_GToken", encrypt($token), "true"))
               update_user_meta(get_id_user(), "CalendApp_GToken", encrypt($token));
	} catch (Exception $exc){
        print("<p class='Alert_red'>Dati non memorizzati: ".$exc->getMessage()."</p>");    
     }

}

/*
Funzione che scrive l' oggetto client nel db wordpress
*/

function Write_client($client)    {
    try{
        if(!add_user_meta(get_id_user(), "CalendApp_GClient", $client, "true"))
               update_user_meta(get_id_user(), "CalendApp_GClient", $client);
	} catch (Exception $exc){
        print("<p class='Alert_red'>Dati non memorizzati: ".$exc->getMessage()."</p>");    
     }
}

/*
Funzione che restituisce i dati di accesso all'account di Google Calendar presente nel db wordpress
*/

function Load_google_access_data($valore_blog)    {
    require_once('security.php');
        $dati = array();
    	if ($valore_blog==0) {
    		$dati[0] = get_user_meta(get_id_user(), "CalendApp_GClient_ID", "true");
    		$dati[1] = decrypt(get_user_meta(get_id_user(), "CalendApp_GClient_secret", "true"));
    		$dati[2] = get_user_meta(get_id_user(), "CalendApp_GCal", "true");}
    	else {
			$id_admin = get_id_admin_from_blog($valore_blog);
    		$dati[0] = get_user_meta($id_admin, "CalendApp_GClient_ID", "true");
    		$dati[1] = decrypt(get_user_meta($id_admin, "CalendApp_GClient_secret", "true"));
    		$dati[2] = get_user_meta($id_admin, "CalendApp_GCal", "true");
    	}
    return $dati; 
}

/*
Funzione che scrive i dati di accesso per l' autenticazione nel db wordpress
*/

function Write_google_access_data($client_id,$client_secret) {
    require_once('security.php');
    try{
        if(!add_user_meta(get_id_user(), "CalendApp_GClient_ID", $client_id,"true"))
               update_user_meta(get_id_user(), "CalendApp_GClient_ID", $client_id);
        if(!add_user_meta(get_id_user(), "CalendApp_GClient_secret", encrypt($client_secret),"true"))
               update_user_meta(get_id_user(), "CalendApp_GClient_secret", encrypt($client_secret));
        print("<p class='Alert_green'>Dati memorizzati con successo</p>");
     } catch (Exception $exc){
        print("<p class='Alert_red'>Dati non memorizzati: ".$exc->getMessage()."</p>");    
     }
}

/*
Funzione che permette la scrittura del calendario selezionato dopo l' autenticazione nel db wordpress
*/

function Write_google_calendar_name($calendar) {
    try{
        if(!add_user_meta(get_id_user(), "CalendApp_GCal", $calendar,"true"))
               update_user_meta(get_id_user(), "CalendApp_GCal", $calendar);
        //print("<p class='Alert_green'>Calendario impostato con successo</p>");
     } catch (Exception $exc){
        print("<p class='Alert_red'>Dati non memorizzati: ".$exc->getMessage()."</p>");    
     }
}

/*
Funzione che permette la scrittura del percorso di reindirizzamento all' uri locale
*/

function Write_path($path) {
    try{
        if(!add_site_option("CalendApp_GAuth_path", $path))
               update_site_option("CalendApp_GAuth_path", $path);
     } catch (Exception $exc){
        print("<p class='Alert_red'>Dati non memorizzati: ".$exc->getMessage()."</p>");    
     }
}

/*
Funzione che restituisce il percorso di reindirizzamento all' uri locale
*/

function Load_path()    {
    $path = get_site_option("CalendApp_GAuth_path");
	return $path;
}

/*
Funzione che restituisce l'intervallo temporale di visualizzazione degli eventi presente nel db wordpress
*/

function Load_interval_date($valore_blog)   {
	$dati = array();
	$id_admin = get_id_admin_from_blog($valore_blog);
	$dati[0] = get_user_meta($id_admin, "CalendApp_StartDate", "true");
    $dati[1] = get_user_meta($id_admin, "CalendApp_EndDate", "true");
    //}
    return $dati; 
}

/*
Funzione che permette la scrittura dell'intervallo temporale definito dall'utente nel db wordpress
*/

function write_interval_date($start,$end)   {
    try{
        if(!add_user_meta(get_id_user(), "CalendApp_StartDate", $start,"true"))
               update_user_meta(get_id_user(), "CalendApp_StartDate", $start);
        if(!add_user_meta(get_id_user(), "CalendApp_EndDate", $end,"true"))
               update_user_meta(get_id_user(), "CalendApp_EndDate", $end);
        print("<p class='Alert_green'>Dati memorizzati con successo</p>");
     } catch (Exception $exc){
        print("<p class='Alert_red'>Dati non memorizzati: ".$exc->getMessage()."</p>");    
     }
}
?>
