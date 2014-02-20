<?php
include_once(dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR."wp-load.php");

/*
Funzione che restituisce i dati di accesso all'account di Google Calendar presente nel db wordpress
*/

function Load_google_access_data()    {
    require_once('security.php');
    $dati = array();
    $dati[0] = get_option("CalendApp_GUser");
    $dati[1] = decrypt(get_option("CalendApp_GPass"));
    $dati[2] = get_option("CalendApp_GCal");
    return $dati; 
    
}

/*
Funzione che scrive i dati di accesso all'account di Google Calendar nel db wordpress
*/
function Write_google_access_data($username,$password, $calendar) {
    require_once('security.php');
    try{
        if (!add_option("CalendApp_GUser", $username, null, "no"))
               update_option("CalendApp_GUser", $username);
        if(!add_option("CalendApp_GPass", encrypt($password), null, "no"))
               update_option("CalendApp_GPass", encrypt($password));
        if(!add_option("CalendApp_GCal", $calendar, null, "no"))
               update_option("CalendApp_GCal", $calendar);
        print("<p class='Alert_green'>Dati memorizzati con successo</p>");
    
     } catch (Exception $exc){
        print("<p class='Alert_red'>Dati non memorizzati: ".$exc->getMessage()."</p>");    
     }
}

/*
Funzione che restituisce l'intervallo temporale di visualizzazione degli eventi presente nel db wordpress
*/
function Load_interval_date()   {
    $dati = array();
    $dati[0] = get_option("CalendApp_StartDate");
    $dati[1] = get_option("CalendApp_EndDate");
    return $dati; 
}

/*
Funzione che permette la scrittura dell'intervallo temporale definito dall'utente nel db wordpress
*/
function write_interval_date($start,$end)   {
    try{
        if (!add_option("CalendApp_StartDate", $start, null, "no"))
               update_option("CalendApp_StartDate", $start);
        if(!add_option("CalendApp_EndDate", $end, null, "no"))
               update_option("CalendApp_EndDate", $end);
        print("<p class='Alert_green'>Dati memorizzati con successo</p>");
     } catch (Exception $exc){
        print("<p class='Alert_red'>Dati non memorizzati: ".$exc->getMessage()."</p>");    
     }
}
?>
