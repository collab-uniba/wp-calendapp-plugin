<?php

/*
 * Funzione che permette la pubblicazione della tabella degli appelli nei post e pagine del front-end inserendo una parola chiave
 */
function pub_cal($content){
 $pattern='/(\[--EXAMS_TABLE=)+(.)+(--\])+/';
    $matches = array();
    preg_match( $pattern, $content,$matches);
    $exam=substr($matches[0],15,-3);
    if (preg_match( $pattern, $content)) {
        $html = "<div id=\"alert\"></div>
                <input type='hidden' id='idpost'value='".get_the_ID()."' />
                <div id=\"calendar-content-".get_the_ID()."\"></div>
                <script type=\"text/javascript\">
                    var path_http = '".plugins_url(null, __FILE__)."';
                    var path_php = path_http + '/jQuery_interop.php'; 
                    var title = '".$exam."';
                    \$j('#alert').html('<p>Caricamento degli eventi in corso..</p>');
                    \$j.post(path_php, { 'Richiesta': 4, 'examTitle':title, 'backend':0 }, function(data){
                        \$j('#alert').html('');
			\$j('#calendar-content-".get_the_ID()."').append(data); 
                    });
            </script>";
        $returnContent = str_replace($matches[0], $html, $content);
        return $returnContent;
    }
    return $content;
   
}
?>
