<?php

/*
Funzione che determina il form di inserimento degli eventi
*/

function Insert_event_form()
{  
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script("jquery-ui-datepicker");
    wp_enqueue_style('jquery.ui.theme', plugins_url(null, __FILE__).'/smoothness/jquery-ui-1.10.0.custom.min.css');
  
    if(isset($_GET["Id_Gcal"]))
        echo "<h1>Modifica appello</h1>";
    else
        echo "<h1>Definisci un nuovo appello</h1>";

?>
<p><hr size=1 noshade></p>  
 
<input type="hidden" value="<?php echo plugin_dir_path(__FILE__); ?>" id="path2" />
<input type="hidden" value="<?php echo plugins_url(null,__FILE__); ?>" id="path" />
  
<div id="alert"></div>
<div id="content"></div>

    <?php if(isset($_GET["Id_Gcal"])) {  
           echo "<script type='text/javascript'>
                    var id ='".$_GET["Id_Gcal"]."';
                    compila_form_modifica(id);
                  </script> ";
           }
    ?>
      
    <form action="" METHOD="POST" >
        <table class="form-table">
            <tr>
                <td><label for="nome_evento">Inserisci il nome del corso:</label></td>
                <td><input type="text" name="nome_evento" size=25 id="Ins_txt_name_event" /> </td>
            </tr>
            <tr>
                <td><label for="locaz_evento">Inserisci dove si svolgerà l'esame:</label></td>
                <td><input type="text" name="locaz_evento" size="25" id="Ins_txt_locaz_event" /></td>
            </tr>
            <tr>
                <td><label>Inserisci la data dell'esame:</label></td>
                <td>
                    <input type="hidden" value="" id="hidd_date" />
                    <input type="text"  readonly="readonly" value="" id="date" />
                </td>
            </tr>
            <tr>
                <td><label>Inserisci l'ora di inizio dell'esame:</label></td>
                <td>
                    <select id="Ins_Hour_time">
                    <?php for($i=8;$i<=20;$i++) {
                            $selected="";
                            $h=$i;
                            if($i<10)
                                $h="0".$i;
                            if($i==10)
                                $selected=" selected='selected' ";
                            echo "<option value='$h' $selected>". $h  ."</option>";
                            } ?>   	
                    </select>	
                    <select id="Ins_Minutes_time">
                   <?php  for($i=0;$i<=60;$i++)  {
				if($i<10)
                                    echo "<option value='0$i'>0".  $i."</option>"; 
				else
                                    echo "<option value='$i'>".  $i."</option>"; 
			}  ?> 	
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Inserisci la durata dell'esame (hh):</label></td>
                <td>
                    <select id="Ins_Durata">
                    	<?php  for($i=1;$i<=10;$i++)
                                    echo "<option value='$i'>" . $i . "</option>"; 	
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Scegli la tipologia dell'esame:</label></td>
                <td>
                    <select id="Ins_Tipologia">
                    	<option value="Lab">Laboratorio</option>
                        <option value="Scritto">Scritto</option>
                        <option value="Orale">Orale</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Note:</label></td>
                <td><textarea id="Ins_Note" cols="30" rows="5"> </textarea></td>
            </tr>
            <tr>
                <td><input type="button" value="Salva" align="middle" id="<?php if (isset($_GET["Id_Gcal"])) echo "Btn_modify_event"; else echo "Btn_enter_event"; ?>"/></td>
                <td><input type="hidden" value="<?php if (isset($_GET["Id_Gcal"])) echo $_GET["Id_Gcal"]; ?>" id="Id_evento" /></td>
            </tr>
        </table>	          
    </form>
<script type="text/javascript">
    $j(document).ready(function(){
        var mesi = ['gennaio','febbraio','marzo','aprile','maggio','giugno','luglio','agosto','settembre','ottobre','novembre','dicembre'];
        $j.datepicker.regional['it'] = {
		monthNames: mesi,
		monthNamesShort: ['Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'],
		dayNames: ['Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato'],
		dayNamesShort: ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'],
		dayNamesMin: ['Do','Lu','Ma','Me','Gi','Ve','Sa'],
		firstDay: 1,
		renderer: $j.datepicker.defaultRenderer,
		prevText: '&#x3c;Prec', prevStatus: 'Mese precedente',
		prevJumpText: '&#x3c;&#x3c;', prevJumpStatus: 'Mostra l\'anno precedente',
		nextText: 'Succ&#x3e;', nextStatus: 'Mese successivo',
		nextJumpText: '&#x3e;&#x3e;', nextJumpStatus: 'Mostra l\'anno successivo',
		currentText: 'Oggi', currentStatus: 'Mese corrente',
		todayText: 'Oggi', todayStatus: 'Mese corrente',
		clearText: 'Svuota', clearStatus: 'Annulla',
		closeText: 'Chiudi', closeStatus: 'Chiudere senza modificare',
		yearStatus: 'Seleziona un altro anno', monthStatus: 'Seleziona un altro mese',
		weekText: 'Sm', weekStatus: 'Settimana dell\'anno',
		dayStatus: '\'Seleziona\' D, M d', defaultStatus: 'Scegliere una data',
		isRTL: false
	};
        $j.datepicker.setDefaults($j.datepicker.regional['it']);
        $j('#date').datepicker({
            onSelect: function(date) {
                $j( '#date' ).datepicker( "option", "defaultDate", date );
                var day = $j.datepicker.formatDate('d', new Date(date));
                var month = mesi[$j.datepicker.formatDate('m', new Date(date))-1];
                var year = $j.datepicker.formatDate('yy', new Date(date))
                $j('#date').val(day+" "+month+" "+year);
                $j('#hidd_date').val(date)
            },
            dateFormat : 'yy-mm-dd',
            showOn: "button",
            buttonImage: "<?php echo plugins_url(null, __FILE__); ?>/smoothness/images/calendar.gif",
            buttonImageOnly: true,
            defaultDate: $j('#hidd_date').val()
	 });              
     });
</script>

<?php   }    ?>