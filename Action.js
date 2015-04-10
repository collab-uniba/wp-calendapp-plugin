// JavaScript Document

var $j = jQuery.noConflict();
var client_secret = "pwd";
var path = "path";
var path2 = "path_fisico"; 
var client_id = "usr";

/*
Funzione che viene invocata quando viene selezionato un link di modifica di un evento
*/
function modifica_evento(id)   {
  var scadenza = new Date();
  var adesso = new Date();
  scadenza.setTime(adesso.getTime() + (parseInt(1) * 60000));
  document.cookie = escape(id) + '; expires=' + scadenza.toGMTString() + '; path=/';
  window.location = "admin.php?page=Enter_event&Id_Gcal="+id;
}

/*
Funzione che recupera i dati di un evento e compila il form relativo
*/
function compila_form_modifica(num) {
  var path_http = $j('#path').val(); 
  var path_php = path_http + '/jQuery_interop.php';
  var path_fisico = $j('#path2').val();
  $j('#alert').html('<p>Caricamento dell\'evento in corso..</p>');
  $j.post(path_php, { 'path': path_fisico,'Richiesta': 5, 'Id': num }, function(data){
      var event = data.split("||");
      //if (event[0]=="ok"){
        $j('#alert').text('');
        $j('#Ins_txt_name_event').val(event[1]);
        $j('#Ins_txt_locaz_event').val(event[2]);
        $j('#hidd_date').val(event[5]+"-"+event[4]+"-"+event[3]);
        $j( '#date' ).datepicker( "option", "defaultDate",$j('#hidd_date').val());
        $j('#date').val($j.datepicker.formatDate('dd MM yy', new Date(event[5]+"-"+event[4]+"-"+event[3])));
        $j('#Ins_Hour_time').val(event[6]);
        $j('#Ins_Minutes_time').val(event[7]);
        $j('#Ins_Durata').val(event[8]);
        $j('#Ins_Tipologia').val(event[9]);
        $j('#Ins_Note').val(event[10]);
      //} else
        //   $j('#alert').html(event[0]);
  });
}

/*
Funzione che viene invocata quando viene selezionato un link di eliminazione di un evento
*/
function elimina_evento(href)   {
  var path_php = path_http + '/jQuery_interop.php';
  $j('#content').text('');
  $j('#alert').html('<p>Caricamento degli eventi in corso..</p>');
  $j.post(path_php, { 'Richiesta': 3,'href': href}, function(data){
    //if (data!="")   {
    //    $j('#alert').html(data);
    //    $j('#alert').html('<p class="Alert_green">prova</p>');
    //} else{
        var title = 'all';
         $j.post(path_php, { 'Richiesta': 4, 'examTitle':title, 'backend':1 }, function(data){
            $j('#alert').html('<p class="Alert_green">Evento eliminato</p>');
            $j('#content').append(data);
        });
    //}			 
  });
}

/*
Funzione che viene invocata al termine del caricamento di una pagina
*/
$j(document).ready(function($j) {
    /*
    Funzione che restituisce:
    True se una data è valida
    False altrimenti
    */
    function isValid(value) {
        var check = false;
        var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
        if( re.test(value)){
            var adata = value.split('/');
            var mm = parseInt(adata[1],10); // was gg (giorno / day)
            var dd = parseInt(adata[0],10); // was mm (mese / month)
            var yyyy = parseInt(adata[2],10); // was aaaa (anno / year)
            var xdata = new Date(yyyy,mm-1,dd);
            if ( ( xdata.getFullYear() == yyyy ) && ( xdata.getMonth () == mm - 1 ) && ( xdata.getDate() == dd ) )
                check = true;
            else
                check = false;
        } else
            check = false;
        return check;
    }
    
    /*
    Funzione che effettua la validazione delle TextBox in cui viene inserito il nome e il luogo di un evento.
    La funzione restituisce:
    True se entrembi i campi sono stati compilati correttamente
    False altrimenti
    */
    function Check_input(nome,luogo)    {
        var flag = true; 	
	if(nome.length == 0 || luogo.length == 0)   {
            flag = false;
	} 	
	return flag; 	
    }
	
    /*
    Funzione che riceve in input una stringa contenete i primi tre caratteri del nome del mese e restituisce il numero corrispondente.
    */
    function from_name_month_to_number(name)    {
        var number = 0; 
	switch(name)    {
            case 'Jan':
                number = 01; 
            break;		
            case 'Feb':
                number = 02; 
            break;		
            case 'Mar':
		number = 03;
            break;	
            case 'Apr':
		number = 04; 
            break;	
            case 'May':
		number = 05; 
            break;	
            case 'Jun':
		number = 06; 
            break;		
            case 'Jul':
		number = 07; 
            break;	
            case 'Aug':
                number = 08; 
            break;		
            case 'Sep':
		number = 09; 
            break;		
            case 'Oct':
		number = 10; 
            break;		
            case 'Nov':
		number = 11; 
            break;		
            case 'Dec':
		number = 12; 
            break;
        }	
	return number; 
    }	
		
    if(client_secret == "" || client_id == "")  {
        $j('#flag').val('0');
    }
    
    /*
    Funzione che so attiva quando l'utente effettua la modifica di un evento
    */
    $j('#Btn_modify_event').click(function($){		
	var Mod_name = $j('#Ins_txt_name_event').val(); 
	var Mod_luogo = $j('#Ins_txt_locaz_event').val();
	var Mod_note = $j('#Ins_Note').val();
	var Mod_Tipo = $j('#Ins_Tipologia option:selected').text();
	var Mod_Durata = $j('#Ins_Durata option:selected').text();
    var Id_evento = $j('#Id_evento').val();	
	var date = ($j('#hidd_date').val()).split("-");
	var Mod_Year_Date = date[0]; 
	var Mod_Month_Date = date[1];
	var Mod_Day_Date = date[2];
	var Mod_Hour_Date = $j('#Ins_Hour_time option:selected').text();
	var Mod_Minutes_Date = $j('#Ins_Minutes_time option:selected').text();
	var path = $j('#path').val();
	var path_php = path + '/jQuery_interop.php';  
	$j('#result').html('');
	$j('#alert').html('<p>Upgrade in corso.. 30% completato</p>');			
	if(isValid(Mod_Day_Date + "/" + Mod_Month_Date + "/" + Mod_Year_Date))  {			
            if(Check_input(Mod_name,Mod_luogo)) {	
                $j('#alert').html('<p>Upgrade in corso.. 60% completato</p>');
                var Mod_Data_inizio = new Date(Mod_Year_Date,Mod_Month_Date-1,Mod_Day_Date,Mod_Hour_Date,Mod_Minutes_Date,'00','00');			
                if(Mod_Data_inizio > Date.now())    {
                    $j('#alert').html('<p>Upgrade in corso.. 90% completato</p>');		
                	$j.post(path_php, {  'Richiesta': 6, 'tipologia':  Mod_Tipo ,'Luogo': Mod_luogo,'Note': Mod_note,'Nome': Mod_name, 
                                        'giorno_data_inizio': Mod_Day_Date,'mese_data_inizio': Mod_Month_Date,'anno_data_inizio': Mod_Year_Date,
                                        'ora_ora_inzio' : Mod_Hour_Date , 'minuti_ora_inizio': Mod_Minutes_Date,'durata': Mod_Durata,
                                        'Id_evento': Id_evento }, function(data){   $j('#alert').html(data);  });
                }   else    {
                    $j('#alert').html("<p class='Alert_orange'>Non si può inserire un evento con una data precedente di quella attuale</p>");		
                }
            }   else    {
                $j('#alert').html("<p class='Alert_orange'>Campo nome o campo luogo non compilato correttamente");		
            }
        }   else    {
            //Se la data di inizio non è valida
            $j('#alert').html("<p class='Alert_orange'>Data inserita non valida");		  	
        } 
    });
	
    /*
    Funzione che si attiva quando l'utente imposta l'intervallo di visualizzazione degli eventi.
    */
    $j('#btn_set_interval').click(function($){
	path = $j('#path').val();
	path2 = $j('#path2').val();
        var date_start = $j('#hidd_date_start').val();
        var date_end = $j('#hidd_date_end').val();
	var path_php = path + '/jQuery_interop.php'; 
	$j('#client_id').val();
        $j.post(path_php, { 'data_inizio': date_start,'data_fine' : date_end , 'Richiesta': 2 }, function(data){
            $j('#alert').html(data);
        });
        return false;
    });
	
    /*
    Funzione che si attiva quando l'utente inserisce le credenziali di autenticazione a Google.
    */
    $j('#btnsave_cred').click(function($){		
        client_secret =  $j('#client_secret').val();
		client_id = $j('#client_id').val();
		path = $j('#path').val();		
	if (client_secret == ""  || client_id == "")    {
            $j('#alert').html("<p class='Alert_orange'> Tutti i campi sono obbligatori</p>");
	}   else    {
            var valore = '1'; 
            var path_php = path + '/jQuery_interop.php'; 
            $j('#flag').val(valore);
            $j('#client_secret').val();
            $j.post(path_php, { 'Client_id':  client_id , 'Client_secret' : client_secret , 'Richiesta': 0 }, function(data){
                $j('#alert').html(data);
            });
        }	
	return false;
    });


	/*
    Funzione che si attiva quando l'utente, dopo essersi autenticato, carica i calendari disponibili.
    */
    $j('#btnload_calendars').click(function($){	
            var valore = '1'; 
            var path_php = path + '/jQuery_interop.php';
            var calendars='';
            $j('#flag').val(valore);
            $j('#client_secret').val();
            	$j.post(path_php, { 'Richiesta': 8 }, function(data){
            	calendars = data.split("    ");
				$j('option', '#cal').remove();
            	for(i=0;i<calendars.length-1;i++){                          
                	$j("#cal").append("<option id='"+ i +"'>"+calendars[i]+"</option>");
                	}
	            });
	return false;
	});


   /*
    Funzione che si attiva quando l'utente, dopo aver selezionato il calendario, si connette.
    */    
   $j('#btnsave_link_calendar').click(function($){
    		var cal = $j("#cal").val();
    		cal = cal.split(" - ");
            var valore = '1'; 
            var path_php = path + '/jQuery_interop.php'; 
            $j('#flag').val(valore);
            $j('#client_secret').val();
            $j.post(path_php, { 'Calendar': cal[1],  'Richiesta': 7 }, function(data){
                $j('#alert').html(data);
            });
	return false;
    });

    /*
    Funzione che si attiva quando l'utente inserisce un nuovo evento
    */
    $j('#Btn_enter_event').click(function($)    {	
        var Ins_name = $j('#Ins_txt_name_event').val(); 
	var Ins_luogo = $j('#Ins_txt_locaz_event').val();
	var Ins_note = $j('#Ins_Note').val();
	var Ins_Tipo = $j('#Ins_Tipologia option:selected').text();
	var Ins_Durata = $j('#Ins_Durata option:selected').text();
        var date = ($j('#hidd_date').val()).split("-");
	var Ins_Year_Date = date[0]; 
	var Ins_Month_Date = date[1];
	var Ins_Day_Date = date[2];
	var Ins_Hour_Date = $j('#Ins_Hour_time option:selected').val();
	var Ins_Minutes_Date = $j('#Ins_Minutes_time option:selected').text();
	var path = $j('#path').val(); 
	var path_php = path + '/jQuery_interop.php';  	
	$j('#result').html('');
	$j('#alert').html('<p>Inserimento in corso.. 30% completato</p>');
        
	if(isValid(Ins_Day_Date + "/" + Ins_Month_Date + "/" + Ins_Year_Date))	{			
            if(Check_input(Ins_name,Ins_luogo)) {	
                $j('#alert').html('<p>Inserimento in corso.. 60% completato</p>');
		var Ins_Data_inizio = new Date(Ins_Year_Date,Ins_Month_Date-1,Ins_Day_Date,Ins_Hour_Date,Ins_Minutes_Date,'00','00');
		if(Ins_Data_inizio > Date.now())    {
                    $j('#alert').html('<p>Inserimento in corso.. 90% completato</p>');			
                    $j.post(path_php, {  'Richiesta': 1,'tipologia':  Ins_Tipo ,'Luogo': Ins_luogo,'Note': Ins_note,
                        'Nome': Ins_name, 'giorno_data_inizio': Ins_Day_Date,'mese_data_inizio': Ins_Month_Date,'anno_data_inizio': Ins_Year_Date,
                        'ora_ora_inzio' : Ins_Hour_Date , 'minuti_ora_inizio': Ins_Minutes_Date,'durata': Ins_Durata }, function(data){
                            $j('#alert').html('');
                            $j('#alert').append(data);		
                    });
		}   else    {
                    $j('#alert').html("<p class='Alert_orange'>Non si può inserire un evento con una data precedente di quella attuale</p>");		 
                }
            }   else    {
                $j('#alert').html("<p class='Alert_orange'>Campo nome o campo luogo non compilato correttamente");	 
            }		
        }   else    {
            //Se la data di inizio non è valida
            $j('#alert').html("<p class='Alert_orange'>Data inserita non valida</p>");		  	
	}
    });
	
    /*
    Funzione che si attiva quando l'utente cambia il valore della durata di un evento.
    */
    $j('#Ins_Durata').change(function($) {
        var Ins_Durata = $j('#Ins_Durata option:selected').text();
        if(Ins_Durata > 1)  {
            $j('#Ins_Tipo_durata').text('Ore');   
        }   else    {
            $j('#Ins_Tipo_durata').text('Ora');
        }
    });
    
});