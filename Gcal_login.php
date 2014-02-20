<?php

/**
 * Form di login per le credenziali dell'account Google
 */
require_once 'Load_Write.php';
function Display_settings_form()   {
    $dati = Load_google_access_data(); 
    $alert = ""; 
    $value_user=""; 
    $value_pwd=""; 
    $value_calendar="";  

    if($dati[0] != "" && $dati[1] != "" && $dati[2] != "")  {   		
        $value_user = $dati[0];	
        $value_pwd = $dati[1];
        $value_calendar = $dati[2];
        $alert = "<p>Dati già impostati in precedenza!</p>";
    } else	{
        $alert = "<p class='Alert_orange'>Dati non ancora impostati</p>";
    } ?>

    <h1>Impostazioni di Google Calendar</h1>
    <p><hr size=1 noshade></p>
    <div id="alert"><?php echo $alert; ?></div>
    
    <form action="" METHOD="POST">
        <table class="form-table">
                <tr>
                    <td><label for="username">Inserisci il tuo indirizzo e-mail Google:</label></td>
                        <td><input type="text" name="username" size=25 id="usr" value="<?php echo $value_user; ?>"/> </td>
                </tr>
                <tr>
                    <td><label for="password">Inserisci la tua password di Google:</label></td>
                    <td><input type="password" name="password" size=25 id="pwd" value="<?php echo $value_pwd;  ?>" /></td>
                </tr>
                <tr>
                    <td><label for="calendar">Inserisci il nome del calendario:</label></td>
                    <td><input type="text" name="calendar" size=25 id="calendar" value="<?php echo $value_calendar;  ?>" /></td>
                </tr>
                <tr>
                    <td><input type="button" value="Imposta" id="btnsave"/></td>
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
            

