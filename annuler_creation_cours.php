<?
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/enrol/meta/lib.php');
require_once($CFG->dirroot.'/mod/url/lib.php');
require_once($CFG->dirroot.'/course/lib.php');

require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('base');

echo $OUTPUT->header();

$datejour = date('d/m/Y');
$djour = explode("/", date('d/m/Y')); 
$auj = $djour[2].$djour[1].$djour[0]; 

$uid = $USER->username;
$nom = fullname($USER, true);

//moodleform
require_once($CFG->dirroot.'/local/creation_cours/form_annulation_cours.php');

?>
<script type="text/javascript">
function setTextField(ddl, id) {
	document.getElementById(id).value = ddl.options[ddl.selectedIndex].text;
}
</script>

<?
//Instantiate simplehtml_form 
$mform = new annul_html_form();
 
//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
} else if ($fromform = $mform->get_data()) {
  //In this case you process validated data. $mform->get_data() returns data posted in form.
  $formdata = $mform->get_data();
  $course = $formdata->course;
  $tcourse = $formdata->tcourse;
  $headers = "From: no-reply@unimes.fr\r\n";
  $message = utf8_decode("demande de suppression du cours ".$tcourse." (".$course.") par ".$uid);
  mail("brice.quillerie@unimes.fr",$message,$message,$headers);
//  mail("sophie.vessiere@unimes.fr",$message,$message,$headers);
  echo "Votre demande d'annulation a &eacute;t&eacute; prise en compte.<br/><br/> Pour &eacute;viter des cons&eacute;quences facheuses, celle-ci doit &ecirc;tre effectu&eacute; manuellement.";
} else {
  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
  // or on the first display of the form.
 
  //Set default data (if any)
  $mform->set_data($mform);
  //displays the form
  $mform->display();
}
?>