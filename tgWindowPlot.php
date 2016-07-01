<?php
ob_start( "ob_gzhandler" );
?>
<?php
##############################################################################
##
## TGCAT WEBPAGE DOCUMENT
##
## TITLE: tgcat_imgView.php
##
## DESCRIPTION:
##
##      this is the premade image viewing GUI for the tgcat website
##      will allow viewing of one tgcat id at a time
##
## REVISION:
##
##     -v 1.0 Arik Mitschang (Author)
##            Emma Reishus
##
##############################################################################
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title> TGCat Spectral Plotter </title>
<?php

  //error_reporting( E_ALL );
  //ini_set( "display_errors",1);

require "tgMainLib.php";
require "tgDatabaseConnect.php";
require "styleInc.php";

//
// LOCAL DATA -----------------
//

//
// parse http query
//
$id = $_GET['i'];
$img_id = $_GET['m'];
$fr = $_GET['f'];

?>

<style type="text/css">
body { 
  background-image:none;
  background-color:none;
}
</style>
</head>
<body>

<div id='prevWin'>

<?php
if ( is_multi_id( $id ) ){
  $qcheck = "SELECT count( distinct(instrument) ) as icnt,count( distinct( grating ) ) as gcnt FROM obsid WHERE id in ( $id )";
  $qcheck = mysql_query( $qcheck );
  $qcheck = mysql_fetch_assoc( $qcheck );
  if ( $qcheck['icnt'] > 1 || $qcheck['gcnt'] > 1 ){
    print "WARNING: Cannot combine differing INSTRUMENT/GRATINGS";
    exit;
  }
}
$myid = split_multi_id( $id );
$myid = $myid[0];
$q = "SELECT distinct(instrument),grating FROM obsview WHERE id in ( $myid )";
$q = mysql_query( $q )  or die( mysql_error() );
$info = mysql_fetch_assoc( $q );
$DETECTOR=$info['instrument'];
$GRATING=$info['grating'];
$FILEROOT=$fr;

$window_plot = TRUE;

print "<div id='GUIControls'>";
include "tgGUI.php";
print "</div>";

//print "<br> get value from tgWindowPlot = " . $_GET['usesaved'] . "<br>";


//grab usesaved parameter
if ( $_GET['usesaved'] ) {
  $usesaved=$_GET['usesaved'];
 }

if ( $usesaved ) {
  //print "USESAVED<br>";
  print "<iframe name='custom_plot' id='customPlot' style='position:relative;width:100%;height:630px;' src='tgRunPlot.php?id=$id&det=$info[instrument]&grat=$info[grating]&fr=$fr&usesaved=$usesaved'></iframe>";
 }
 else {
   //print "NOT USESAVED<br>";
   print "<iframe name='custom_plot' id='customPlot' style='position:relative;width:100%;height:630px;' src='tgRunPlot.php?id=$id&det=$info[instrument]&grat=$info[grating]&fr=$fr'></iframe>";
 }

?>

</div>

</body>
</html>
