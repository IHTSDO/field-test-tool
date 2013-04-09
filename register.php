<?php include "inc/conn.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php require('inc/head.php'); ?>

  <title>SNOMED CT GP/FP RefSet Field Test - New user registration</title>
</head>
<body>
  <div class="container">
    <?php require('inc/header.php'); ?>
    <div class="main clearfix">

      <div class="page-header">
        <h1>Participant registration</h1>
      </div>

      <div class="row">
        <div class="span8 offset2">
          <p><span class="req">*</span> Required</p>
          <form method="post" action="verify.php" class="form-horizontal" id="registrationForm" name="registrationForm" data-validate="parsley">
            <fieldset>
              <div class="control-group">
                <label class="control-label" for="regTitle"><span class="req">*</span> Title</label>
                <div class="controls">
                  <select class="input-xlarge" id="regTitle" name="regTitle" data-required="true">
                    <option value="">Please select</option>
                    <option value="Mr.">Mr.</option>
                    <option value="Mrs.">Mrs.</option>
                    <option value="Ms.">Ms.</option>
                    <option value="Miss">Miss</option>
                    <option value="Dr.">Dr.</option>
                    <option value="Prof.">Prof.</option>
                    <option value="Rev.">Rev.</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="regFirstname"><span class="req">*</span> First name</label>
                <div class="controls">
                  <input type="text" class="input-xlarge" maxlength="30" id="regFirstname" name="regFirstname" placeholder="First name" data-trigger="change" data-required="true">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="regLastname"><span class="req">*</span> Last name</label>
                <div class="controls">
                  <input type="text" class="input-xlarge" maxlength="30" id="regLastname" name="regLastname" placeholder="Last name" data-trigger="change" data-required="true">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="regEmail"><span class="req">*</span> Email address</label>
                <div class="controls">
                  <input type="text" class="input-xlarge" maxlength="50" id="regEmail" name="regEmail" placeholder="Email address" data-trigger="change" data-required="true" data-type="email">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="regPassword"><span class="req">*</span> Password</label>
                <div class="controls">
                  <input type="password" class="input-xlarge" id="regPassword" name="regPassword" placeholder="Password" data-trigger="change" data-required="true" data-minlength="6"><span class="help-inline">Minimum 6 characters</span>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="regConfirmpassword"><span class="req">*</span> Confirm password</label>
                <div class="controls">
                  <input type="password" class="input-xlarge" id="regConfirmpassword" name="regConfirmpassword" placeholder="Confirm password" data-trigger="change" data-required="true" data-minlength="6" data-equalto="#regPassword">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="regCountry"><span class="req">*</span> Country</label>
                <div class="controls">
                  <select class="input-xlarge" id="regCountry" name="regCountry" data-required="true">
                    <option value="">Please select</option>
                    <?php require('inc/countries.php'); ?>
                  </select>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="regRole"><span class="req">*</span> Main role/occupation</label>
                <div class="controls">
                  <input type="text" class="input-xlarge" maxlength="100" id="regRole" name="regRole" placeholder="Main role/occupation" data-required="true">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="regGender"><span class="req">*</span> Gender</label>
                <div class="controls">
                  <select class="input-xlarge" id="regGender" name="regGender" data-required="true">
                    <option value="">Please select</option>
                    <?php
                    $result = mysql_query("SELECT * FROM Gender") or die(mysql_error());
                    while($row = mysql_fetch_array($result)){
                      ?>
                      <option value="<?=$row['gender_id'];?>"><?=$row['gender'];?></option>
                      <?php
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="regAge"><span class="req">*</span> Age</label>
                <div class="controls">
                  <input type="text" class="input-xlarge" id="regAge" name="regAge" placeholder="Age" data-required="true">
                </div>
              </div>
              <div class="control-group">
                <div class="controls">
                  <input type="submit" class="btn" value="Register">
                </div>
              </div>
            </fieldset>
          </form>
        </div>
      </div>

    </div>
    <?php require('inc/footer.php'); ?>
  </div>

<?php require('inc/script.php'); ?>
</body>
</html>