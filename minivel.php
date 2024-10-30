<?php
/*
 Plugin Name: Minivel
 Plugin URI: http://blog.biblioeteca.com/widgets-plugins-y-demas/plugin-minivel-para-wordpress/
 Description: Muestra el nivel alcanzado mediante trofeos por un usuario biblioEteca
 Author: José Antonio Espinosa
 Version: 1.0
 Author URI: http://www.biblioeteca.com/
 */


error_reporting(E_ALL);
add_action("widgets_init", array('Minivel', 'register'));
register_activation_hook( __FILE__, array('Minivel', 'activate'));
register_deactivation_hook( __FILE__, array('Minivel', 'deactivate'));

class Minivel {


	function activate(){
		$data = array( 'minivel_Usuario' => 'usuarioBiblioEteca','minivel_Titulo' => 'Mi nivel en Biblioeteca');
		if ( ! get_option('minivel_configuracion')){
			add_option('minivel_configuracion' , $data);
		} else {
			update_option('minivel_configuracion' , $data);
		}
	}
	function deactivate(){
		delete_option('minivel_configuracion');
	}

	function control(){
		$data = get_option('minivel_configuracion');
		?>

<p><label>Usuario biblioEteca<input name="minivel_configuracion_option1"
	type="text" value="<?php echo $data['minivel_Usuario']; ?>" /></label></p>
<p><label>Titulo/Comentario<input name="minivel_configuracion_option2"
	type="text" value="<?php echo $data['minivel_Titulo']; ?>" /></label></p>


		<?php
		if (isset($_POST['minivel_configuracion_option1'])){
			$data['minivel_Usuario'] = attribute_escape($_POST['minivel_configuracion_option1']);
			$data['minivel_Titulo'] = attribute_escape($_POST['minivel_configuracion_option2']);

			update_option('minivel_configuracion', $data);
		}
	}



	function widget($args){
		extract($args);

		$opciones     = get_option( "minivel_configuracion" );

		//configurable
		$userBiblioeteca=$opciones['minivel_Usuario'];
		//configurable
		$server="http://www.biblioeteca.com";
		//configurable
		$comment=$opciones['minivel_Titulo'];

		$existe=false;
		$url='/biblioeteca.web/widgets/minivel/';

		$fp=@fopen($server.$url.$userBiblioeteca,"r");
		if($fp){
			//Acciones a realizar si existe
			$existe= true;
		}else{
			//Acciones a realizar en caso de que no exista
			$existe= false;
			@fclose($fp);
		}

		$noleo=false;
		if ($existe){

			$source = '';
			while (!@feof($fp)) {
				$source .= @fread($fp, 8192);
			}
			@fclose($handle);
			echo $before_widget;
			echo $before_title.$comment.$after_title;
			if (strpos($source,"Ooooops!")>0)
			{
				$noleo=true;
				
			}
			else {
				echo $source;
        	}
		}

		if (!$existe)
		{

			echo "<br/>";
			echo "Problemas de conectividad con BiblioEteca";
			echo "<br/>";
		}
		
		if ($noleo)
		{
		
			echo "<br/>";
			echo "Error, usuario no configurado";
			echo "<br/>";
		}

		echo $after_widget;
	}

	function register(){
		wp_register_sidebar_widget("Minivel","Mi nivel - Minivel", array('Minivel', 'widget'));
		wp_register_widget_control("Minivel","Mi nivel - Minivel", array('Minivel', 'control'));
	}
}
?>
