<?php
    // To start the session
    session_start();

    //importing files
    include_once("include/config.php");
    include_once("include/OAuth.php");
    include_once("include/TwitterAPIExchange.php");
    include_once("include/twitteroauth.php");
    require('fpdf/fpdf.php');

    $followers = "https://api.twitter.com/1.1/followers/list.json";
    $screenname = $_REQUEST['screenname'];
    $getMethod = 'GET';

    $settings = array(
        'oauth_access_token' => OAUTH_ACCESS_TOKEN,
        'oauth_access_token_secret' => OAUTH_ACCESS_TOKEN_SECRET,
        'consumer_key' => COSUMER_KEY,
        'consumer_secret' => COSUMER_SECRET
    );

    $getfield = '?screen_name='.$screenname;
    $twitter = new TwitterAPIExchange($settings);
                $twitter->setGetfield($getfield)
                        ->buildOauth($followers, $getMethod)
                        ->performRequest();

    $followersres = json_decode($twitter->setGetfield($getfield)
                                        ->buildOauth($followers, $getMethod)
                                        ->performRequest(), $assoc = TRUE);

    $download = array();
    foreach($followersres['users'] as $follower){
        array_push($download, $follower['screen_name']);
    }
    if ($_REQUEST['type'] == 'xls') {
        header("Content-Disposition: attachment; filename=followers.xls");
        header("Content-Type: application/vnd.ms-excel;");
        header("Pragma: no-cache");
        header("Expires: 0");
        $out = fopen("php://output", 'w');
        foreach ($download as $data)
        {
            fwrite($out, "@".$data.PHP_EOL);
        }
        fclose($out);
    } else {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(40,10,'Followers of @'.$screenname);

        $pdf->Ln(10);
        foreach($download as $data){
            $pdf->Cell(40,10,"@".$data);
            $pdf->Ln(10);
        }
        $pdf->Output();
    }
?>