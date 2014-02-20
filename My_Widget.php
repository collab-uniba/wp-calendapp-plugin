<?php
class My_Widget extends WP_Widget {
    public $name = 'Widget calendario appelli';
    public $description = 'Widget creato per la visualizzazione degli eventi da parte di altri utenti';
    public $control_options = array();
	
    function My_Widget() {
        // widget actual processes
	$widget_options = array(
            'classname' => __CLASS__,
            'description' => $this->description,
	);
        parent::__construct( __CLASS__, $this->name,$widget_options,$this->control_options);
    }

    function form($instance) {
        // outputs the options form on admin
        echo admin_url(); 	
    }

    function update($new_instance, $old_instance) {
        // processes widget options to be saved
    }

    function widget($args, $instance) {
        // outputs the content of the widget
        echo "<p>Calendario appelli</p>";
	echo "<ul>"; 
	echo "<li><a href='". admin_url() ."admin.php?page=Vis_event'>Visualizza calendario appelli</a></li>"; 
	echo "</ul>";
    }
}

?>