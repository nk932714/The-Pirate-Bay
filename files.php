<?php

if (isset($_GET['id'])) {
            $url= "https://thepiratebay.org/ajax_details_filelist.php?id=".$_GET['id']; //for files list
            $raw_data = file_get_contents($url);
            //header("Content-Type: text/plain");
            echo $raw_data;
    
}


if (isset($_GET['url'])) {
                    $url = "https://thepiratebay.org/torrent/".$_GET['url'];
                    $raw_data = file_get_contents($url);
                    
                    if (isset($_GET['comments'])) {
                                    $re = '/<div id="commentsheader" class="comments">(.*?)<\/div><\/div>            <\/div>/ms';
                                    $msg = "Sorry no comments found";
                                }
                    if (isset($_GET['info'])) {
                                $re = '/<div class="nfo">(.*?)<div class="download">/ms';
                                $msg = "Sorry no description found";
                                }
                    $count = preg_match_all($re, $raw_data, $matches);
                    if($count!==0){ echo $matches[1][0]; }
                    else  echo $msg;
}

?>
