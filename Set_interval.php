<?php

require_once 'Load_Write.php';
/*
Funzione che determina il form di impostazione dell'intervallo.
*/
function Set_interval_form()
{
    setlocale(LC_ALL, "it_IT.utf8");
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script("jquery-ui-datepicker");
    wp_enqueue_style('jquery.ui.theme', plugins_url(null, __FILE__).'/smoothness/jquery-ui-1.10.0.custom.min.css');
  
	$dati = Load_interval_date(); 
	$alert = "";  
	$data_inizio=""; 
	$data_fine="";  
	$flag = false; 
	$today = new DateTime("now");
	if($dati[0] != "" && $dati[1] != "")	{
		$data_fine = explode("-",$dati[1]);
		$data_inizio = explode("-",$dati[0]);	
		$flag = true; 
		$alert = "<p>Date già impostati in precedenza!</p>";
	} 
	else    {
          $alert = "<p class='Alert_orange'>Date non ancora impostati</p>";
	  $flag = false;	
		
	}
	
	if($flag)   {
		$d_start = new DateTime($data_inizio[0] ."-" . $data_inizio[1] . "-" . $data_inizio[2]);
		$d_end = new DateTime($data_fine[0] ."-" . $data_fine[1] . "-" . $data_fine[2]);
	} else {
            if ($today->format('m')>'4'){
                $d_start= new DateTime($today->format('Y')."-6-1");
                $d_end= new DateTime(($today->format('Y')+1)."-4-30");
                }
            else{
                $d_start= new DateTime(($today->format('Y')-1)."-6-1");
                $d_end= new DateTime($today->format('Y')."-4-30");
            }
        }
	
?>

    <h1>Intervallo di visualizzazione degli appelli</h1>
    <p><hr size=1 noshade></p>
    <div id="alert" ><?php echo $alert; ?></div>
    <form action="" METHOD="POST">
        <table class="form-table">
            <tr>
                <td><label>Inserisci la data di inizio:</label></td>
                <td>
                    <input type="hidden" value="<?php echo $d_start->format("Y-m-d"); ?>" id="hidd_date_start" />
                    <input type="text"  readonly="readonly" value="<?php echo  strftime("%e %B %G",  strtotime($d_start->format("n/j/Y"))); ?>" id="date_start" />
                </td>
            </tr>
            <tr>
                <td><label>Inserisci la data di fine:</label></td>
                <td>
                    <input type="hidden" value="<?php echo $d_end->format("Y-m-d"); ?>" id="hidd_date_end" />
                    <input type="text" readonly="readonly" value="<?php echo strftime("%e %B %G",strtotime($d_end->format("n/j/Y"))); ?>" id="date_end" />
                </td>
            </tr>
            <tr>
                <td><input type="button" value="Salva date" id="btn_set_interval"/></td>
                <td>
                    
                    <input type="hidden" value="0" id="flag" />
                    <input type="hidden" value="<?php  echo plugins_url(null, __FILE__) ?>" id="path"  />
                    <input type="hidden" value=" <?php echo plugin_dir_path(__FILE__); ?>  " id="path2" />
                </td>
            </tr>
        </table>
    </form>
    <p><hr size=1 noshade></p>
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
        $j('#date_start').datepicker({
            onSelect: function(date) {
                $j( '#date_start' ).datepicker( "option", "defaultDate", date );
                var day = $j.datepicker.formatDate('d', new Date(date));
                var month = mesi[$j.datepicker.formatDate('m', new Date(date))-1];
                var year = $j.datepicker.formatDate('yy', new Date(date))
                $j('#date_start').val(day+" "+month+" "+year);
                $j('#hidd_date_start').val(date);
            },
            dateFormat : 'yy-mm-dd',
            showOn: "button",
            buttonImage: "<?php echo plugins_url(null, __FILE__); ?>/smoothness/images/calendar.gif",
            buttonImageOnly: true,
            defaultDate: $j('#hidd_date_start').val()
	 });
         
        $j('#date_end').datepicker({
           onSelect: function(date) {
               $j( '#date_end' ).datepicker( "option", "defaultDate", date );
                var day = $j.datepicker.formatDate('d', new Date(date));
                var month = mesi[$j.datepicker.formatDate('m', new Date(date))-1];
                var year = $j.datepicker.formatDate('yy', new Date(date))
                $j('#date_end').val(day+" "+month+" "+year);
                $j('#hidd_date_end').val(date);
           },
	   dateFormat : 'yy-mm-dd',
           showOn: "button",
           buttonImage: "<?php echo plugins_url(null, __FILE__); ?>/smoothness/images/calendar.gif",
           buttonImageOnly: true,
           defaultDate: $j('#hidd_date_end').val()
        }); 
    });
</script>

<?php } ?>

