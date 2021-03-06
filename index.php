<?php
require ('inc/head.php');
?>

<title>SNOMED CT GP/FP RefSet Field Test - Home</title>
</head>
<body>

<?php
// check for login post
if ($_POST["loginEmail"] && $_POST["loginPassword"]) {
    $sql = sprintf("SELECT * FROM Users WHERE email='%s'", mysql_real_escape_string($_POST[loginEmail]));
    $rows = mysql_query($sql) or die(mysql_error());

    if (mysql_num_rows($rows) == 1) {
        $user = mysql_fetch_array($rows);
        if ($user["password"] == md5($_POST["loginPassword"])) {
            if ($user["verified"]) {
                $tRows = mysql_query("SELECT * FROM Title WHERE title_id='$user[title_id]'") or die(mysql_error());
                $uTitle = mysql_fetch_array($tRows);
                $_SESSION["title"] = $uTitle["title"];
                $_SESSION["first_name"] = $user["first_name"];
                $_SESSION["last_name"] = $user["last_name"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["option"] = $user["option_id"];

                $_SESSION["logged"] = true;
				$_SESSION['last_activity'] = time(); //your last activity was now, having logged in.
				$_SESSION['expire_time'] = 45*60; //expire time in seconds

            } else {
                $message = '<div class="alert">You have not yet verified your email address.<br>Please retrieve the verification code from the email you should by now have received, and enter it <a href="verify.php">here</a>.</div>';
            }
        } else {
            $message = '<div class="alert alert-error">Password incorrect</div>';
        }
    } else {
        $message = '<div class="alert alert-error">Email address not recognised</div>';
    }
    // echo $message;
}

?>

  <div class="container">
    <?php
    require ('inc/header.php');
 ?>
    <div class="main clearfix">
<?php
if($message){
  echo $message;
}
if(!$_SESSION["logged"]){
?>
      <div class="row">
        <div class="span12">
          <div class="hero-unit">
            <h2>GP/FP SNOMED CT RefSet and ICPC-2 mapping project - Field Test</h2>
            <p>Welcome to the Field Test of the GP/FP SNOMED CT RefSet and map to ICPC</p>
            <p>Clinical input is needed to ensure that both the GP/FP RefSet and map to ICPC-2 are of the highest quality possible. The involvement of many GPs/FPs from multiple countries will ensure that both products are acceptable for future implementation at a local level in electronic health records. </p>
            <p>Thank you for your participation.</p>    
            <p>Instructions for completing the Field Test are available <a href="userGuide.php" target="_blank"> in the User Guide</a></p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="span4 offset1">
          <h2>New users</h2>
          <p><a class="btn" href="register.php">Click here to register</a></p>
        </div>
        <div class="span4 offset1">
          <h2>Returning users</h2>
          <form method="post" action="index.php" class="form-horizontal" id="loginForm" name="loginForm" data-validate="parsley">
            <fieldset>
              <div class="control-group">
                <label class="control-label" for="loginEmail">Email</label>
                <div class="controls">
                  <input type="text" name="loginEmail" id="loginEmail" placeholder="Email" data-trigger="change" data-required="true" data-type="email">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="loginPassword">Password</label>
                <div class="controls">
                  <input type="password" name="loginPassword" id="loginPassword" placeholder="Password" data-trigger="change" data-required="true">
                </div>
              </div>
              <div class="control-group">
                <div class="controls">
                  <a href="forgot-password.php">Forgotten your password?</a>
                </div>
              </div>
              <div class="control-group">
                <div class="controls">
                  <button type="submit" class="btn">Sign in</button>
                </div>
              </div>
            </fieldset>
          </form>
        </div>
      </div>
<?php
} else {
/*$_SESSION ["encounter_id"] = null;
$_SESSION ["add_mode"] = null;*/
$rows = mysql_query("SELECT * FROM Users U, TestApproach T WHERE user_id='$_SESSION[user_id]' and T.option_id = U.option_id") or die(mysql_error());
$user = mysql_fetch_array($rows);

switch ($user["option_id"]) {
case 1:
$option_label="selecting via SNOMED CT Concepts";
break;
case 2:
$option_label="selecting via ICPC-2 Codes";
break;
case 3:
$option_label="verifying the SNOMED CT refset members";
break;
}

if (!$_SESSION["completed_encs"]) {
    $encountersData = mysql_query("SELECT MAX(user_encounter_id) AS user_encounter_id FROM Encounters WHERE user_id='$_SESSION[user_id]' AND complete='1' AND active='y'") or die(mysql_error());
    
    while($encounters = mysql_fetch_assoc($encountersData)) {
            $_SESSION["completed_encs"] = $encounters["user_encounter_id"];
            $log -> user("encounters completed is - '$_SESSION[completed_encs]'");
    }
}
$encountersData = mysql_query("SELECT * FROM Encounters WHERE user_id='$_SESSION[user_id]' AND complete='1' AND active='y'") or die(mysql_error());
$encountersCompleted = mysql_num_rows($encountersData);

?>
      <div class="page-header">
        <h1>Home</h1>
      </div>
      <div class="row">
        <div class="span12">
          <div class="well">
            <p class="lead">Welcome, <?= ($_SESSION['title'] !== 'Other' ? $_SESSION['title'] . ' ' : ''); ?><?= $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] ?>.</p>
            <p>You are participating in this field test by the <strong><?= $user["option_label"]; ?></strong> option.</p>
            <p>You have completed <?= $encountersCompleted; ?> of <?= $configvars["encounters"]["maxencounters"]; ?> encounters.</p>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="span4 offset4">
          <ul class="unstyled bigButtons">
   
    <?php       
            if ($encountersCompleted > 0) {
            ?>
                <li><a class="btn btn-large btn-block btn-primary" href="encounters.php">View Patient Encounters</a></li>
              <?php
            }
            ?>
          </ul>
        </div>
      </div>

<?php
}
?>
    </div>
    <?php
        require ('inc/footer.php');
 ?>
  </div>

<?php
    require ('inc/script.php');
 ?>
</body>
</html>