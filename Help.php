<h1>Help di CalendApp - ver 1.2</h1>
 <p><hr size=1 noshade></p>
<div class="wrap">
    <ul>
        <li>
            <h3>Primo utilizzo:</h3>
            <p style='text-align:justify;'>
                Cliccare su <b><a href="admin.php?page=CalendApp">Impostazioni</a></b>, indicare Client ID, Client secret e dopo essersi autenticati caricare la lista dei calendari e sselezionare il calendario da utilizzare.<br />
                E' consigliato utilizzare un calendario dedicato a questo scopo, altrimenti verranno visualizzati 
                anche gli altri eventi presenti nel calendario ma non riguardanti gli appelli.
                <br />
                <br />
                Qualora si possedesse il plugin "Wp supercache" occorre disabilitarlo perchè potrebbe dare problemi nel salvataggio delle impostazioni delle credenziali.
            </p>
        </li>
        <br />
        <li>
            <h3>Pubblicazione del calendario degli appelli:</h3>
            <p style='text-align:justify;'>Per pubblicare il calendario degli appello in un post è necessario autenticarsi una sola volta (da backend) ed inserire una keyword che sarà opportunamente sostituita 
                da CalendApp con il relativo codice. 
                La keyword da utilizzare è<p style='text-align:center;'><b>[--EXAMS_TABLE=ALL--]</b></p>La parola <b>ALL</b> indica che verranno pubblicati gli appelli di tutte le discipline.
                Per Pubblicare gli appelli di una sola disciplina sarà sufficiente indicare il relativo nome. Ad esempio, se si vuol pubblicare
                il calendario degli appelli di <b>Laboratorio di Informatica</b> basterà scrivere in un post <p style='text-align:center;'><b>[--EXAMS_TABLE=Laboratorio di Informatica--]</b></p>
                <br />
                <br />
                N.B.: La stringa del nome della disciplina da inserire nella keyword è case-insensitive.
            <!--</p>-->
        </li>
        <br />
        <li><h3>Accorgimenti:</h3>
            <p style='text-align:justify;'>1) E' possibile anche inserire un evento direttamente da Google Calendar e immediatamente questo verrà visualizzato tra gli eventi 
                di CalendApp. Per fare ciò è necessario inserire il titolo nella forma <b>TIPOLOGIA - NOME-DISCIPLINA</b>. 
                Ad esempio per inserire l'appello scritto di sistemi per la collaborazione basterà indicare nel titolo dell'evento: <b>Scritto - Sistemi per la collaborazione</b> 
                facendo attenzione a lasciare uno spazio a destra e a sinistra del trattino.<br /><br />
                2) Durante la creazione di un calendario in Google Calendar il nome non deve contenere le stringhe " - " (ovvero spazio a destra e a sinistra del trattino) e "    " (ovvero i 4 spazi consecutivi). <br /><br />
                3) Durante l' inserimento o la modifica dei valori di nome, luogo e descrizione di un evento occorre evitare di inserire la seguente stringa "||".
                </p>
        </li>
        <br />
        
        <li><h3>Credits:</h3>
            <ul>
                <li>Zend GCal v1.0 per Drupal creato da Floriano Fauzzi</li>
                <li>Zend GCal v1.0 per Wordpress creato da Maria Antonietta Fanelli</li>
                <li>Zend Gcal v1.1 (rinominato CalendApp) per Wordpress creato da Giovanni Marzulli</li>
                <li>CalendApp v1.2 per Wordpress creato da Francesco Altamura</li>
            </ul>
        </li>
    </ul>
</div>
<?php

?>
