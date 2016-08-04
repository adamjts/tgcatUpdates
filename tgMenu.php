<?php

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


$LAST_QUERY = null;
if ( $_COOKIE['myCurrQid'] ){
  $LAST_QUERY =  $_COOKIE['myCurrQid'];
}

function initMainMenu(){
  print "
<div id='menuBar'>
<div id='menu'>
";
}

function finalizeMainMenu(){
  print "
</div>
</div>
";
}

function generateFileMenu(){
  //
  // this should contain links to
  // main parts of the site and should
  // be mostly independent from the parent
  // page
  //
  global $LAST_QUERY;

  $menuTitle = "TGCat";
  print "
<ul>
<li> <a id='main'> $menuTitle </a>
<ul>
  <li><a href='.'> Home </a> </li>
  <li><a href='about.php'> About </a> </li>
";
  if ( $LAST_QUERY && ! preg_match( '/tgData.php/',$_SERVER['SCRIPT_FILENAME']) ){
    print "<li><a href='tgData.php?q=$LAST_QUERY'>Back to Query Results</li>\n";
  }
print "
  <li><a href='tgStage.php'> Package Download Area </a></li>
  <li><a href='tgTrend.php'> Trends </a></li>
";
  #generateQuerySubMenu();
print "
  <li><a onclick='window.close();'>Close</a></li>
</ul>
</li>
</ul>
";

 generateQueryMainMenu();

}

function generateQueryMenuBody(){
  global $LAST_QUERY;
  print "
  <li><a href='tgSearch.php?t=N'>Name </a></li>
  <li><a href='tgSearch.php?t=C'>Cone Search </a></li>
  <li><a href='tgSearch.php?t=T'>Type </a></li>
  <li><a href='tgSearch.php?t=S'>Spectral Properties </a></li>
  <li><a href='tgSearch.php?t=O'>Obsid </a></li>
  <li><a href='tgSearch.php?t=D'>Arbitrary Extraction Column </a></li>
  <li><a href='tgSearch.php?t=X'>Arbitrary Source Column </a></li>
";
 if ( $LAST_QUERY || $_COOKIE['qids'] ){
   print "<li><a>------------------</a></li>\n";
 }
 
 if ( $LAST_QUERY && ! preg_match( '/tgData.php/',$_SERVER['SCRIPT_FILENAME']) ){
   print "<li><a href='tgData.php?q=$LAST_QUERY'>Latest Query Results</li>\n";
 }
 if ( $_COOKIE['qids'] ){ print "<li><a href='tgSelf.php'>My Recent Queries</a></li>\n"; }
}

function generateQueryMainMenu(){
  $menuTitle = "Query";
  print "
<ul>
<li><a id='main'>$menuTitle</a>
<ul>
";
  generateQueryMenuBody();
print "
</ul>
</li>
</ul>
";
}  

function generateQuerySubMenu(){
  //
  // the query submenu will allow the user to
  // navigate to one of the query types to
  // search the database 
  //
  $menuTitle = "Query";
  print "
<li><a><span class='leaf'>$menuTitle</span></a>
<ul>
";
  generateQueryMenuBody();
print "
</ul>
</li>
";
}

function generateDataViewMenu( $qid, $tableCode, $cols ){
  //
  // make a "veiw" menu that has data viewing
  // related properties
  //
  $menuTitle = "View";
  $switchTable = generateChangeTableCodeOption($qid, $tableCode );
  print "
<ul>
<li> <a id='main'>$menuTitle </a>
<ul>
";
  generateSortSubMenu( $qid, $cols ); 
  if ( is_array( $cols ) ){ $cols=join( ",", $cols ); }
  print "
<li><a id='openColumnSelect' href='tgRequests.php?t=C&amp;q=$qid&amp;c=$tableCode&amp;i=$cols'>Change Display Columns</a></li>
<!-- <li> $switchTable </li> -->
<li> <a target='_blank' href='tgCli.php?q=$qid&OUTPUT=A'> ASCII Table </a> </li>
<li> <a target='_blank' href='tgCli.php?q=$qid&OUTPUT=V'> VOTable </a> </li>
 </ul>
</li>
</ul>
";
}  

function generateChangeTableCodeOption( $qid, $tableCode ){
  //
  // this just creates a linke to change from
  // one table type to the next for the same
  // query
  //
  if ( $tableCode == "s" ){
    return "<a href='tgData.php?q=$qid&amp;c=o'>View Extractions Table</a>";
  }
  else {
    return "<a href='tgData.php?q=$qid&amp;c=s'>View Source Table</a>";
  }
}
function generateSortSubMenu( $qid, $cols ){
  //
  // create the menu item that controls
  // the sorting of the data table
  //
  $sortPage = "tgData.php";
  $menuTitle = "Sort";
  print "
<li> <a><span class='leaf'>$menuTitle</span></a>
<ul>
";
  foreach ( $cols as $col ){
    print "
  <li> <a class='drop'><span class='leaf'>$col</span></a>
    <ul>
     <li><a href='${sortPage}?q=$qid&amp;t=RP&amp;s=${col}+asc'> Primary Order Asc  </a></li>
     <li><a href='${sortPage}?q=$qid&amp;t=RP&amp;s=${col}+desc'> Primary Order Desc </a></li>
     <li><a href='${sortPage}?q=$qid&amp;t=RS&amp;s=${col}+asc'> Secondary Order Asc  </a></li>
     <li><a href='${sortPage}?q=$qid&amp;t=RS&amp;s=${col}+desc'> Secondary Order Desc </a></li>
    </ul>  
  </li>
";
  }
  print "</ul>
</li>
";
}

function generateHelpMenu(){
  //
  // a help menu containing links to
  // useful parts of the site
  //
  $menuTitle = "Help";
  print "
<ul id='right'>
<li><a id='main'> $menuTitle </a>
<ul> 
  <li><a href='tgHelp.php'> Help </a></li>
  <li><a href='tgHelp.php?guide=help/tgcat_help_menu_quickguide.html'> Menu Quick Guide </a></li>
  <li><a href='about.php'> About </a></li>
  <li><a href='tgNews.php'> Announcements Feed </a></li>
  <li><a href='http://space.mit.edu/cxc/analysis/tgcat/index.html' target='_blank'> Software </a></li>
  <li><a href='mailto:tgcat@space.mit.edu'> Contact Us </a></li>
  <!-- <li><a href='tgHelp.php'> Help on this Page </a></li> -->
</ul>
</li>
</ul>
";
}

function generateQuickSearchBar(){
  //
  // make the quick search form in the 
  // menu bar
  //
  $menuTitle = "Quick Search";
  print "
<ul>
<li> <a id='main'>$menuTitle</a>
<ul>
   <li><a id='quickSearchMenu'><form action='tgData.php?t=NQ' method='POST' style='border:0px;margin:0px;padding:10px;'>Quick Search:<input type='text' name='quicksearch' size=25><input type='hidden' name='queryType' value='QUICK'><input type='Submit' value='Go'></form></a></li>
</ul>
</li>
</ul>
";
}

function generateDataActionMenu( $tableCode ){
  //
  // create the actions for the data window
  //
  $menuTitle = "Actions";
  $inputStyle = "width:100%;height:100%;display:block;padding:6px;margin:0px;font-size:14px;min-width:200px;";
  print "
<ul>
<li> <a id='main'>$menuTitle</a>
<ul>
   <li><a href='#'><input style='$inputStyle' class='di' type='submit' name='action' value='Limit'></a></li>
   <li><a href='#'><input style='$inputStyle' id='openDownloadWindow' class='di' type='submit' name='action' value='Download'></a></li>
";
  if ( $tableCode == "o" ){
    print "<li><a href='#'><input style='$inputStyle' class='di' type='submit' name='action' value='Go to Source Table' onClick='SKIPCHECK=1;'></a></li>\n";

    if (! $_COOKIE['tgPlotParams'] ) { 
      print "<li><a href='#'><input style='$inputStyle' class='di' type='submit' name='action' value='Plot ( Combined )'></a></li>\n
<li><a href='#'><input style='$inputStyle' class='di' type='submit' name='action' value='Plot ( Multiple )'></a></li>\n";
      } 
    else {
      print "
    <li><a class='drop'><span class='leaf'>Plot ( Combined )</span></a>\n
      <ul>\n
       <li><li><a href='#'><input style='$inputStyle' class='di' type='submit' name='combined' value='Use Default Parameters'></a></li>\n
       <li><a href='#'><input style='$inputStyle' class='di' type='submit' name='combined' value='Use Saved Parameters'></a></li>\n
      </ul>\n
    </li>\n
    <li><a class='drop'><span class='leaf'>Plot ( Multiple )</span></a>\n
      <ul>\n
       <li><a href='#'><input style='$inputStyle' class='di opt_default' type='submit' name='multiple' value='Use Default Parameters'></a></li>\n    
       <li><a href='#'><input style='$inputStyle' class='di opt_saved' type='submit' name='multiple' value='Use Saved Parameters'></a></li>\n
      </ul>\n
    </li>\n";
    }
  }
  else {
    print "<li><a href='#'><input style='$inputStyle' class='di' type='submit' name='action' value='Go to Extractions Table' onClick='SKIPCHECK=1;'></a></li>";
  }
  print "<li><a href='#'><input style='$inputStyle' id='openFilterWindow' class='di' type='submit' name='action' value='Filter results' onClick='SKIPCHECK=1;'></a></li>";
  print "<li><a href='#'><input style='$inputStyle' class='di' type='submit' name='action' value='Clear filters' onClick='SKIPCHECK=1;'></a></li>";
  print "<li><a href='#'><input style='$inputStyle' class='di' type='submit' name='action' value='Reset query' onClick='SKIPCHECK=1;'></a></li>";
  print "<li><a href='#'><input style='$inputStyle' id='openTagWindow' class='di' type='submit' name='action' value='Tag Query' onClick='SKIPCHECK=1;'></a></li>
</ul>
</li>
</ul>
";


}

function generatePlotViewMenu( $id, $fr, $multiple=FALSE ){
  $menuTitle = "View";  
  print "
<ul>
<li> <a id='main'>$menuTitle</a>
<ul>
   <li><a href='tgPrev.php?i=$id&amp;m=P' target='imageWin'>Preview Gallery</a></li>
   <li><a href='tgPrev.php?i=$id&amp;m=F' target='imageWin'>File Table</a></li>
   <li><a href='tgPrev.php?i=$id&amp;m=S' target='imageWin'><span class='leaf'>Spectral Properties Table</span></a>
       <ul>
       <li><a href='tgPrev.php?i=$id&amp;m=S&amp;s=wmid asc,wlo asc,whi asc' target='imageWin'>Sort by wavelength (default)</a></li>
       <li><a href='tgPrev.php?i=$id&amp;m=S&amp;s=count_rate desc' target='imageWin'>Sort by count_rate</a></li>
       <li><a href='tgPrev.php?i=$id&amp;m=S&amp;s=photon_flux desc,count_rate desc' target='imageWin'>Sort by photon_flux</a></li>
       <li><a href='tgPrev.php?i=$id&amp;m=S&amp;s=energy_flux desc,count_rate desc' target='imageWin'>Sort by energy_flux</a></li>
       </ul>
   </li>
   <li><a href='tgPrev.php?i=$id&amp;m=V' target='imageWin'>VV Report</a></li>
   <li><a><span class='leaf'>Custom Plotting</span></a>
       <ul>
";
  if (! $_COOKIE['tgPlotParams']) {
    print "
       <li><a href='tgPrev.php?i=$id&amp;m=C&amp;f=$fr' target='imageWin' onclick='setPlotted();'>Open Plotter</a></li>
       <li><a href='tgWindowPlot.php?i=$id&amp;m=C&amp;f=$fr' target='_blank' onclick='setPlotted();'>Open Plotter ( new window )</a></li>
      ";
  
  }
  else {
    print "
          <li> <a><span class='leaf'>Open Plotter</span></a> 
             <ul>
             <li><a href='tgPrev.php?i=$id&amp;m=C&amp;f=$fr' target='imageWin' class='onclick='setPlotted();'>Use Default Parameters</a></li>
             <li><a href='tgPrev.php?i=$id&amp;m=C&amp;f=$fr&amp;usesaved=Y' target='imageWin' onclick='setPlotted();'>Use Saved Parameters</a></li> 
             </ul>
          </li>
          <li> <a><span class='leaf'>Open Plotter ( new window )</span></a>
             <ul>
             <li><a href='tgWindowPlot.php?i=$id&amp;m=C&amp;f=$fr' target='_blank' onclick='setPlotted();'>Use Default Parameters</a></li>
             <li><a href='tgWindowPlot.php?i=$id&amp;m=C&amp;f=$fr&amp;usesaved=Y' target='_blank' onclick='setPlotted();'>Use Saved Parameters</a></li>
             </ul>
          </li>";                                                                                                                                                            
  } 
  if( $multiple ) {
    $d = $multiple[0];
    $r = $multiple[1];
    print "<script type='text/javascript' scr='tgZip.js'></script>";
    print "
       <li> <a><span class='leaf'>ASCII Dump</span><a>
          <ul>
          <li><a href='tmp/${fr}ascii.dat.gz' target='imageWin' title='note: plotting required first' onclick='return verifyPlotted();'>Individual ASCII Dump</a></li>
          <li><a href='tmp/multiple${d}${r}/${d}M${r}.tar' target='imageWin' title='note: plotting required first' onclick='return verifyPlotted();'>Multiple ASCII Dump</a></li>
          </ul>
       </li>";
    // print "<script type='text/javascript' src='https://raw.github.com/Stuk/jszip/master/jszip.js'></script>";
    //print "<script type='text/javascript' src='tgZip.js'></script>
  }
  else {
    print "
       <li><a href='tmp/${fr}ascii.dat.gz' target='imageWin' title='note: plotting required first' onclick='return verifyPlotted();'>ASCII Dump</a></li>";
  }
  print "
       <li><a href='tmp/${fr}.commands.sl' target='imageWin' title='note: plotting required first' onclick='return verifyPlotted();'>ISIS Commands File</a></li>
       <li><a href='tmp/${fr}.error' target='imageWin' title='note: plotting required first' onclick='return verifyPlotted();'>ISIS Error log</a></li>
       </ul>
   </li>
</ul>
</li>
</ul>
";
}

function generateHelpTopicsMenu(){
  $menuTitle = "Help Topics";
  print "
<ul>
<li> <a id='main'>$menuTitle</a>
<ul>
    <li><a href='tgHelp.php'>Intro</a></li>
    <li><a><span class='leaf'>Catalog Creation</span></a>
        <ul>
           <li><a href='tgHelp.php?guide=help/tgcat_help_processing.html'><i>Catalog Creation</i></a></li>  
           <li><a href='tgHelp.php?guide=help/tgcat_help_processing.html#gratings_modes'>-Supported Modes</a></li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_processing.html#processing_quick_guide'>-Quick Guide</a></li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_processing.html#zero_order_methods'>-Zeroth Order Determination</a></li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_processing.html#vv'>-Validation and Verification</a></li>
        </ul>
    </li>
    <li><a><span class='leaf'>Searching TGCat</span></a>
        <ul>
           <li><a href='tgHelp.php?guide=help/tgcat_help_search.html'><i>Searching TGCat</i></a></li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_search.html#OBSID_SEARCH'>-By Obsid</a></li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_search.html#NAME_SEARCH'>-By Target Name</a> </li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_search.html#COORD_SEARCH'>-By Target Coordinates</a></li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_search.html#SPECPROP_SEARCH'>-By Derived Spectral Properties</a></li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_search.html#OTHER_SEARCH'>-By Arbirtary Database Fields</a></li>
        </ul>
    </li>
    <li><a><span class='leaf'>Query Results</span></a>
        <ul>
           <li><a href='tgHelp.php?guide=help/tgcat_help_results.html'><i>Query Results</i></a></li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_results.html#source_data_table'>-Source Table</a></li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_results.html#extractions_data_table'>-Extractions Table</a> </li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_results.html#results_change_columns'>-Changing Displayed Columns</a></li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_results.html#results_sorting'>-Sorting the tables</a></li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_results.html#results_download'>-Request Packaged Data</a></li>
        </ul>
    </li>
    <li><a><span class='leaf'>Summary Products</span></a>
        <ul> 
           <li><a href='tgHelp.php?guide=help/tgcat_help_summary.html'><i>Summary Products</i></a></li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_summary.html#canned_plots'>-Precomputed plots</a></li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_summary.html#other_sum_products'>-Other summary products</a></li>
        </ul>
    </li>
    <li><a><span class='leaf'>Plotting</span></a>
        <ul> 
           <li><a href='tgHelp.php?guide=help/tgcat_help_plotting.html'><i>Plot customization</i></a></li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_plotting.html#plot_getthere'>-How to get there</a></li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_plotting.html#indplot'>-Individual extraction plots</a></li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_plotting.html#combplot'>-Combined extraction plots</a></li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_plotting.html#multiplot'>-Multiple extraction plots</a></li>
           <li><a href='tgHelp.php?guide=help/tgcat_help_plotting.html#bugs'>-Bugs</a></li>           
           <!-- <li><a href='tgHelp.php?guide=help/tgcat_help_plotting.html#plot_something'>-Example</a></li> -->
        </ul>
    </li>
    <li><a href='tgHelp.php?guide=help/tgcat_demos.html'>Demos/Tours/Guides</a></li>

</ul>
</li>
</ul>
";
}

function generateTrendViewMenu( $type ){
  $menuTitle = "View";
  if ( $_SERVER['QUERY_STRING'] ){
    $addref = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'] . '&amp;';
  }
  else{
    $addref = $_SERVER['SCRIPT_NAME'] . '?';
  }
  print "
<ul>
<li> <a id='main'>$menuTitle</a>
    <ul>
";
  if ( $type == "SPECPROP" ){
    print "<li><a href='tgTrend.php?type=OBSID'>Observation Trends</a></li>";
  }
  print "
    <li><a href='tgTrend.php?type=SPECPROP&amp;C=1'>Count Rates Statistics</a></li>
    <li><a href='tgTrend.php?type=SPECPROP&amp;P=1'>Photon Flux Statistics</a></li>
    <li><a href='tgTrend.php?type=SPECPROP&amp;E=1'>Energy Flux Statistics</a></li>
";
  if ( $type == "SPECPROP" ){
    print "  
    <li><a><span class='leaf'>Add</span></a>
        <ul>
           <li><a href='${addref}C=1'>Count Rates Statistics</a></li>
           <li><a href='${addref}P=1'>Photon Flux Statistics</a></li>
           <li><a href='${addref}E=1'>Energy Flux Statistics</a></li>
        </ul>
    </li>
";
  }
print "
</ul>
</li>
</ul>
";
}
?>
