<?php
/*
* @version 0.1 (wizard)
*/
 global $session;
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $qry="1";
  // search filters
  // QUERY READY
  global $save_qry;
  if ($save_qry) {
   $qry=$session->data['MiLamp1_qry'];
  } else {
   $session->data['MiLamp1_qry']=$qry;
  }
  if (!$qry) $qry="1";
  $sortby_MiLamp1="ID DESC";
  $out['SORTBY']=$sortby_MiLamp1;
  // SEARCH RESULTS
  $res=SQLSelect("SELECT * FROM MiLamp1 WHERE $qry ORDER BY ".$sortby_MiLamp1);
  if ($res[0]['ID']) {
   //paging($res, 100, $out); // search result paging
   $total=count($res);
   for($i=0;$i<$total;$i++) {
    // some action for every record if required
   }
   $out['RESULT']=$res;
  }
