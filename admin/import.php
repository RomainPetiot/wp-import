<?php
set_time_limit(0);
/**
 * import des données
 */

class CSVImport
{
	/**
	* Constructor
	*/
	public function __construct()
    {
    	$this->importProduct();
    }

    public function importProduct()
	{
		global $wpdb;
		$ligneTraite = 0;
		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'adherent',
		);
		$myposts = get_posts( $args );
		foreach ( $myposts as $post ) : setup_postdata( $post );
			wp_delete_post( $post->ID, true );
		endforeach;
		wp_reset_postdata();

		// Connexion et sélection de la base
		global $con;
		$con = mysqli_connect('localhost', 'romainpetiot_fivape', 'fivapefivape', 'romainpetiot_fivape')
		    or die('Impossible de se connecter : ' . mysql_error());
		$query = mysqli_query($con, "SELECT ID, post_title FROM wp_posts WHERE wp_posts.post_type = 'membre' AND post_status = 'publish'") or die(mysqli_error($con));

		while ($row = $query->fetch_assoc()) {

			$activite = array();
			$val_activity = search($row['ID'], "baseline_membre");
			if($val_activity == "Vape Shop" || $val_activity == "Vape shop et web" || $val_activity == "https://www.vapostore.com/"){
				$activite[] = 13;
			}
			elseif($val_activity == "Fabricant de e-liquides"){
				$activite[] = 20;
			}
			elseif($val_activity == "Webstore" || $val_activity == "Vape shop et web" || $val_activity == "Grossiste & Webstore"){
				$activite[] = 14;
			}
			elseif($val_activity == "Consulting"){
				$activite[] = 25;
			}
			elseif($val_activity == "Fabricant de e-liquides / Prestataire de services ..." || $val_activity == "Grossiste et fabricant de e-liquides"){
				$activite[] = 20;
			}
			elseif($val_activity == "Fabricant de matériels" || $val_activity == "Concepteur de distributeurs automatiques et fabric..."){
				$activite[] = 22;
			}
			elseif($val_activity == "Fabricant matière première et bases"){
				$activite[] = 21;
			}
			elseif($val_activity == "Grossiste" || $val_activity == "Grossiste et fabricant de e-liquides" || $val_activity == "Grossiste & Webstore" || $val_activity == "Grossiste e-liquides"){
				$activite[] = 15;
			}
			elseif($val_activity == "Presse spécialisée" || $val_activity == "Info"){
				$activite[] = 26;
			}
			elseif($val_activity == "Prestataire de services production"){
				$activite[] = 23;
			}
			echo $val_activity.' => '.$activite[0].'<br />';
			$post = array();
			$post['post_type']   = 'adherent';
			$post['post_status'] = 'publish';
			$post['post_title'] = sanitize_text_field($row['post_title']);
			/*$acf_ref = $post['acf_ref'];
			$showroom = $post['showroom'];
			$tab_img_secondaire = $post['img_secondaire'];*/

			$p = wp_insert_post( $post, true );
			if ( is_wp_error( $p ) ) {
				echo sanitize_text_field($row['post_title']).' : '.$p->get_error_message().'<br />';
			}
			else{
				$t = wp_set_object_terms( $p, $activite, 'activity', true );
				if ( is_wp_error( $t ) ) {
					var_dump($activite);
					echo $val_activity.' => '.$t->get_error_message().'<br />';
				}
				update_field("field_5a9d055546a10", "<p><b>".$row['post_title']."</b></p><p>".search($row['ID'], "website_membre")."</p>", $p);

				$field_name = "field_5a9d056946a11";
				$adresse = search($row['ID'], "adresse_siege_membre");
				if(!empty($adresse)){
					$gps = unserialize(search($row['ID'], "gps_siege_membre"));
					$value = array("address" => $adresse, "lat" => $gps["lat"], "lng" => $gps["long"], "zoom" => 15);
					update_field($field_name, $value, $p);
				}
				$ligneTraite++;
			}

		}

		echo '<p>
		'.$ligneTraite.' lignes traités
		</p>';
	}
}

function search($id, $s){
  global $con;
  $query = mysqli_query($con, "SELECT meta_value FROM wp_postmeta WHERE post_id = ".$id." AND meta_key='".$s."'");
  $row=mysqli_fetch_array($query,MYSQLI_NUM);
  return sanitize_text_field($row[0]);
}
