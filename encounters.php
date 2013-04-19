<?php include "inc/conn.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php require('inc/head.php'); ?>

  <title>SNOMED CT GP/FP RefSet Field Test - Encounters</title>
</head>
<body>
  <div class="container">
    <?php require('inc/header.php'); ?>
    <div class="main clearfix">
      <div class="page-header">
        <h1>Encounters</h1>
      </div>
<?php
if(!$_SESSION["logged"]){
  include('inc/not-logged-in.php');
} else {
  $_SESSION["return_to"] = null;
?>
      <div class="row">
        <div class="span8 offset2">

          <div class="accordion encounters-list">
          <?php
            $_SESSION ["encounter_id"] = null;
            $_SESSION ["add_mode"] = null;
            $_SESSION ["rfe_id"] = null;
            $_SESSION ["label"] = null;

            $encountersData = mysql_query("SELECT * FROM Encounters WHERE user_id='$_SESSION[user_id]' AND complete='1'") or die(mysql_error());
            if(mysql_num_rows($encountersData)){
              while($row = mysql_fetch_array($encountersData)){
                ?>
                <div class="accordion-group">
                  <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" href="#collapse<?= $row['encounter_id']; ?>">
                      Encounter #<?= $row['encounter_id']; ?> - <?= ($row['label'] == '' ? '<em>No label given</em>' : $row['label']) ?>
                    </a>
                  </div>
                  <div class="accordion-body collapse" id="collapse<?= $row['encounter_id']; ?>">
                    <div class="accordion-inner">
                      <ul class="inline pull-right">
                        <li><button class="btn editlabelBtn" data-encounterid="<?= $row['encounter_id']; ?>" data-currentlabel="<?= $row['label']; ?>">Edit label for this encounter</button></li>
                      </ul>
                      <div class="itemsHolder clearboth clearfix" id="enc-<?= $row['encounter_id']; ?>">
                        <div class="spin"></div>
                        <p>Fetching items...</p>
                      </div>
                      <form action="review-encounter.php" method="post">
                        <input type="hidden" id="enc" name="enc" value="<?= $row['encounter_id']; ?>">
                        <ul class="inline pull-right">
                          <li><button type="submit" class="btn">Edit this encounter</button></li>
                        </ul>
                      </form>
                    </div>
                  </div>
                </div>
                <?php
              }
            } else {
              ?>
              <p class="lead">You have not yet completed any encounters.</p>
              <a href="add-item.php" class="btn">Add encounter</a>
              <?php
            }
          ?>
          </div>

        </div>
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