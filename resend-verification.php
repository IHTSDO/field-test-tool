<?php include "inc/conn.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php require('inc/head.php'); ?>

  <title>SNOMED CT GP/FP RefSet Field Test - Resend verification</title>
</head>
<body>
  <div class="container">
    <?php require('inc/header.php'); ?>
    <div class="main clearfix">

      <div class="page-header">
        <h1>Resend verification</h1>
      </div>
<?php
if($_SESSION["logged"]){
  include('inc/already-logged-in.php');
} else {
?>
      <div class="row">
        <div class="span12">
          <div class="well">
            <p class="lead">Please enter your email address below to receive a reminder of your verification code.</p>
          </div>
        </div>
      </div>

      <div class="row span8 offset2">
        <form class="form-horizontal" id="resendVerification" name="resendVerification" method="post" action="xxx.php" data-validate="parsley">
          <fieldset>
            <div class="control-group">
              <label class="control-label" for="resendEmail">Email address</label>
              <div class="controls">
                <input type="text" id="resendEmail" name="resendEmail" placeholder="Email address" data-trigger="change" data-required="true" data-type="email">
              </div>
            </div>
            <div class="control-group">
              <div class="controls">
                <input type="submit" class="btn" value="Go">
              </div>
            </div>
            <div class="control-group">
              <div class="controls">
                <p><a href="verify.php">Enter verification code</a></p>
              </div>
            </div>
          </fieldset>
        </form>
      </div>
<?php
}
?>
    </div>
    <?php require('inc/footer.php'); ?>
  </div>

<?php require('inc/script.php'); ?>
</body>
</html>