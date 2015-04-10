<?php

require_once 'google-api-php-client/src/Google/Client.php';
require_once 'google-api-php-client/src/Google/Service/Calendar.php';
require_once 'google-api-php-client/autoload.php';
require_once 'Load_Write.php';
require_once 'Authentication.php';

$richiesta = $_POST['Richiesta'];

switch($richiesta)  {
	
    /*Si attiva quando viene richiesta la scrittura delle credenziali di accesso all'account di Google.*/
case 0:	
	$client_id = $_POST['Client_id'];
	$client_secret = $_POST['Client_secret'];
	try {
            Write_google_access_data($client_id,$client_secret);
            
            //creo l' uri togliendo il sito e lasciando il percorso standard del file raggiungibile tramite url
			$uri_full=plugins_url(null, __FILE__);
			$uri_full=explode("/wp-content/",$uri_full);
			$pre_uri=explode("/",$uri_full[0]);
			for ($i=0;$i< count($pre_uri)-1;$i++) {
				$uri=$uri.$pre_uri[$i]."/";
			}
			$uri=$uri."wp-content/".$uri_full[1]."/Authentication.php";
		
            Write_path($uri);
            Gconnect();
            
        } catch (Exception $e) {
            print("<p class='Alert_red'>Errore: ".$e->getMessage()."</p>");
        }       
    break;

		
    /*
    Si attiva quando viene richiesto l'update di un evento creato in precedenza.
    */
    case 1:	
	$tipo = $_POST['tipologia'];
	$nome = $_POST['Nome'];
	$note = $_POST['Note'];
	$luogo = $_POST['Luogo'];
	$durata = $_POST['durata'];
		
	$ora_ora_inzio = $_POST['ora_ora_inzio'];
	$minuti_ora_inizio = $_POST['minuti_ora_inizio'];
		
	//NB: Mese ed anno < 10 hanno una sola cifra
	//sistemo.	 
	$giorno_data_inizio = $_POST['giorno_data_inizio'];
        $mese_data_inizio = $_POST['mese_data_inizio'];
        $anno_data_inizio = $_POST['anno_data_inizio'];      
		
	$startTime =  $ora_ora_inzio . ':' . $minuti_ora_inizio ;
	$startDate =  $anno_data_inizio ."-" . $mese_data_inizio . "-" . $giorno_data_inizio;
	$endDate =  $startDate;	
                
        $timezone = new DateTimeZone('Europe/Rome');
        $offset = $timezone->getOffset(new DateTime($startDate));
	$tzOffset = "+0".$offset/3600;
		
        $d_start = new DateTime($startDate . ' ' . $startTime . ':00');     
	$d_end = new DateTime($startDate . ' ' . $startTime . ':00');

	if($durata == 1)    {
            $d_end->modify('+1 hour');
        }   else    { 
            $d_end->modify('+' . $durata . ' hours');
        }
        $endTime = substr($d_end->format('H:i:s'),0,5);
        
        try{
    		$gdata = calendar_connect();
            $service = $gdata['service'];
            $id = $gdata['id'];						
            // Create a new entry
            $event = new Google_Service_Calendar_Event();
            
            // Populate the event with the desired information
            // Note that each attribute is crated as an instance of a matching class
            
            $event->setSummary($tipo . ' - ' . $nome);
            $event->setDescription($note);
			$event->setLocation($luogo);
			
            $start = new Google_Service_Calendar_EventDateTime();
    		$start->setDateTime("{$startDate}T{$startTime}:00.000{$tzOffset}:00");
    		$event->setStart($start);

            $end = new Google_Service_Calendar_EventDateTime();
    		$end->setDateTime("{$endDate}T{$endTime}:00.000{$tzOffset}:00");
    		$event->setEnd($end);
    			 
            // Upload the event to the calendar server
            // A copy of the event as it is recorded on the server is returned
            $createdEvent = $service->events->insert($id, $event);
            print("<p class='Alert_green'>Evento inserito con successo</p>");
        }   catch (Exception $e) {
            print("<p class='Alert_red'>Errore: ".$e->getMessage()."</p>");
        }
    break;	
	
    /*Si attiva quando viene richiesta la scrittura dell'intervallo di visualizzazione degli eventi*/
    case 2:
        $start = $_POST['data_inizio'];
		$end = $_POST['data_fine'];	
	write_interval_date($start,$end);
    break; 	
    
    /*
     Si attiva quando viene richiesta l'eliminazione di un evento creato in precedenza.
    */
    case 3:        
        $idevent = $_POST['href'];    
        try {
            $gdata = calendar_connect();
            $service = $gdata['service'];
            $id = $gdata['id'];     
            $event=$service->events->delete($id, $idevent);
        }  catch (Exception $e) {
            print("<p class='Alert_red'>Errore: ".$e->getMessage()."</p>");
        }       
    break;
        
    /*
    Si attiva quando viene richiesto il recupero degli eventi dall'account di Google dell'amministratore.
    */
    case 4:            
        $htmltag = ''; 	
        $examTitle = $_POST['examTitle'];
        $backend = $_POST['backend'];
	$today = new DateTime("now");
	$events=array();
	$event_list=array();
	//Recupero delle date impostate dall'amministratore..
        $interval_date = Load_interval_date(get_current_blog_id());				
	$d_start = new DateTime($interval_date[0]);
	$d_end = new DateTime($interval_date[1]);	
	try {	
            $gdata = calendar_connect();
            $service = $gdata['service'];
            $id = $gdata['id'];						
            $eventsParam = array('singleEvents'=>True , 'maxResults'=>500 , 'orderBy'=>'startTime' , 'timeMin'=>$interval_date[0]."T00:00:00.000-00:00" , 'timeMax'=>$interval_date[1]."T00:00:00.000-00:00");
			
// loop through all events read from calendar


            // Retrieve the event list from the calendar server
            $event_list = $service->events->listEvents($id,$eventsParam);
			$events = $event_list->getItems();
            //Se recupero degli eventi inizio a definire la tabella..
            //Altrimenti mostro un messaggio per avvertire l'utente..
            if(count($event_list) > 0)   {
		$i = 0 ; 
		$eventi = count($event_list);	
		$materie_nome = array();
		$materie = array();
		$materie[0] = 'empty'; 
		$materie_nome[0] = 'empty'; 		
							
		/*
		Per ogni evento recuperato 
		Se presente comincio a dividere gli eventi in base alle materie di riferimento
		l'array materie_nome conterrÃƒÂ  i nomi delle materie che il sistema ha rilevato.
		l'array materie conterrÃƒÂ  tutti gli eventi riferiti alla materia il cui titolo ÃƒÂ¨ memorizzato 
		nella stessa posizione dell'array 	materie.
		*/	
		foreach ($events as $event) {
					
                	$titolo = explode(" - ",$event->getSummary());
			if (strcasecmp($examTitle,$titolo[1]) == 0 || strcasecmp($examTitle ,"ALL") ==0 ) {	
                            $flag = FALSE; 
                            $indice = 0; 
                            for($j=0;$j<count($materie_nome);$j++)  {
                                if(strcasecmp($materie_nome[$j],$titolo[1])==0) {
                                    $indice = $j; 
                                    $flag = TRUE; 
                                    break;
				}
                            }						
                            if($flag)   {
                                $materia_scelta = $materie[$indice];			
				$materia_scelta[count($materia_scelta)] = $event; 	
				$materie[$indice] = $materia_scelta;
                            }   else    {
				$materie_nome[count($materie_nome)] = $titolo[1]; 
                                $materia_scelta = array($event); 
				$materie[count($materie)] = $materia_scelta; 	
                            }
                            $i += 1; 
			}//fine if
		}//Fine foreach
                //Verifico che ho prelevato un evento riferito  ad almeno una materia
		if(count($materie_nome) > 1)    {
		//Per ogni materia estraggo l'array degli eventi
		for($j=1;$j<count($materie_nome);$j++)  {
                    $eventi = $materie[$j];
                    
					
                    $htmltag .= "<table class='widefat'";
                    if ($backend) {
                        $htmltag.=" style='width:90%; margin:auto;'";
                        $width=" style='width:200px;'";
                    }
                    $htmltag .= ">";
                    if ($backend==0) {
                    $htmltag .= "<col style='width:16%'>
        						<col style='width:21%'>
        						<col style='width:10%'>
								<col style='width:12%'>
        						<col style='width:13%'>
        						<col style='width:13%'>
								<col style='width:15%'>";
								}
					$htmltag .= "			
                        <thead>
                            <tr><th colspan=";
                                
                            if ($backend) {
                            	$htmltag.="'8'";
                            	}
                            	else  {
                            	$htmltag.="'7'";
                            	}
							$htmltag .= " style='text-align:center;font-size:16px; font-weight:bold;'>". $materie_nome[$j] ."</th></tr>
                            <tr>
                                <th>Appello</th>
                                <th>Data appello</th>
                                <th>Ora</th>
                                <th>Durata</th>
                                <th>Luogo</th>
                                <th>Tipo</th>
                                <th $width>Note</th>";
                    if ($backend)
                        $htmltag .="<th>Link</th>
                            </tr>
                            </thead>
                            <tbody>"; 				
                    //per ogni array degli eventi estraggo ogni singolo evento e popolo la tabella
                    	for ($h=0;$h<=(count($eventi)-1);$h++){
                    	$event = $eventi[$h];
                        $tipologia = explode("-",$event->getSummary());
                            $temp_dt_inizio =  explode("T",$event->getStart()->getDateTime());
                            $temp_dt_fine = explode("T",$event->getEnd()->getDateTime());
                            $d_event_start = new DateTime($temp_dt_inizio[0] . ' ' . substr($temp_dt_inizio[1],0,5) . ':00');
                            $d_event_end = new DateTime($temp_dt_fine[0] . ' ' . substr($temp_dt_fine[1],0,5) . ':00');
                            $diff =   round(abs($d_event_end->format('U') - $d_event_start->format('U'))) / (60*60);
                            if($diff == 1)  {
				$durata = '1 ora'; 	
                            }   else    {
				$durata = $diff . ' ore'; 	
                            }
                            $mese =  get_Month_name(substr($d_event_start->format('d-m-Y'),3,2)); 
                            $date_def_inizio = $d_event_start->format("d-m-Y"); 
                            $ora_def =  $d_event_start->format('H:i'); 
                            $note = $event->getDescription();
							$luogo = $event->getLocation(); 	
                            //}
                            $class="";
                            if($d_event_start < $today)
                                $class=" class='data_precedente'";
                            $htmltag .= "<tr".$class.">
                                <td>" . $mese . "</td>
                                <td>". $date_def_inizio  ."</td>
                                <td>". $ora_def ."</td>
                                <td>". $durata  ."</td>
                                <td>". $luogo ."</td>
                                <td>". $tipologia[0]  ."</td>
                                <td>". $note ."</td>";
                                
                            if ($backend)
                                $htmltag .="<td style='text-decoration:none'><a href='#' name='".  substr($event->getid(),(count($event->getid())-27))."' id='Link_Modifica' onclick=modifica_evento('". substr($event->getid(),(count($event->getid())-27))."')>Modifica</a> | <a href='#' name='".  substr($event->getid(),(count($event->getid())-27))."' id='Link_Elimina' onclick=elimina_evento('".substr($event->getid(),(count($event->getid())-27))."')>Elimina</a></td>";
                            $htmltag .="</tr>"; 					
                    	}
                    $htmltag .= "</tbody><tfoot><tr><th colspan="; 
                    if ($backend) {
									$htmltag .= "'8'";
								  }
					else {
                					$htmltag .= "'7'";
                		 }
                	$htmltag .= "></th></tr></tfoot></table><br />";
                }
            }   else    {
		$htmltag .= "<p class='Alert_orange'>Non sono presenti eventi di interesse educativo per la data impostata</p>";  																
            }
	}   else    {
            $htmltag .= "<p class='Alert_orange'>Non sono presenti eventi per la data prevista</p>"; 
	}//Fine if(count($eventFeed) > 0)
        print($htmltag);
    }  catch (Exception $e) {
        print("<p class='Alert_red'>Errore: ".$e->getMessage()."</p>");				
    }		
    break;
	
    /*Si attiva quando viene richiesta la visualizzazione dei dati di un singolo evento contrassegnato dal suo Id*/
    case 5:            
        try {    
            $gdata = calendar_connect();

            $service = $gdata['service'];
            $id = $gdata['id'];  
            $eventID = $_POST['Id'];
            $event = $service->events->get($id, $eventID);
            
            $titolo_arr = explode(' - ',$event->getSummary());
            $titolo = $titolo_arr[1];
            $luogo = $event->getLocation();        
			$temp_dt_inizio =  explode("T",$event->getStart()->getDateTime());
            $temp_dt_fine = explode("T",$event->getEnd()->getDateTime());
            	
            $d_event_start = new DateTime($temp_dt_inizio[0] . ' ' . substr($temp_dt_inizio[1],0,5) . ':00');
            $d_event_end = new DateTime($temp_dt_fine[0] . ' ' . substr($temp_dt_fine[1],0,5) . ':00');
            $diff =   round(abs($d_event_end->format('U') - $d_event_start->format('U'))) / (60*60);
            $giorno = $d_event_start->format('d'); 
            $mese = $d_event_start->format('m');     
            $anno=$d_event_start->format('Y');	                   
            $ore = $d_event_start->format('H'); 
            $minuti = $d_event_start->format('i'); 
		   
            if($titolo_arr[0]=="Laboratorio")
                $tipologia = "Lab";
            else
                $tipologia=$titolo_arr[0];
            $note = $event->getDescription();
        
            print("ok||".$titolo."||".$luogo."||".$giorno."||".$mese."||".$anno."||".$ore."||".$minuti."||".$diff."||".$tipologia."||".$note);
        	}   catch (Exception $e) {
            print("<p class='Alert_red'>Errore: ".$e->getMessage()."</p>");
	}
    break;

    /*
    Si attiva quando viene richiesto l'update dei dati di un evento da parte dell'amministratore di sistema.
    */
    case 6:            	
        $tipo = $_POST['tipologia'];
	$nome = $_POST['Nome'];
	$note = $_POST['Note'];
	$luogo = $_POST['Luogo'];
	$id_evento = $_POST['Id_evento'];	
	$durata = $_POST['durata'];
	$ora_ora_inzio = $_POST['ora_ora_inzio'];
	$minuti_ora_inizio = $_POST['minuti_ora_inizio'];
		
	//NB: Mese ed anno < 10 hanno una sola cifra
	//sistemo.
	$giorno_data_inizio = $_POST['giorno_data_inizio'];
        $mese_data_inizio = $_POST['mese_data_inizio'];
        $anno_data_inizio = $_POST['anno_data_inizio'];
	$startTime =  $ora_ora_inzio . ':' . $minuti_ora_inizio ;	
	$startDate =  $anno_data_inizio ."-" . $mese_data_inizio . "-" . $giorno_data_inizio;        
        $timezone = new DateTimeZone('Europe/Rome');
        $offset = $timezone->getOffset(new DateTime($startDate));
	$tzOffset = "+0".$offset/3600;       
	$endDate =  $startDate;	
	$d_start = new DateTime($startDate . ' ' . $startTime . ':00');
        $d_end = new DateTime($startDate . ' ' . $startTime . ':00');
		
	if($durata == 1)    {
            $d_end->modify('+1 hour');
	}   else    {
            $d_end->modify('+' . $durata . ' hours');
	}	
	$endTime = substr($d_end->format('H:i:s'),0,5);
		
	//Inizio richiesta di accesso a Goggle.
	try{	
            //$gdata = Gconnect();
            
            $gdata = calendar_connect();
            $service = $gdata['service'];
            $id = $gdata['id'];
					
            $event = $service->events->get($id, $id_evento);							
            // Populate the event with the desired information
            // Note that each attribute is crated as an instance of a matching class
            $event->setSummary( $tipo . ' - ' . $nome);
			$event->setLocation($luogo);
			$start = new Google_Service_Calendar_EventDateTime();
    		$start->setDateTime("{$startDate}T{$startTime}:00.000{$tzOffset}:00");
    		$event->setStart($start);
            $end = new Google_Service_Calendar_EventDateTime();
    		$end->setDateTime("{$endDate}T{$endTime}:00.000{$tzOffset}:00");
    		$event->setEnd($end);
            $event->setDescription($note);			
            // Upload the event to the calendar server
            // A copy of the event as it is recorded on the server is returned
            $updatedEvent = $service->events->update($id, $event->getId(), $event);
            print("<p class='Alert_green'>Update dell'evento effettuato con successo</p>");
        }   catch (Exception $e) {
            print("<p class='Alert_red'>Errore: ".$e->getMessage()."</p>");	
	}
    break;
	
case 7:	
	try {
            $calendar_selected = $_POST['Calendar'];
            Write_google_calendar_name($calendar_selected);
			$gdata = calendar_connect();
            if(isset($gdata))
                print("<p class='Alert_green'>Connessione riuscita</p>");
        } catch (Exception $e) {
            print("<p class='Alert_red'>Errore: ".$e->getMessage()."</p>");
        }       
    break;


case 8:	
	$lista=get_list_calendars();
	print($lista);
    break;

}

function get_list_calendars() {
		  try {
			$valore_blog=get_current_blog_id();
			$client = Load_client($valore_blog);
    		$client->setAccessToken(Load_token($valore_blog));
			// Crea un istanza del servizio Calendar
    
   			$calendarService = new Google_Service_Calendar($client);
    		$gdata['service'] = $calendarService;
    		$calendarList = $calendarService->calendarList->listCalendarList();
    		foreach ($calendarList->getItems() as $calendarListEntry) {
    		if (($calendarListEntry->getSummary()!="Compleanni") && ($calendarListEntry->getId()!="it.italian#holiday@group.v.calendar.google.com"))
    		$lista = $lista.$calendarListEntry->getSummary()." - ".$calendarListEntry->getId()."    ";   		
			}
    		return $lista;	
		} catch (Exception $e) {
            print("<p class='Alert_red'>Errore: ".$e->getMessage()."</p>");
        }       
}


/*
Funzione che restituisce il mese,in formato italiano, sulla base del valore numerico che possiede in input
*/

function get_Month_name($mese)  {
	$nome_mese = ''; 
	if($mese == 1)
		$nome_mese = 'Gennaio'; 
	if($mese == 2)
		$nome_mese = 'Febbraio'; 
	if($mese == 3)
		$nome_mese = 'Marzo'; 
	if($mese == 4)
		$nome_mese = 'Aprile'; 
	if($mese == 5)
		$nome_mese = 'Maggio'; 
	if($mese == 6)
		$nome_mese = 'Giugno'; 
	if($mese == 7)
		$nome_mese = 'Luglio'; 
	if($mese == 8)
		$nome_mese = 'Agosto'; 
	if($mese == 9)
		$nome_mese = 'Settembre'; 
	if($mese == 10)
		$nome_mese = 'Ottobre'; 
	if($mese == 11)
		$nome_mese = 'Novembre'; 
	if($mese == 12)
		$nome_mese = 'Dicembre'; 
	return $nome_mese; 
}


/*
Funzione che riceve in input un numero, e se tale numero ÃƒÂ¨ ad una singola cifra, restituisce l'equivalente a due cifre.
*/
function fix_string($numero)    {
    if($numero == 1 || $numero == 2 ||$numero == 3 || 
       $numero == 4 || $numero == 5 || $numero == 6 || 
       $numero == 7 || $numero == 8 || $numero == 9)    
		$numero = '0' . $numero; 	
    return $numero; 
}

/*
 * Connessione a Google Calendar
 */
function Gconnect() { 
     $client=set_settings_client();
        if (! $client->getAccessToken()) {
            $auth = $client->createAuthUrl();
			print ("<p class='Alert_green'><a name=\"". $auth ."\" id=\"Link_Autentica\" href=\"". $auth ."\" target=\"_blank\">Attiva utente</a></p>");                 
			//print ("<td style='text-decoration:none'><a href='#' name='carica_calendari' id='Link_Carica_Calendari' onclick=carica_lista_calendari('".plugins_url(null, __FILE__)."')>Carica i calendari</a></td>");
		}
}


function calendar_connect() {
	$valore_blog=get_current_blog_id();
	$client = Load_client($valore_blog);
    $dati_accesso = Load_google_access_data($valore_blog);
    $calendar = $dati_accesso[2];	
    $client->setAccessToken(Load_token($valore_blog));
	// Crea un istanza del servizio Calendar
    $calendarService = new Google_Service_Calendar($client);
    $gdata['service'] = $calendarService;
    $calendarList = $calendarService->calendarList->listCalendarList();
    
    //imposta il caledario specificato 
    foreach ($calendarList->getItems() as $calendarListEntry) {
    //se l'id del calendario corrente Ã¨ uguale a quello impostato legge il nome
        if ($calendarListEntry->getId() == $calendar){
            $gdata['id'] = $calendarListEntry->getId();
            $gdata['name'] = $calendarListEntry->getSummary();
		}
    }
    
    if (!$gdata['name'])
        throw new Exception("Calendario non trovato");
   
    return $gdata;
}


?>