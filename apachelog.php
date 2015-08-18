<?php
error_reporting(0);

if(isset($_POST['gene'])){


$ac_arr = $_FILES['elfile']['tmp_name'];

$error_fle = file($ac_arr);

$astring = join("", $error_fle);

$astring = preg_replace("/(\n|\r|\t)/", "", $astring);

$records = preg_split("/([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $astring, -1, PREG_SPLIT_DELIM_CAPTURE);

$sizerecs = sizeof($records);

// now split into records
$i = 1;
$j = 0;
$each_rec = 0;
$iphits = array();
$dayhit = array();
$vishit = array();

while($i<$sizerecs) {
  $ip = $records[$i];
  $all = $records[$i+1];
  // parse other fields
  preg_match("/\[(.+)\]/", $all, $match);
  $access_time = $match[1];
  $all = str_replace($match[1], "", $all);
  preg_match("/\"[A-Z]{3,7} (.[^\"]+)/", $all, $match);
  $http = $match[1];
  $link = explode(" ", $http);
  $all = str_replace("\"[A-Z]{3,7} $match[1]\"", "", $all);
  preg_match("/([0-9]{3})/", $all, $match);
  $success_code = $match[1];
  $all = str_replace($match[1], "", $all);
  preg_match("/\"(.[^\"]+)/", $all, $match);
  $ref = $match[1];
  $all = str_replace("\"$match[1]\"", "", $all);
  preg_match("/\"(.[^\"]+)/", $all, $match);
  $browser = $match[1];
  $all = str_replace("\"$match[1]\"", "", $all);
  preg_match("/([0-9]+\b)/", $all, $match);
  $bytes = $match[1];
  $all = str_replace($match[1], "", $all);

  $iphits[$j]['ip'] = $ip;
  
  $tim =  date_parse_from_format("d/M/Y", $access_time);

  $iphits[$j]['time'] = $tim['year'].'/'.$tim['month'].'/'.$tim['day'];
  $day['time2'] = $tim['year'].'-'.$tim['month'].'-'.$tim['day'];
  $dayhit[$j][$day['time2']] = $ip;
  $vishit[$j][$ip] = $day['time2'];
  

  //echo "<br>IP: $ip<br>Access Time: $access_time<br>Page: $link[0]<br>Type: $link[1]<br>Success Code: $success_code<br>Bytes Transferred: $bytes<br>Referer: $ref <br>Browser: $browser<hr>";
  // advance to next record
  $j++;
  $i = $i + 2;
  $each_rec++;
}

echo "<pre>";
//print_r($iphits);

/* 

- number of hits / day (for each day) 

*/
$counts = array();
foreach($iphits as $key=>$subarr) {
  if (isset($counts[$subarr['time']])){
    $counts[$subarr['time']]++;
  }
  else $counts[$subarr['time']] = 1;
  $counts[$subarr['time']] = isset($counts[$subarr['time']]) ? $counts[$subarr['time']]++ : 1;
}

echo "- number of hits / day (for each day)";
print_r($counts);

/*
- number of unique visitors / day (for each day)
*/
$input = array_map("unserialize", array_unique(array_map("serialize", $iphits)));

//print_r($input);

$counts = array();
foreach($input as $key=>$subarr) {
  if (isset($counts[$subarr['time']])){
    $counts[$subarr['time']]++;
  }
  else $counts[$subarr['time']] = 1;
  $counts[$subarr['time']] = isset($counts[$subarr['time']]) ? $counts[$subarr['time']]++ : 1;
}
echo "- number of unique visitors / day (for each day)";
print_r($counts);



/*
 Average hits per visitor
*/

$date_count = array(); 

foreach($vishit as $key=>$date) { 
        $key = array_keys($date);  // get our date
        $date_count[$key[0]]++;
}
echo "- average number of hits / visitor (for the whole period)";
print_r($date_count);
}
?>


<form enctype="multipart/form-data" name="formelog" action="" method="post">
    <input type="file" name="elfile" />
    <input type="submit" name="gene" value="Generate" />
</form>
