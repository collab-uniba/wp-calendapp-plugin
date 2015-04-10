<?php

/**
 * Form di login per le credenziali dell'account Google
 */
require_once 'Load_Write.php';
require_once 'google-api-php-client/src/Google/Client.php';
require_once 'google-api-php-client/src/Google/Service/Calendar.php';
require_once 'google-api-php-client/autoload.php';

//require_once 'jQuery_interop.php';
function Display_settings_form()   {
    $dati = Load_google_access_data(get_current_blog_id()); 
    $alert = ""; 
    $value_client_id=""; 
    $value_client_secret=""; 
    $value_calendar="";  
  		
    if($dati[0] != "" && $dati[1] != "")  { 
    	if($dati[2] != "") { 
        	$value_client_id = $dati[0];	
        	$value_client_secret = $dati[1];
        	$value_calendar = $dati[2];
        	$alert = "<p>Dati già impostati in precedenza!</p>"; }
        else	{
        $alert = "<p class='Alert_orange'>Calendario non ancora impostato</p>";
    	}    	
    } else	{
        $alert = "<p class='Alert_orange'>Dati non ancora impostati</p>";
    } 
    ?>

    <h1>Impostazioni di Google Calendar</h1>
    <p><hr size=1 noshade></p>
    <div id="alert"><?php echo $alert; ?></div>
    
    <form action="" METHOD="POST">
        <table class="form-table">
                <tr>
                    <td><label for="username">Inserisci il client ID della web application:</label></td>
                        <td><input type="text" name="username" size=80 id="client_id" value="<?php echo $value_client_id; ?>"/> </td>
                </tr>
                <tr>
                    <td><label for="password">Inserisci il client secret della web application:</label></td>
                    <td><input type="password" name="password" size=80 id="client_secret" value="<?php echo $value_client_secret;  ?>" /></td>
                </tr>
                <tr>
                    <td><label for="calendar">Inserisci l' indirizzo ID del calendario (creato dal tuo account gmail):</label></td>             	
                	<td><select name="calendar" id="cal" >
                		
                	<?php
          if ($dati[0] != "" && $dati[1] != "") {
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

                			if($dati[0] != "" && $dati[1] != "")  {
                				$lista_cal=explode("    ",$lista);
                				for ($i=0;$i<(count($lista_cal)-1);$i++){
									$opt_str="<option id='".$i."'";
                        			$estrai_cal=	explode(" - ",$lista_cal[$i]);
                        				if ($estrai_cal[1] == $value_calendar)
                        				$opt_str=$opt_str." selected=\"selected\"";
                        			$opt_str=$opt_str." >".$lista_cal[$i]."</option>";
								echo $opt_str;
								}
							}
		  }
                		?>
                		</select>
                	</td>
                </tr>
                <tr>
                    <td><input type="button" value="Imposta credenziali e autenticati" id="btnsave_cred"/></td>
                    <td><input type="button" value="Carica i calendari" id="btnload_calendars"/></td>
					<td><input type="button" value="Connettiti" id="btnsave_link_calendar"/></td>
                    <td>
                        <input type="hidden" value="0" id="flag" />
                        <input type="hidden" value="<?php  echo plugins_url(null, __FILE__) ?>" id="path"  />
                        <input type="hidden" value=" <?php echo plugin_dir_path(__FILE__); ?>  " id="path2" />
                    </td>
                </tr>
        </table>
        
    </form>
    <p><hr size=1 noshade></p>          
<?php } ?>           
            
