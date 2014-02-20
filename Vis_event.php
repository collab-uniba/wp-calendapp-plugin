<?php

/*
Funzione che determina la visualizzazione degli eventi 
*/
function Vis_event_form()   {   ?>

<h1>Calendario appelli di Google Calendar</h1>
<p><hr size=1 noshade></p>
<input type="hidden" value="<?php echo WP_PLUGIN_DIR; ?>" id="path2" />
<div id="alert"></div>
<div id="content"></div>
<p><hr size=1 noshade></p>
<script type="text/javascript">		
    var path_http = '<?php echo plugins_url(null, __FILE__) ?>';
    var path_php = path_http + '/jQuery_interop.php'; 
    var title = 'all';
    $j('#alert').html('<p>Caricamento degli eventi in corso..</p>');
    $j.post(path_php, { 'Richiesta': 4, 'examTitle':title, 'backend':1 }, function(data){
        $j('#alert').html('');
	$j('#content').append(data);		 
    });	
</script>

<?php   }   ?>