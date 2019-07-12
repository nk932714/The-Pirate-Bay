<?php

/*************************FILTERS ********************************/
    if (isset($_GET['audio'])) {$audio ="100,"; } else{$audio =""; }
    if (isset($_GET['video'])) {$video ="200,"; } else{$video =""; } 
    if (isset($_GET['apps'])) { $apps="300,";   } else{ $apps="";   }
    if (isset($_GET['games'])) { $games="400,"; } else{ $games=""; }
    if (isset($_GET['porn'])) { $porn = "500,"; } else{ $porn = ""; }
    if (isset($_GET['other'])){ $other = "600,";} else{ $other = "";}
    $total_filters = $audio.$video.$apps.$games.$porn.$other;
    $total_filters = substr($total_filters, 0, -1); // remove last comma
    
    if (isset($_GET['category'])) {$category =$_GET['category']; } else{$category =""; }

    if (!empty($total_filters) && !empty($category)){
            $main_filters = $total_filters; 
    }
    elseif(!empty($category)){
            $main_filters = $category; 
        }
    elseif(!empty($total_filters)){
            $main_filters = $total_filters;
        }
    else{
            $main_filters = "0";
        }
//echo "<br><br>$main_filters";
/***************************** Page no ****************************/
if (isset($_GET['page'])) { $pagea = $_GET["page"]; $pageno = $pagea; $next_page = $pageno + 1; if($pageno>0){$pre_page = $pageno - 1;}else{$pre_page=0;}  	}     else { $pageno = 0;      }

/***************************** search ******************************/
if (isset($_GET['q'])) {$search = urlencode($_GET['q']); $placeholder= $_GET['q']; } else{$search ="game"; $placeholder =''; }
 
/****************************Sort by ******************************/
if (isset($_GET['orderby'])){ $sort_by = $_GET['orderby']; $orderby= $_GET['orderby']; } else { $sort_by = '99'; $orderby=99; }
$orderby ="99";

/************************** completed URL ************************/
    $url = "https://thepiratebay.org/search/".$search."/".$pageno."/".$sort_by."/".$main_filters."/";
    //echo $url;
    
/************************** current_url *************************/
    $actual_link = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    $parsed = parse_url($actual_link);
    $php_link = $parsed['scheme'].'://'.$parsed['host'].$parsed['path'];
    if (strpos($actual_link, '?') !== false) { // check if get parameter request is done or not
                                $query = $parsed['query'];
                                if(!empty($query)){
                                                    parse_str($query, $params); // for sort
                                                    parse_str($query, $params2); // for page no
                                                    $params2['page'] = $next_page; 
                                                    $params3['page'] = $pre_page;
                                                    unset($params['orderby']); 
                                                    $string = '?'.http_build_query($params); }else { $string ="?"; }
                                                    $string_next = '?'.http_build_query($params2); 
                                                    $string_pre = '?'.http_build_query($params3);} 
        else { $string ="?"; $string_next ="?page=1";$string_pre ="?page=0"; }
/****************************** Curl data ***************************************/
 $page = file_get_contents($url);
    preg_match_all('/<div class="detName">.*?<a href="(.*?)" class="detLink" title=".*?">(.*?)<\/a>\n<\/div>\n<a href="(.*?)" title="Download this torrent using magnet"><img src=".*?" alt="Magnet link" \/><\/a>.*?<img src=".*?>\n.*?<font class="detDesc">Uploaded (.*?), Size (.*?), ULed by <a class="detDesc" href=".*?" title="Browse .*?">(.*?)<\/a><\/font>\n.*?<\/td>\n.*?<td align="right">(.*?)<\/td>\n.*?<td align="right">(.*?)<\/td>/', $page, $links);
    $result = [];
    for ($i = 0; $i < count($links[0]); $i++) {
        preg_match('/\/torrent\/(.*?)\//', $links[1][$i], $id);
        $id = $id[1];
        $size = str_replace('&nbsp;', '', $links[5][$i]);
        $size = str_replace('MiB', 'MB', $size);
        $size = str_replace('GiB', 'GB', $size);
        $size = str_replace('KiB', 'KB', $size);
        $added = str_replace('&nbsp;', ' ', $links[4][$i]);
        $added = str_replace(' ', '-', $added);
        $result[] = ['id' => (int) $id, 'title' => $links[2][$i], 'detail_url' => 'https://thepiratebay.org'.$links[1][$i], 'author' => $links[6][$i], 'size' => $size, 'magnet' => $links[3][$i], 'seeders' => (int) $links[7][$i], 'leechers' => (int) $links[8][$i], 'added' => $added];
    }
    if (empty($result)) {
        $result2 = '{"error":"nothing found"}';
        
    }
      $result2 = ($result);

?>

<!DOCTYPE html>
<html>
<head>
<title>The P_bay</title>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- below for popup details -->
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<!-- magnet link popoup data -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
            <style> .sweet-alert h2 {     color: #575757;    font-size: 15px;    text-align: center; line-height: normal;    display: block; }</style>
<script>
function setAll()
{
	document.forms['q'].elements['audio'].checked = false;
	document.forms['q'].elements['video'].checked = false;
	document.forms['q'].elements['apps'].checked = false;
	document.forms['q'].elements['games'].checked = false;
	document.forms['q'].elements['porn'].checked = false;
	document.forms['q'].elements['other'].checked = false;
}

function rmAll() { document.forms['q'].elements['all'].checked = false; }
</script>
</head>
<body>
<form method="get" id="q" action="">

            <input type="search" title="Pirate Search" name="q" required placeholder="Search here..." value="<?php echo $placeholder; ?>" style="background-color:#ffffe0;" class="searchBox" /><input value="Pirate Search" type="submit" class="submitbutton"  />  <br />
            <label for="audio" title="Audio"><input id="audio" name="audio" onclick="javascript:rmAll();" type="checkbox"/>Audio</label>
            <label for="video" title="Video"><input id="video" name="video" onclick="javascript:rmAll();" type="checkbox"/>Video</label>
            <label for="apps" title="Applications"><input id="apps" name="apps" onclick="javascript:rmAll();" type="checkbox"/>Applications</label>
            <label for="games" title="Games"><input id="games" name="games" onclick="javascript:rmAll();" type="checkbox"/>Games</label>
            <label for="porn" title="Porn"><input id="porn" name="porn" onclick="javascript:rmAll();" type="checkbox"/>Po</label>
            <label for="other" title="Other"><input id="other" name="other" onclick="javascript:rmAll();" type="checkbox"/>Other</label>

            <select id="category" name="category" onchange="javascript:setAll();">
                <option value="0">All</option>
                <optgroup label="Audio">
                    <option value="101">Music</option>
                    <option value="102">Audio books</option>
                    <option value="103">Sound clips</option>
                    <option value="104">FLAC</option>
                    <option value="199">Other</option>
                </optgroup>
                <optgroup label="Video">
                    <option value="201">Movies</option>
                    <option value="202">Movies DVDR</option>
                    <option value="203">Music videos</option>
                    <option value="204">Movie clips</option>
                    <option value="205">TV shows</option>
                    <option value="206">Handheld</option>
                    <option value="207">HD - Movies</option>
                    <option value="208">HD - TV shows</option>
                    <option value="209">3D</option>
                    <option value="299">Other</option>
                </optgroup>
                <optgroup label="Applications">
                    <option value="301">Windows</option>
                    <option value="302">Mac</option>
                    <option value="303">UNIX</option>
                    <option value="304">Handheld</option>
                    <option value="305">IOS (iPad/iPhone)</option>
                    <option value="306">Android</option>
                    <option value="399">Other OS</option>
                </optgroup>
                <optgroup label="Games">
                    <option value="401">PC</option>
                    <option value="402">Mac</option>
                    <option value="403">PSx</option>
                    <option value="404">XBOX360</option>
                    <option value="405">Wii</option>
                    <option value="406">Handheld</option>
                    <option value="407">IOS (iPad/iPhone)</option>
                    <option value="408">Android</option>
                    <option value="499">Other</option>
                </optgroup>
                <optgroup label="Porn">
                    <option value="501">Movies</option>
                    <option value="502">Movies DVDR</option>
                    <option value="503">Pictures</option>
                    <option value="504">Games</option>
                    <option value="505">HD - Movies</option>
                    <option value="506">Movie clips</option>
                    <option value="599">Other</option>
                </optgroup>
                <optgroup label="Other">
                    <option value="601">E-books</option>
                    <option value="602">Comics</option>
                    <option value="603">Pictures</option>
                    <option value="604">Covers</option>
                    <option value="605">Physibles</option>
                    <option value="699">Other</option>
                </optgroup>
            </select>

            <input type="hidden" name="page" value="0" />
            <input type="hidden" name="orderby" value="99" />
        </form>
        <input type="button" onclick="window.location.href = '<?php echo $php_link; ?>';" value="Refresh"/> 
        <input type="button" onclick="window.location.href = '<?php echo $string_pre; ?>';" value="Previous Page"/> 
        <input type="button" onclick="window.location.href = '<?php echo $string_next; ?>';" value="Next Page"/> 
        
<table class="rwd-table">
  <tr>
    <th>SN</th>
    <th>Name<a href="<?php echo $string; ?>&orderby=1" style="color: #FFFF99;">&#9660;</a><a href="<?php echo $string; ?>&orderby=2" style="color: #FFFF99;">&#9650;</a></th>
    <th>Size<a href="<?php echo $string; ?>&orderby=5" style="color: #FFFF99;">&#9660;</a><a href="<?php echo $string; ?>&orderby=6" style="color: #FFFF99;">&#9650;</a></th>
    <th>Seeds<a href="<?php echo $string; ?>&orderby=7" style="color: #FFFF99;">&#9660;</a><a href="<?php echo $string; ?>&orderby=8" style="color: #FFFF99;">&#9650;</a></th>
    <th>Leechs<a href="<?php echo $string; ?>&orderby=9" style="color: #FFFF99;">&#9660;</a><a href="<?php echo $string; ?>&orderby=10" style="color: #FFFF99;">&#9650;</a></th>
    <th>Author<a href="<?php echo $string; ?>&orderby=11" style="color: #FFFF99;">&#9660;</a><a href="<?php echo $string; ?>&orderby=12" style="color: #FFFF99;">&#9650;</a></th>
    <th>Added<a href="<?php echo $string; ?>&orderby=3" style="color: #FFFF99;">&#9660;</a><a href="<?php echo $string; ?>&orderby=4" style="color: #FFFF99;">&#9650;</a></th>
  </tr>


<?php
    $count = count($result); //echo $count;

    $start = '<a class=" btn btn-danger" onClick="loadiframe(';
    $end = ')" data-toggle="modal" data-target="#myModal">';//<!--link to iframe-->

    for ($x = 0; $x < $count; $x++) {
        $sn=$x+1;
        $sn=$pageno*30+$sn;
        $files = "'files.php?id=".$result2[$x]['id']."'";
        $comments = "'files.php?comments=1&url=".$result2[$x]['id'].$result2[$x]['title']."'";
        $info = "'files.php?info=1&url=".$result2[$x]['id'].$result2[$x]['title']."'";
        $magnet = "<a href='".$result2[$x]['magnet']."'>";
        $magnet1 = "JSalert('".$result2[$x]['magnet']."')"; //work around because already used two type of commas
    
                echo "<tr> <td data-th='SN'>".$sn."</td> <td data-th='Name'><p onclick=".$magnet1.">".$result2[$x]['title']."</p>";
                echo $start.$info.$end;
                echo"<i class='fa' style='font-size:24px;color:red'>&#9432;</i></a>"; //info
                echo $start.$files.$end;
                echo"<i class='fa' style='font-size:24px;color:red'>&#xf15c;</i></a>"; //files logo
                echo $start.$comments.$end;
                echo"<i class='fa fa-comments-o' style='font-size:24px;color:red'></i></a>"; //comments logo
                echo $magnet;
                echo"<i class='fa fa-download' style='font-size:24px;color:red'></i></a></td>"; //download logo
                echo"<td data-th='Size'>".$result2[$x]['size']."</td> <td data-th='Seeds'>".$result2[$x]['seeders']."</td>
                    <td data-th='leechs'>".$result2[$x]['leechers']."</td>
                    <td data-th='Author'>".$result2[$x]['author']."</td>
                    <td data-th='Added'>".$result2[$x]['added']."</td> </tr>";
        }
        echo "</table>";
?>

<!-- popup work start from here-->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" onClick="unloadiframe()">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header" style="border:hidden">
       <button type="button" class="close" onClick="unloadiframe()" data-dismiss="modal" aria-label="Close"><span aria-   hidden="true">&times; </span>&#10060;</button>
      </div>

      <div class="modal-body" style="padding-top:10px; padding-left:5px; padding-right:0px; padding-bottom:0px;">
      <iframe src="" frameborder="0" id="targetiframe" class="iframe-placeholder" style=" height:500px; width:100%;" name="targetframe" allowtransparency="true"></iframe> <!-- target iframe -->
      </div> <!--modal-body-->
      
      <div class="modal-footer" style="margin-top:0px;">
        <button type="button" class="btn btn-default pull-right" data-dismiss="modal" onClick="unloadiframe()">close</button>
      </div>
      
    </div>
  </div>
</div>
<!-- popup work end here-->
        <input type="button" onclick="window.location.href = '<?php echo $php_link; ?>';" value="Refresh"/> 
        <input type="button" onclick="window.location.href = '<?php echo $string_pre; ?>';" value="Previous Page"/> 
        <input type="button" onclick="window.location.href = '<?php echo $string_next; ?>';" value="Next Page"/> 
<!-- popup script -->
<script>
function loadiframe(htmlHref) //load iframe src location
    {
        document.getElementById('targetiframe').src = htmlHref;
    }

function unloadiframe() //removes the Old frame data
    {
        // unload frame
        var frame = document.getElementById("targetiframe"),
        frameDoc = frame.contentDocument || frame.contentWindow.document;
        frameDoc.removeChild(frameDoc.documentElement);
    }
</script>
<!-- magnet popoup -->
<script type="text/javascript">
function JSalert(msg){
    var msg = msg
	swal(msg);
}
</script>
