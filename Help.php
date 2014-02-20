<h1>Help di CalendApp - ver 1.1</h1>
 <p><hr size=1 noshade></p>
<div class="wrap">
    <ul>
        <li>
            <h3>Primo utilizzo:</h3>
            <p style='text-align:justify;'>
                Cliccare su <b><a href="admin.php?page=Zend_gcal">Impostazioni</a></b> e indicare le credenziali di accesso a Google Calendar e il nome del calendario da utilizzare.<br />
                E' consigliato utilizzare un calendario dedicato a questo scopo, altrimenti verranno visualizzati 
                anche gli altri eventi presenti nel calendario ma non riguardanti gli appelli.
            </p>
        </li>
        <br />
        <li>
            <h3>Pubblicazione del calendario degli appelli:</h3>
            <p style='text-align:justify;'>Per pubblicare il calendario degli in un post è sufficiente inserire una keyword che sarà opportunamente sostituita 
                dal Zend_GCal con il relativo codice. 
                La keyword da utilizzare è<p style='text-align:center;'><b>[--EXAMS_TABLE=ALL--]</b></p>La parola <b>ALL</b> indica che verranno pubblicati gli appelli di tutte le discipline.
                Per Pubblicare gli appelli di una sola disciplina sarà sufficiente indicare il relativo nome. Ad esempio, se si vuol pubblicare
                il calendario degli appelli di <b>Laboratorio di Informatica</b> basterà scrivere in un post <p style='text-align:center;'><b>[--EXAMS_TABLE=Laboratorio di Informatica--]</b></p>
                <br />
                <br />
                N.B.: La stringa del nome della disciplina da inserire nella keyword è case-insensitive.
            </p>
        </li>
        <br />
        <li><h3>Inserimento di un evento direttamente da Google Calendar:</h3>
            <p style='text-align:justify;'>E' possibile anche inserire un evento direttamente da Google Calendar e immediatamente questo verrà visualizzato tra gli eventi 
                di Zend_GCal. Per fare ciò è necessario un piccolo accorgimento: inserire il titolo nella forma <b>TIPOLOGIA - NOME-DISCIPLINA</b>. 
                Ad esempio per inserire l'appello scritto di sistemi per la collaborazione basterà indicare nel titolo dell'evento: <b>Scritto - Sistemi per la collaborazione</b> 
                facendo attenzione a lasciare uno spazio a destra e a sinistra del trattino.</p>
        </li>
        <li><h3>Credits:</h3>
            <ul>
                <li>Zend GCal v1.0 per Drupal creato da Floriano Fauzzi</li>
                <li>Zend GCal v1.0 per Wordpress creato da Maria Antonietta Fanelli</li>
                <li>Zend Gcal v1.1 (rinominato CalendApp) per Wordpress creato da Giovanni Marzulli</li>
            </ul>
        </li>
    </ul>
</div>
<?php

?>
