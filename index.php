<?php
    // To start the session
    session_start();

    //importing files
    include_once("include/config.php");
    include_once("include/OAuth.php");
    include_once("include/TwitterAPIExchange.php");
    include_once("include/twitteroauth.php");
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Twitter RTCamp</title>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark py-4">
    <div class="container-fluid">
        <span class="navbar-brand h1"><i class="fa fa-twitter-square" style="font-size:48px"></i>Twitter RTCamp</span>
        <a href="logout.php"><button class="logoutBtn">Logout</button></a>
    </div>
    </nav>
    <div id="content">
        <?php
            // ini_set("default_socket_timeout", 150);
            if (isset($_SESSION['status']) && $_SESSION['status'] === 'verified') {
                //if oauth is done

                //getting userid and tokens from session variables
                $screenname = $_SESSION['request_vars']['screen_name'];
                $userID = $_SESSION['request_vars']['user_id'];
                $oauth_token = $_SESSION['request_vars']['oauth_token'];
                $oauth_token_secret = $_SESSION['request_vars']['oauth_token_secret'];


                if(isset($_REQUEST['screenname']))
                    $screenname = $_GET['screenname'];

                $settings = array(
                    'oauth_access_token' => OAUTH_ACCESS_TOKEN,
                    'oauth_access_token_secret' => OAUTH_ACCESS_TOKEN_SECRET,
                    'consumer_key' => COSUMER_KEY,
                    'consumer_secret' => COSUMER_SECRET
                );

                $userinfo = "https://api.twitter.com/1.1/users/show.json";
                $followers = "https://api.twitter.com/1.1/followers/list.json";
                $tweets = "https://api.twitter.com/1.1/statuses/home_timeline.json";

                $getMethod = 'GET';
                $postMethod = 'POST';

                $getfield = '?screen_name='.$screenname;
                
                // * User Info
                $twitter = new TwitterAPIExchange($settings);
                $twitter->setGetfield($getfield)
                        ->buildOauth($userinfo, $getMethod)
                        ->performRequest();

                $userinfoRes = json_decode($twitter->setGetfield($getfield)
                                                    ->buildOauth($userinfo, $getMethod)
                                                    ->performRequest(), $assoc = TRUE);
                

                // * Tweets
                $twitter = new TwitterAPIExchange($settings);
                $twitter->setGetfield($getfield)
                        ->buildOauth($tweets, $getMethod)
                        ->performRequest();

                $tweetsRes = json_decode($twitter->setGetfield($getfield)
                                                ->buildOauth($tweets, $getMethod)
                                                ->performRequest(), $assoc = TRUE);
                
                
                $profilepic = $userinfoRes['profile_image_url'];

                // Display

                // profile pic and welcome text
                echo '<img class="profilepic" src='.$profilepic.'></img>';
                echo '<b>📢 @'.$screenname.'</b>';

                //user info
                
                echo '<h6>User Information</h6>';
                echo '😀Name: '.$userinfoRes['name'];
                echo '<br> 🚩Followers: '.$userinfoRes['followers_count'];
                echo '<br> ✔️Following: '.$userinfoRes['friends_count'];
                echo '<br> 🖊️Tweets: '.$userinfoRes['statuses_count'];
                echo '<hr>';


                echo "<h1 class='title'> Recent Feed </h1>";
                // display tweets
                foreach($tweetsRes as $tweet){
                    $profilepic = $tweet['user']['profile_image_url'];
                    echo "<img class='profilepic' src=".$profilepic."></img>";
                    echo "@".$tweet['user']['screen_name']."<br>";
                    echo $tweet['text']."<br>";
                    echo "<hr>";
                }

                //followers
                $twitter = new TwitterAPIExchange($settings);
                $twitter->setGetfield($getfield)
                        ->buildOauth($followers, $getMethod)
                        ->performRequest();

                $followersres = json_decode($twitter->setGetfield($getfield)
                                                    ->buildOauth($followers, $getMethod)
                                                    ->performRequest(), $assoc = TRUE);
                
                echo "<h4>Followers</h4>";
                foreach($followersres['users'] as $follower){
                    $profilepic = $follower['profile_image_url'];
                    echo "<img class='profilepic' src=".$profilepic."></img>";
                    echo "<a href='index.php?screenname=".$follower['screen_name']."'>@".$follower['screen_name']."</a><br>";
                    echo "<hr>";
                }


                echo "<a href='download.php?screenname=".$screenname."&type=xls'><button class='btnDownload'>Download Followers in Excel</button></a>";
                echo "<a href='download.php?screenname=".$screenname."&type=pdf'><button class='btnDownload'>Download Followers in PDF</button></a>";
            } else {
                //Login
                echo '<a href="process.php"><button class="loginBtn">Log In With Twitter</button></a>';
            }
            
        ?>

    </div>
</body>
</html>