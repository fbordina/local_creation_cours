<?php


require_once("$CFG->libdir/formslib.php");

class simplehtml_form extends moodleform {
	//Add elements to form
	public function definition() {
		global $CFG;
		global $USER;
		
		$mform = $this->_form; // Don't forget the underscore! 
		$mform->setRequiredNote('* = champs obligatoires');
		$mform->setJsWarnings('Erreur de saisie ','Veuillez corriger');

        $mform->addElement('html', "<b>Cet espace vous permet de cr&eacute;er un espace de cours en ligne sur la plateforme p&eacute;dagogique Un&icirc;mes. Les cours propos&eacute;s sont rattach&eacute;s aux maquettes universitaires valid&eacute;es.</b><br/><br/>
Vous pouvez au choix :<br/>
A.      Cr&eacute;er un espace de cours vide<br/>
B.      Cr&eacute;er un espace de cours en r&eacute;cup&eacute;rant les ressources et activit&eacute;s que vous aviez dans votre espace de cours de l'an dernier<br/><br/>
<table border=1><tr style='font-weight: bold; text-align: center'>
        <td>Cr&eacute;er un espace de cours vide</td><td>R&eacute;cup&eacute;ration des contenus d'un cours de l'an dernier</td>
    </tr><tr>
        <td>1.  Vous devez identifier le cours que vous souhaitez cr&eacute;er via les 4 listes d&eacute;roulantes de la partie \" Cr&eacute;ation d'un cours vide pour l'ann&eacute;e 2017-2018 \"<br/>
        2.      Cliquez sur \" Enregistrer \" en bas de la page.
        </td>
        <td>
        1.      Vous devez identifier le cours dans la maquette 2017-2018 via les 4 listes d&eacute;roulantes de la partie \" Cr&eacute;ation d'un cours vide pour l'ann&eacute;e 2017-2018 \"<br/>
        2.      S&eacute;lectionnez le cours dont vous souhaitez r&eacute;cup&eacute;rer le contenu dans la liste d&eacute;roulante de la partie \" R&eacute;cup&eacute;ration d'un cours de l'an dernier \"<br/>
        3.      Cliquez sur \" Enregistrer \" en bas de la page.
        </td></tr></table>
");

		// Premi�re partie : cr&eacute;ation d'un cours
		
		$mform->addElement('header', 'destination', 'Cr&eacute;ation d\'un cours vide pour l\'ann&eacute;e 2017-2018');
		$mform->addElement('html', 'S&eacute;lectionnez votre cours en utilisant obligatoirement les 4 listes d&eacute;roulantes.<br/><br/>');

		// On stocke les cours deja crees
		$db = mysqli_connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass) or die("Cannot connect to database engine!");
		mysqli_select_db($db, $CFG->dbname) or die("Cannot connect to database $CFG->dbname!");

		mysqli_query ($db, "set names utf8");
		$sql = "SELECT c.idnumber, concat(u.firstname,' ', u.lastname) enseignant FROM mdl_user u, mdl_role_assignments r, mdl_context cx, mdl_course c  WHERE c.idnumber is not null AND u.id = r.userid  AND r.contextid = cx.id  AND cx.instanceid = c.id"; //  AND r.component = 'enrol_flatfile'";
//		$sql = "SELECT idnumber FROM mdl_course";
		$result = mysqli_query($db, $sql);
		$courscrees = array();
		while($row = mysqli_fetch_array($result)) {
//print_r($row);
			$courscrees[$row['idnumber']] = $row['enseignant']; 
		}
		mysqli_close($db);
		
		if(!apc_exists('niveaux1') || !apc_exists('niveaux2') || !apc_exists('niveaux3') || !apc_exists('niveaux4') ){
			$connect = ocilogon($CFG->si_user,$CFG->si_pass,$CFG->si_url_base);
		}
		
		// Le niveau 1 
		if(!apc_exists('niveaux1')){
			$req = "select * from mdl_niveau1";
			$stmt = ociparse($connect,$req);
			ociexecute($stmt,OCI_DEFAULT);
			$niveaux1 = array();
			while (($row = oci_fetch_array($stmt, OCI_BOTH)) != false) {
				$niveaux1[] = $row;
			}
			apc_store('niveaux1', $niveaux1);
		}
		$niveaux1_cache = apc_fetch('niveaux1');
			
		$select_niveau1 = $mform->createElement( 'select', 'niveau1', 'Niveau 1 :', null, array('onchange' => 'setTextField(this,\'tniveau1\');'));
		$select_niveau1->addOption( 'Domaines / DU / UE d\'ouverture', '', array( 'disabled' => 'disabled', 'selected'=>'true' ) );
		
		foreach ($niveaux1_cache as $row) {
			$select_niveau1->addOption($row[1],$row[0]);
		}
		$mform->addElement($select_niveau1);
		$mform->addRule('niveau1', 'Vous devez saisir une ligne dans "Domaines / DU / UE d\'ouverture"', 'required', '', 'client');
		$mform->addElement('hidden', 'tniveau1', '',array('id'=>'tniveau1'));

		// Le niveau 2
		if(!apc_exists('niveaux2')){
			$req = "select * from mdl_niveau2";
			$stmt = ociparse($connect,$req);
			ociexecute($stmt,OCI_DEFAULT);
			$niveaux2 = array();
			while (($row = oci_fetch_array($stmt, OCI_BOTH)) != false) {
				$niveaux2[] = $row;
			}
			apc_store('niveaux2', $niveaux2);
		}
		$niveaux2_cache = apc_fetch('niveaux2');
				
		$select_niveau2 = $mform->createElement( 'select', 'niveau2', 'Niveau 2 :', null, array('onchange' => 'setTextField(this,\'tniveau2\');'));
		$select_niveau2->addOption( 'Dipl&ocirc;me / mention', '', array( 'disabled' => 'disabled', 'selected'=>'true' ) );
		
		foreach ($niveaux2_cache as $row) {
			$select_niveau2->addOption($row[1],$row[0],array('class'=>$row[2]));
		}
		$mform->addElement($select_niveau2);
		$mform->addRule('niveau2', 'Vous devez saisir une ligne dans "Diplome / mention"', 'required', '', 'client');
		$mform->addElement('hidden', 'tniveau2', '',array('id'=>'tniveau2'));
		
		// Le niveau 3
		if(!apc_exists('niveaux3')){
			$req = "select * from mdl_niveau3 where code in (select distinct id || '' from mdl_niveau4) or CODE like 'UEO%'";
			$stmt = ociparse($connect,$req);
			ociexecute($stmt,OCI_DEFAULT);
			$niveaux3 = array();
			while (($row = oci_fetch_array($stmt, OCI_BOTH)) != false) {
				$niveaux3[] = $row;
			}
			apc_store('niveaux3', $niveaux3);
		}
		$niveaux3_cache = apc_fetch('niveaux3');
		
		$select_niveau3 = $mform->createElement( 'select', 'niveau3', 'Niveau 3 :', null, array('onchange' => 'setTextField(this,\'tniveau3\');'));
		$select_niveau3->addOption( 'Semestre / Parcours', '', array( 'disabled' => 'disabled', 'selected'=>'true' ) );
		foreach ($niveaux3_cache as $row) {
			$select_niveau3->addOption($row[1],$row[0],array('class'=>$row[2]));
		}
		$mform->addElement($select_niveau3);
		$mform->addRule('niveau3', 'Vous devez saisir une ligne dans "Semestre / Parcours"', 'required', '', 'client');
		$mform->addElement('hidden', 'tniveau3', '',array('id'=>'tniveau3'));
		
		// Le niveau 4
		if(!apc_exists('niveaux4')){
			$req = "select * from mdl_niveau4";
			$stmt = ociparse($connect,$req);
			ociexecute($stmt,OCI_DEFAULT);
			$niveaux4 = array();
			while (($row = oci_fetch_array($stmt, OCI_BOTH)) != false) {
				$niveaux4[] = $row;
			}
			apc_store('niveaux4', $niveaux4);
		}
		$niveaux4_cache = apc_fetch('niveaux4');

		$select_niveau4 = $mform->createElement( 'select', 'niveau4', 'Niveau 4 :', null, array('onchange' => 'setTextField(this,\'tniveau4\');'));
		$select_niveau4->addOption( 'Cours', '', array( 'disabled' => 'disabled', 'selected'=>'true' ) );
		foreach ($niveaux4_cache as $row) {
			if (in_array($row[0],array_keys($courscrees)))
				$select_niveau4->addOption($row[1] . ' par ' . $courscrees[$row[0]],$row[0],array('disabled' => 'disabled', 'class'=>$row[2]));
			else $select_niveau4->addOption($row[1],$row[0],array('class'=>$row[2]));
		}
		
		$mform->addElement($select_niveau4);
		$mform->addRule('niveau4', 'Vous devez saisir une ligne dans "Cours"', 'required', '', 'client');
		$mform->addElement('hidden', 'tniveau4', '',array('id'=>'tniveau4'));
		
		// Seconde partie : Restauration de cours 
		$mform->addElement('header', 'source', 'R&eacute;cup&eacute;ration d\'un cours de l\'an dernier');
		$mform->closeHeaderBefore('source');
		$mform->addElement('html', 'Choisir dans la liste d&eacute;roulante ci-dessous le cours de l\'ancienne plateforme dont vous souhaitez r&eacute;cup&eacute;rer le contenu.<br/><br/>');

		$db = mysqli_connect($CFG->old_mysql, $CFG->dbuser, $CFG->dbpass) or die("Cannot connect to database engine!");
		mysqli_select_db($db, $CFG->old_database) or die("Cannot connect to database $CFG->dbname !");
		mysqli_query ($db, "set names utf8");
		$sql = "SELECT distinct c.id courseid, c.fullname coursename, c.shortname shortname FROM mdl_user u, mdl_role_assignments r, mdl_context cx, mdl_course c WHERE u.id = 
r.userid AND r.contextid = cx.id AND cx.instanceid = c.id AND r.roleid in (2,3) AND cx.contextlevel =50 ";

		if ($USER->username != 'admin') $sql .= "AND u.username = '".$USER->username."'";
		$result = mysqli_query($db, $sql) or die(mysqli_error($db));

		if (!$result) echo "Aucun cours disponible";
		else {
			
			$select_oldcourse = $mform->createElement( 'select', 'oldcourse', 'Ancien cours :', null);
			$select_oldcourse->addOption( 'Ancien cours', '', array( 'disabled' => 'disabled', 'selected'=>'true' ) );
			
			while ($row = mysqli_fetch_assoc($result)) $select_oldcourse->addOption($row["coursename"] . '(' .$row["shortname"] .')',$row["courseid"]);
			$mform->addElement($select_oldcourse);
			// $mform->addRule('oldcourse', 'Vous devez saisir une ligne dans "Ancien cours"', 'required', '', 'client');
		} 
		mysqli_close($db);
		
		$this->add_action_buttons();

		$mform->addElement('header', 'source', 'Suppression d\'un cours cr&eacute;&eacute; par erreur');
		$mform->addElement('html', 'Si vous souhaitez annuler une demande de cr&eacute;ation de cours effectu&eacute;e depuis cette interface, <a href="annuler_creation_cours.php">cliquez ici</a>.<br/><br/>') ;

	}
	//Custom validation should be added here
	function validation($data, $files) {
		return array();
	}
}

?>
