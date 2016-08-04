<?php

####################################################
####################################################
##
##  FILENAME: tgProcessors.php
##
##  DESCRIPTION: 
##    Contains functions to initialize and use a 
##    new query object. 
##
##  REVISIONS:
##    handling of near match NAME search (Emma Reishus)
##
####################################################
####################################################
/*
Copyright (C) 2010 Massachusetts Institute of Technology 

This software was developed by the MIT Kavli Institute for
Astrophysics and Space Research under contract SV3-73016 from the
Smithsonian Institution.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA
*/

require "tgNewQueryLib.php";

//
// processes and initializes a new query object
// takes the query object and an optional boolean 
// value as parameters
//
function preprocessQueryRequests( $q, $first=TRUE ){
  //
  // input arg is expected to be a query object
  //
  $rType = $_REQUEST['t'];
  $qid = $_REQUEST['q'];
  $targ = $_REQUEST['targ'];
  $tableCode = $_REQUEST['c'];
  $qType = $_REQUEST['queryType'];
  //$q->setSearchType( $qType );
  //
  // initialize
  //
  if ( $rType == "NQ" && $first){ // if this is the first search
    processNewQueryForm( $q );
    $qid = $q->getQueryId();
    $storedQids = split(",",$_COOKIE['qids'] );
    if ( count($storedQids) > 30 ){ array_shift( $storedQids ); }
    array_push( $storedQids, $qid );
    setcookie( "qids", implode(",",$storedQids ), time() + 365*86400 );
    //print_r( $q );
    header( "Location: tgData.php?q=$qid" );
    return array("targ" => $targ, "qid" => $qid);
  }
  else if( $rType == "NQ" ) { // if this is the second search (for near matches)
    $_POST['targ'] = $near_matches;
    processNewQueryForm( $q );
    $qid = $new_query->getQueryId();
    $storedQids = split(",",$_COOKIE['qids'] );
    if ( count($storedQids) > 30 ){ array_shift( $storedQids ); }
    array_push( $storedQids, $qid );
    setcookie( "qids", implode(",",$storedQids ), time() + 365*86400 );
    //print_r( $new_query );
    $new_query->setSearchType( $qType );
  }

  else if ( $qid ){    
    $q->initQuery( $qid );
    //
    // set the cookie for our current working 
    // query ( no matter how old )
    // 
    setcookie( "myCurrQid",$qid,time() + 365*86400 );
  }
  //
  // set stuff
  //
  if ( $tableCode ){
    $q->setTableCode( $tableCode );
  }
  if ( $rType == "RP" ){
    //
    // this is reorder primary type
    //
    $sort = $_REQUEST['s'];
    $q->setSort( $sort );
  }
  elseif ( $rType == "RS" ){
    //
    // this is reorder secondary type
    //
    $sort = $_REQUEST['s'];
    $q->setAddSort($sort);
  }
  elseif ( $rType == "CC" || $rType == "DC" ){
    //
    // this means we want to change columns
    //   
    $newcols = "";
    if ( ! $_REQUEST['l'] ){
      $newcols = processChangeColumnForm();
    }
    else if ( $_REQUEST['l'] ){
      $newcols = $_REQUEST['l'];
    }
    if ( $rType == "DC" || ! $newcols ){
      //
      // this is a request for the default columns
      // 
      $q->setDefaultColumns();
    }
    else {
      $q->setColumns( $newcols );
    }
    //
    // if the user would like to save the selected columns
    // set a cookie to save that info ( right now for a year ) 
    //    
    if ( $_REQUEST['save'] )
      {	
	$cookiename = $q->getTableCode() . "columns";
	$scols = implode(",",$newcols );
	if ( $scols == "" ) { $scols="NOCOLS"; }
	setcookie($cookiename,$scols,time() + 365*86400 );
      }       
    header( "Location: tgData.php?q=$qid" );
  }
  elseif ( $rType == "TQ" ){
    //print_r( $_REQUEST );   
    $q->setDescription( $_REQUEST['tag'] );
    header( "Location: tgData.php?q=$qid&n=T" );
  }
  elseif ( $rType == "FQ" ){
    if ( $_REQUEST['filtercond'] ){
      $q->setFilter( $_REQUEST['filtercol'], $_REQUEST['filterop'], $_REQUEST['filtercond'] );
    }
    header( "Location: tgData.php?q=$qid" );
  }
  elseif ( $rType == "AC" ){
    //
    // we need to process data table actions
    // 
    if( $_REQUEST['action'] ) { $action = $_REQUEST['action']; }
    if( $_REQUEST['combined'] ) { $combined = $_REQUEST['combined']; }
    if( $_REQUEST['multiple'] ) { $multiple = $_REQUEST['multiple']; }
    $actionIds = $_REQUEST['dsc'];
    $aIdsStr = join( ",", $actionIds );

    if ( $action == "Limit" ){
      if ( count( $actionIds ) != 0 ){
	$q->setIds( $actionIds );
      }
      header( "Location: tgData.php?q=$qid" );
    }
    if ( $action == "Go to Source Table" ){
      if ( count( $actionIds ) != 0 ){
	$q->setIds( $actionIds );
      }
      $q->setTableCode( "s" );
      header( "Location: tgData.php?q=$qid" );
    }
    if ( $action == "Go to Extractions Table" ){
      if ( count( $actionIds ) != 0 ){
	$q->setIds( $actionIds );
      }
      $q->setTableCode( "o" );
      header( "Location: tgData.php?q=$qid" );
    }
    if ( $action == "Download" ){
      $ids = getDataTableIdString();
      if ( $ids ){
	header( "Location: tgRequests.php?q=$qid&t=D&i=$ids" );
      }
    }
    if ( $action == "Clear filters" ){
      $q->clearFilter();
      header( "Location: tgData.php?q=$qid" );
    }     
    if ( $action == "Reset query" ){
      $q->setOriginal();
      header( "Location: tgData.php?q=$qid" );
    }     
    if ( $action == "Tag Query" ){
      header( "Location: tgRequests.php?q=$qid&t=T" );
    }
    if ( $action == "Filter results" ){
      header( "Location: tgRequests.php?q=$qid&t=F&i=" . implode( "," ,$q->getColumns()) );
    }

    
    if ( $action == "Plot ( Combined )" || $combined ){
      //
      // create combined plot in new page
      //
      $ids = getDataTableIdString();
      if ( $ids ){
	$idcode = "i";
	$fr = time() . "T" . mt_rand();
	if ( $combined == "Use Saved Parameters" ) {$usesaved = 'Y'; }
        else { $usesaved=''; }
	if ( $_REQUEST['c'] == 's' ){ $idcode = "s"; }
	if ( $_REQUEST['dlusedjs'] == 1 ){
	  print "<script type='text/javascript'>
window.open( 'tgPlot.php?$idcode=$ids&t=C&usesaved=$usesaved&fr=$fr','combinedDataWindow' ).focus()
</script>";
	}
	else {
	  header( "Location: tgPlot.php?$idcode=$ids&q=$qid" );
	}
      }
    }

    if( $action == "Plot ( Multiple )" || $multiple ) {
      //
      // create multiple plot(s) in new page(s)
      //
      $ids = $_POST["dsc"];
      if ( $ids ){
	$idcode = "i";

        if ( $multiple == "Use Saved Parameters" ) { $usesaved = '&usesaved=Y'; }
        else { $usesaved=''; }
	//if ( $_REQUEST['c'] == 's' ){ 
	// $idcode = "s"; // }

	$sid = "";
	for( $i = 0; $i < count($ids); $i++) {
	  $sid .= $ids[$i];
	  if ( $i + 1 < count($ids) ) { $sid .= ","; }
	}
	$d = mt_rand();
	$r = time();

	mkdir("tmp/multiple$d$r");
	chmod("tmp/multiple$d$r",0777);

	print "<script type='text/javascript'>";
	// 
	// loop through ids to create each plot
	// 
	$files = "";
	for( $i = 0; $i < count($ids); $i++) {
	  $id = $ids[$i];
	  $fr = time() . "T" . mt_rand();
	  print "window.open('tgPlot.php?$idcode=$id&t=C&d=$d&r=$r&fr=$fr$usesaved').focus();\n";
	  //$fr_list .= "tmp/$d/$frascii.dat.gz ";
	  chmod("tmp/multiple$d/$frascii.dat.gz", 0777); // needed??
	  chmod("tmp/multiple$d$r/$dM$r", 0777); // needed??
	}
	print "</script>";
	//print "<script type='text/javascript'>alert('test, end tgProcessors');</script>";
      }
    }	
  }
}

function processChangeColumnForm()
{
  $newcols = Array();
  foreach ( array_keys( $_REQUEST ) as $request ){
    if ( preg_match( "/^show_.*/",$request ) ){
      //$c = split( "[_]",$request );
      $c = preg_replace( "/show_/","", $request );
      array_push( $newcols, $c );
    }
  }
  return $newcols;
}

function getDataTableIdString()
{
  $ids = join( ",", $_POST["dsc"] );
  return $ids;
}

?>
