<?php

include_once( 'Zend'.DIRECTORY_SEPARATOR.'Gdata'.DIRECTORY_SEPARATOR.'ClientLogin.php');
include_once( 'Zend'.DIRECTORY_SEPARATOR.'Gdata'.DIRECTORY_SEPARATOR.'Calendar.php');
include_once( 'Zend'.DIRECTORY_SEPARATOR.'Gdata'.DIRECTORY_SEPARATOR.'Calendar'.DIRECTORY_SEPARATOR.'EventQuery.php');
require_once 'Load_Write.php';

$richiesta = $_POST['Richiesta'];

switch($richiesta)  {
	
    /*Si attiva quando viene richiesta la scrittura delle credenziali di accesso all'account di Google.*/
    case 0:	
	$username = $_POST['Username'];
	$password = $_POST['Password'];
        $calendar = $_POST['Calendar'];
	try {
            Write_google_access_data($username,$password,$calendar);
            $gdata = Gconnect();
            if(isset($gdata))
                print("<p class='Alert_green'>Connessione riuscita</p>");
        } catch (Zend_Gdata_App_HttpException $e){
            print("<p class='Alert_red'>Errore: Calendario non trovato</p>");
        } catch (Zend_Gdata_App_AuthException $e){
            print("<p class='Alert_red'>Login fallito: Credenziali Google non corrette</p>");
        }   catch (Zend_Gdata_App_Exception $e) {
            print("<p class='Alert_red'>Errore: ".$e->getMessage()."</p>");			
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
            $gdata = Gconnect();
            $service = $gdata['service'];
            $url = $gdata['url'];
									
            // Create a new entry using the calendar service's magic factory method
            $event= $service->newEventEntry();
            // Populate the event with the desired information
            // Note that each attribute is crated as an instance of a matching class
            $event->title = $service->newTitle( $tipo . ' - ' . $nome);
            $event->where = array($service->newWhere($luogo));
            $event->content = $service->newContent( $note );			 
            $when = $service->newWhen();
            $when->startTime = "{$startDate}T{$startTime}:00.000{$tzOffset}:00";
            $when->endTime = "{$endDate}T{$endTime}:00.000{$tzOffset}:00";
            $event->when = array($when);
            // Upload the event to the calendar server
            // A copy of the event as it is recorded on the server is returned
            $service->insertEvent($event,$url);
            print("<p class='Alert_green'>Evento inserito con successo</p>");
        }  catch (Zend_Gdata_App_HttpException $e){
            print("<p class='Alert_red'>Errore: Calendario non trovato</p>");
        } catch (Zend_Gdata_App_AuthException $e){
            print("<p class='Alert_red'>Login fallito: Credenziali Google non corrette</p>");
        }   catch (Zend_Gdata_App_Exception $e) {
            print("<p class='Alert_red'>Errore: ".$e->getMessage()."</p>");
        } catch (Exception $e) {
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
        $href = $_POST['href'];    
        try {
            $gdata = Gconnect();
            $service = $gdata['service'];
            $url = $gdata['url'];        
            $service->delete($href);
        } catch (Zend_Gdata_App_HttpException $e){
            print("<p class='Alert_red'>Errore: Calendario non trovato</p>");
        } catch (Zend_Gdata_App_AuthException $e){
            print("<p class='Alert_red'>Login fallito: Credenziali Google non corrette</p>");
        } catch (Zend_Gdata_App_Exception $e) {
            print("<p class='Alert_red'>Errore: ".$e->getMessage()."</p>");				
	} catch (Exception $e) {
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
	//Recupero delle date impostate dall'amministratore..
        $interval_date = Load_interval_date();				
	$d_start = new DateTime($interval_date[0]);
	$d_end = new DateTime($interval_date[1]);	
	try {	
            $gdata = Gconnect();
            $service = $gdata['service'];
            $url = $gdata['url'];                              
            $query = $service->newEventQuery($url);
            $query->setUser(NULL);						
            $query->setVisibility(NULL);
            $query->setProjection(NULL);						
            $query->setOrderby('starttime');
            $query->setSortOrder('descending');
            $query->setMaxResults(500);
            $query->setStartMin($interval_date[0]);
            $query->setStartMax($interval_date[1]);							
            // Retrieve the event list from the calendar server
            $eventFeed = $service->getCalendarEventFeed($query);
            //Se recupero degli eventi inizio a definire la tabella..
            //Altrimenti mostro un messaggio per avvertire l'utente..
            if(count($eventFeed) > 0)   {
		$i = 0 ; 
		$eventi = count($eventFeed);	
		$materie_nome = array();
		$materie = array();
		$materie[0] = 'empty'; 
		$materie_nome[0] = 'empty'; 		
							
		/*
		Per ogni evento recuperato 
		Se presente comincio a dividere gli eventi in base alle materie di riferimento
		l'array materie_nome conterrà i nomi delle materie che il sistema ha rilevato.
		l'array materie conterrà tutti gli eventi riferiti alla materia il cui titolo è memorizzato 
		nella stessa posizione dell'array 	materie.
		*/	
		foreach ($eventFeed as $event) {
                	$titolo = explode(" - ",$event->title);
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
                    $htmltag .= ">
                        <thead>
                            <tr>
                                <th colspan='8' style='text-align:center;font-size:16px; font-weight:bold;'>". $materie_nome[$j] ."</th>
                            </tr>
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
                    for($h=(count($eventi)-1);$h>=0;$h--)   {
                        $evento = $eventi[$h];
                        $tipologia = explode("-",$evento->title);
                        foreach($evento->when as $quando)   {
                            $temp_dt = explode(" ",$quando);
                            $temp_dt_inizio =  explode("T",$temp_dt[1]);
                            $temp_dt_fine = $p_fine = explode("T",$temp_dt[3]);
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
                            $note = $evento->content;
                            foreach($evento->where as $dove)    {
				$luogo = $dove; 	
                            }
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
                                $htmltag .="<td style='text-decoration:none'><a href='#' name='".  substr($evento->id,(count($evento->id)-27))."' id='Link_Modifica' onclick=modifica_evento('". substr($evento->id,(count($evento->id)-27))."')>Modifica</a> |
                                           <a href='#' name='".  substr($evento->id,(count($evento->id)-27))."' id='Link_Elimina' onclick=elimina_evento('". $evento->getEditLink()->href."')>Elimina</a></td>";
                            $htmltag .="</tr>"; 					
			}
                    }
                    $htmltag .= "<tbody><tfoot><tr><th colspan='8'></th></tr></tfoot></table><br />"; 
                }
            }   else    {
		$htmltag .= "<p class='Alert_orange'>Non sono presenti eventi di interesse educativo per la data impostata</p>";  																
            }
	}   else    {
            $htmltag .= "<p class='Alert_orange'>Non sono presenti eventi per la data prevista</p>"; 
	}//Fine if(count($eventFeed) > 0)
        print($htmltag);
    } catch (Zend_Gdata_App_HttpException $e){
        print("<p class='Alert_red'>Errore: Calendario non trovato</p>");
    } catch (Zend_Gdata_App_AuthException $e){
        print("<p class='Alert_red'>Login fallito: Credenziali Google non corrette</p>");
    } catch (Zend_Gdata_App_Exception $e) {
        print("<p class='Alert_red'>Errore: ".$e->getMessage()."</p>");				
    } catch (Exception $e) {
        print("<p class='Alert_red'>Errore: ".$e->getMessage()."</p>");				
    }		
    break;
	
    /*Si attiva quando viene richiesta la visualizzazione dei dati di un singolo evento contrassegnato dal suo Id*/
    case 5:            
        try {    
            $gdata = Gconnect();
            $service = $gdata['service'];
            $url = $gdata['url'];    
            $eventURL = $url."/" . $_POST['Id'];
            $event = $service->getCalendarEventEntry($eventURL);
            $titolo_arr = explode(' - ',$event->title);
            $titolo = $titolo_arr[1];
            $luogo = $event->where[0];        
            $temp_dt = explode(" ",$event->when[0]);
            $temp_dt_inizio =  explode("T",$temp_dt[1]);
            $temp_dt_fine = $p_fine = explode("T",$temp_dt[3]);	
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
            $note = $event->content;
        
            print("ok||".$titolo."||".$luogo."||".$giorno."||".$mese."||".$anno."||".$ore."||".$minuti."||".$diff."||".$tipologia."||".$note);
        
	}  catch (Zend_Gdata_App_HttpException $e){
            print("<p class='Alert_red'>Errore: Calendario non trovato</p>");                        
        } catch (Zend_Gdata_App_AuthException $e){
            print("<p class='Alert_red'>Login fallito: Credenziali Google non corrette</p>");
        }   catch (Zend_Gdata_App_Exception $e) {
            print("<p class='Alert_red'>Errore: ".$e->getMessage()."</p>");		
        } catch (Exception $e) {
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
            $gdata = Gconnect();
            $service = $gdata['service'];
            $url = $gdata['url'];                            
            $eventURL = $url."/" . $id_evento;					
            $event = $service->getCalendarEventEntry($eventURL);							
            // Populate the event with the desired information
            // Note that each attribute is crated as an instance of a matching class
            $event->title = $service->newTitle( $tipo . ' - ' . $nome);
            $event->where = array($service->newWhere($luogo));								 
            $when = $service->newWhen();
            $when->startTime = "{$startDate}T{$startTime}:00.000{$tzOffset}:00";
            $when->endTime = "{$endDate}T{$endTime}:00.000{$tzOffset}:00";
            $event->when = array($when);
            $event->content = $service->newContent( $note );			
            // Upload the event to the calendar server
            // A copy of the event as it is recorded on the server is returned
            $event->save();
            print("<p class='Alert_green'>Update dell'evento effettuato con successo</p>");
        }  catch (Zend_Gdata_App_HttpException $e){
            print("<p class='Alert_red'>Errore: Calendario non trovato</p>");
        } catch (Zend_Gdata_App_AuthException $e){
            print("<p class='Alert_red'>Login fallito: Credenziali Google non corrette</p>");
        }   catch (Zend_Gdata_App_Exception $e) {
            print("<p class='Alert_red'>Errore: ".$e->getMessage()."</p>");		
	} catch (Exception $e) {
            print("<p class='Alert_red'>Errore: ".$e->getMessage()."</p>");	
	}
    break;
	
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
Funzione che riceve in input un numero, e se tale numero è ad una singola cifra, restituisce l'equivalente a due cifre.
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
    $dati_accesso = Load_google_access_data();
    $user = $dati_accesso[0];
    $pwd = $dati_accesso[1];
    $calendar = $dati_accesso[2];
 
    // Create an authenticated HTTP client
    $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pwd, Zend_Gdata_Calendar::AUTH_SERVICE_NAME);						 
    // Create an instance of the Calendar service
    
    $gdata['service'] = new Zend_Gdata_Calendar($client);		
    $calFeed = $gdata['service']->getCalendarListFeed();
    //imposta il caledario specificato 
    foreach ($calFeed as $cal) {
        if ($cal->title->text == $calendar)
            $gdata['url'] = $cal->link[0]->href;
    }
    
    if (!$gdata['url'])
        throw new Exception("Calendario non trovato");
    
    return $gdata;
}

?>