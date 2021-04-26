<?php
    // To start the session
    session_start();

    //importing files
    include_once("include/config.php");
    include_once("include/OAuth.php");
    include_once("include/TwitterAPIExchange.php");
    include_once("include/twitteroauth.php");

    if (isset($_REQUEST['oauth_token']) && $_SESSION['token'] !== $_REQUEST['oauth_token']) {
        //checking if tokens match or not.. if not destroy session as session became old
        session_destroy();
        header('Location: ./index.php');
    }
    elseif(isset($_REQUEST['oauth_token']) && $_SESSION['token'] == $_REQUEST['oauth_token']){
        //if tokens match authrizing it and redirecting ot index.php
        $conn = new TwitterOAuth(COSUMER_KEY, COSUMER_SECRET, $_SESSION['token'], $_SESSION['token_secret']);
        $access_token = $conn->getAccessToken($_REQUEST['oauth_verifier']);

        if ($conn->http_code == '200') {
            //if auth succesfull -> setting session variables
            $_SESSION['status'] = 'verified';
            $_SESSION['request_vars'] = $access_token;
            //removing previous tokens
            unset($_SESSION['token']);
            unset($_SESSION['token_secret']);
            header('Location: ./index.php');
        } else {
            die("Error Occured");
        }
    }

    else{
        if (isset($_GET['denied'])) {
            //if value associated with GET is invalid
            header('Location: ./index.php');
            die();
        }

        //Connecting to twitter and gettinh oauth tokens
        $conn = new TwitterOAuth(COSUMER_KEY, COSUMER_SECRET);
        $request_token = $conn->getRequestToken();

        //storing tokens to session variable
        $_SESSION['token'] = $request_token['oauth_token'];
        $_SESSION['token_secret'] = $request_token['oauth_token_secret'];

        if ($conn->http_code == '200') {
            //if success giving control to twitter to authorize
            $twitter_url = $conn->getAuthorizeURL($request_token['oauth_token']);
            header('Location: '.$twitter_url);
        } else {
            die("Error Occured");
        }
        
    }
?>