<?php
require ('inc/head.php');
require ('inc/conn.php');

if (!is_null($_GET["type"]) && !is_null($_GET["enc"])) {// came from the review page

    error_log("I am in here - the came from review page");

    $_SESSION["add_mode"] = $_GET["type"];
    $recordType = ($_GET["type"] == 0 ? "Reason For Encounter" : "Health Issue");
    $_SESSION["encounter_id"] = $_GET["enc"];

    $returnTo = "review-encounter.php";

} else {
    error_log("I am in here - the first if else place");
    error_log("debugflag - encounter_id var is - ");
    error_log($_SESSION["encounter_id"]);

    if ($_SESSION["logged"]) {
        error_log("I am in here");

        if ($_SESSION["option"] == 3) {// this is for refset verification only

            if ($_POST["conceptsDropdown"] || $_POST["conceptRepresentation"]) {

                if (!$_SESSION["encounter_id"]) {// no encounter id, so create new encounter and new RFE
                    error_log("I am in here - no encounter id, so create new encounter and new RFE");

                    $sql = "INSERT INTO Encounters (user_id) VALUES ('$_SESSION[user_id]')";
                    mysql_query($sql) or die(mysql_error());
                    $_SESSION["encounter_id"] = mysql_insert_id();

                    $_SESSION["add_mode"] = 0;
                    $recordType = "Reason For Encounter";

                    error_log("debugflag - encounter_id var after newly set is - ");
                    error_log($_SESSION["encounter_id"]);
                    error_log("debugflag - recordType var after newly set is - ");
                    error_log($recordType);
                    error_log("debugflag - encounter details refid var after newly set is - ");
                    error_log($_SESSION["rfe_id"]);

                }
                
                $sql = sprintf("INSERT INTO Encounter_Reasons (encounter_id, refset_id, sct_id, sct_scale, sct_alt) 
                                        VALUES ('$_SESSION[encounter_id]', '$_SESSION[add_mode]', '$_POST[conceptsDropdown]', '$_POST[conceptRepresentation]', '%s')", mysql_real_escape_string($_POST["conceptFreeText"]));

                error_log($sql);

                mysql_query($sql) or die(mysql_error());
                $message = '<div class="alert alert-success">' . ($_SESSION["add_mode"] == 0 ? "RFE" : "Health Issue") . ' successfully recorded.</div>';

                if ($_POST["addAnother"] == "false") {
                    $_SESSION["add_mode"] = 1;
                }

                $recordType = ($_SESSION["add_mode"] == 0 ? "Reason For Encounter" : "Health Issue");

            } else {
                $message = '<div class="alert alert-error" id="errorMsg" name="errorMsg">There was an error - Please ensure the relevant fields are populated.</div>';
            }

        } else {// this is for mapping verification

            // debug notices
            error_log("I am in here - option 1 & 2 verification");
            error_log("debugflag - concepts dropdown after newly set is - '$_POST[conceptsDropdown]'");
            error_log("debugflag - concepts alt text after newly set is - '$_POST[conceptFreeText]'");
            error_log("debugflag - icpc after newly set is - '$_POST[icpc2]'");
            error_log("debugflag - icpc alt text after newly set is - '$_POST[icpcDropdown]'");

            if (($_POST["conceptsDropdown"] || $_POST["conceptFreeText"]) && ($_POST["icpc2"] || $_POST["icpcDropdown"])) {// all mandatory fields posted

                if (!$_SESSION["encounter_id"]) {// no encounter id, so create new encounter and new RFE
                    error_log("I am in option 1&2 and the fields are populated - no encounter id, so create new encounter and new RFE");

                    $sql = "INSERT INTO Encounters (user_id) VALUES ('$_SESSION[user_id]')";
                    mysql_query($sql) or die(mysql_error());
                    $_SESSION["encounter_id"] = mysql_insert_id();

                    $_SESSION["add_mode"] = 0;
                    $recordType = "Reason For Encounter";

                    error_log("debugflag - encounter_id var after newly set is - ");
                    error_log($_SESSION["encounter_id"]);
                    error_log("debugflag - recordType var after newly set is - ");
                    error_log($recordType);
                    error_log("debugflag - encounter details refid var after newly set is - ");
                    error_log($_SESSION["rfe_id"]);

                }
                $icpcfield = ($_SESSION["option"] == 1 ? $_POST["icpc2"] : $_POST["icpcDropdown"] );
                $icpcAltfield = ($_SESSION["option"] == 1 ? $_POST["icpcDropdown"] : "" );
                error_log("icpcfield is '$icpcfield' and icpcAltfield is '$icpcAltfield'");
                
                
                $sql = sprintf("INSERT INTO Encounter_Reasons (encounter_id, refset_id, sct_id, sct_scale, sct_alt, icpc_id, icpc_scale, icpc_alt_id) 
                                        VALUES ('$_SESSION[encounter_id]', '$_SESSION[add_mode]', '$_POST[conceptsDropdown]', '$_POST[conceptRepresentation]', '%s', '$icpcfield',
                                        '$_POST[icpc2appropriate]','$icpcAltfield')", mysql_real_escape_string($_POST["conceptFreeText"]));

                error_log($sql);
                mysql_query($sql) or die(mysql_error());

                $message = '<div class="alert alert-success">' . ($_SESSION["add_mode"] == 0 ? "RFE" : "Health Issue") . ' successfully recorded.</div>';

                error_log("debugflag - addAnother var is - " + $_POST["addAnother"]);
                error_log("debugflag - add_mode var is - " + $_SESSION["add_mode"]);

                if ($_POST["addAnother"] == "false") {
                    $_SESSION["add_mode"] = 1;
                }

                $recordType = ($_SESSION["add_mode"] == 0 ? "Reason For Encounter" : "Health Issue");
            } else// if((($_POST["conceptsDropdown"] || $_POST["conceptFreeText"]) && ($_POST["icpc2"] || $_POST["icpc2appropriate"])) )
            {

                if (!$_SESSION["encounter_id"]) {
                    $recordType = ($_SESSION["add_mode"] == 0 ? "Reason For Encounter" : "Health Issue");
                } else {
                    $message = '<div class="alert alert-error" id="errorMsg" name="errorMsg">There was an error - RFE/Health Issue was not recorded. Please ensure the relevant fields are populated.</div>';
                }
            }
        } //endif of checking the approach option
    }//endif of is logged in and page origin check

    $returnTo = "add-item.php";
}
  ?>
  
  
 <title>SNOMED CT GP/FP RefSet Field Test - Add <?= $recordType; ?></title>
</head>
<body>
  <div class="container">
    <?php
    require ('inc/header.php');
 ?>
    <div class="main clearfix">

      <div class="page-header well">
        <h1>Add <?= $recordType; ?></h1>
        <?php if ($recordType == "Reason For Encounter") 
        { ?>
        <blockquote>
            A Reason For Encounter is "an agreed statement of the reason(s) why a person enters the health care system, representing the demand for care by that person. The terms written down and later classified by the provider clarify the reason for encounter and consequently the patient’s demand for care without interpreting it in the form of a diagnosis. The reason for encounter should be recognised by the patient as an acceptable description of the demand for care” 
            <small>(Wonca Dictionary of General/Family Practice, 2003).</small>
        </blockquote>
        <input type="hidden" id="refType" name="refType" value="0">

        <?php } else { ?>
        <blockquote>
            A Health Issue is an “issue related to the health of a subject of care, as identified or stated by a specific health care party”. This is further defined in the notes as “according to this definition, a health issue can correspond to a health problem, a disease, an illness”<br />
            <small>(Health informatics – System of concepts to support continuity of care – Part 1: basic concepts (CEN 13940-1))</small>
        </blockquote>
        <input type="hidden" id="refType" name="refType" value="1">
        <?php } ?>
		
      </div>
<?php
if(!$_SESSION["logged"]){
  include('inc/not-logged-in.php');
} else { 
  if($message){
    echo $message;
  }
?>

      <form method="post" action="<?= $returnTo; ?>" id="addItem" name="addItem" data-validate="parsley">
        <fieldset>

       <?php 
  			switch ($_SESSION["option"]) {
  			  case 1:
       ?> 
            
          <!-- for the SNOMED CT first option -->
            
          <div class="row">
            <div class="span8 offset2">
              <p>1. Search for (and select) a <strong>SNOMED CT</strong> concept that represents the <?= $recordType; ?> you wish to record.</p>
              <div class="input-append">
                <input id="searchBox" name="searchBox" type="text" maxlength="50">
                <button id="searchBtn" class="btn" type="button">Search</button>
              </div>
              <select class="input-xlarge" id="conceptsDropdown" name="conceptsDropdown" size="8" data-error-container="#conceptValidation" >
                <option value="">Select SNOMED concept</option>
                <?php /* require('inc/concepts.php');  */ ?> 
              </select>
              <button id="clearBtn" class="btn" type="button">Reset</button>
              <div id="conceptValidation"></div>
              <dl class="dl-horizontal synonyms">
                <dt>Synonyms:</dt>
                <dd></dd>
              </dl>
              <hr>
              <p>2. How well does this SNOMED CT concept adequately represent the <?= $recordType; ?> you wish to record?</p>
              <div class="likert">
                <label class="radio inline">
                  <span>1</span><input type="radio" name="conceptRepresentation" id="conceptRepresentation1" value="1" data-error-container="#representationValidation"><span>Very well</span>
                </label>
                <label class="radio inline">
                  <span>2</span><input type="radio" name="conceptRepresentation" id="conceptRepresentation2" value="2">
                </label>
                <label class="radio inline">
                  <span>3</span><input type="radio" name="conceptRepresentation" id="conceptRepresentation3" value="3">
                </label>
                <label class="radio inline">
                  <span>4</span><input type="radio" name="conceptRepresentation" id="conceptRepresentation4" value="4">
                </label>
                <label class="radio inline">
                  <span>5</span><input type="radio" name="conceptRepresentation" id="conceptRepresentation5" value="5"><span>Poorly</span>
                </label>
              </div>
              <div id="representationValidation"></div>
              <hr>
              <p>3. If the SNOMED CT concept was not an accurate representation, or no appropriate SNOMED CT concept was found, please write in free text the clinical term you wished to record.</p>
              <input type="text" class="span8" id="conceptFreeText" name="conceptFreeText" maxlength="250">
              <hr>
              
          <!-- Display ICPC Codes after being matched (or not) to the chosen SCT code -->    
          <div id="ICPC-Code" style="display: none;">
              <p>4. The associated ICPC-2 code is: <strong><span class="icpcCode"></span></strong></p>
              <input type="hidden" id="icpc2" name="icpc2">
              <hr>
              <p>5. In your opinion, is this ICPC-2 code an appropriate match for the <?= $recordType; ?> you recorded?</p>
              <div class="likert">
                <label class="radio inline">
                  <span>1</span><input type="radio" name="icpc2appropriate" id="icpc2appropriate1" value="1" data-error-container="#appropriateValidation"><span>Very well</span>
                </label>
                <label class="radio inline">
                  <span>2</span><input type="radio" name="icpc2appropriate" id="icpc2appropriate2" value="2">
                </label>
                <label class="radio inline">
                  <span>3</span><input type="radio" name="icpc2appropriate" id="icpc2appropriate3" value="3">
                </label>
                <label class="radio inline">
                  <span>4</span><input type="radio" name="icpc2appropriate" id="icpc2appropriate4" value="4">
                </label>
                <label class="radio inline">
                  <span>5</span><input type="radio" name="icpc2appropriate" id="icpc2appropriate5" value="5"><span>Not at all</span>
                </label>
              </div>
              <div id="appropriateValidation"></div>
              <hr>
              <p>6. If the ICPC-2 code is not an appropriate match, please record your preferred ICPC-2 code:</p>
              <div class="input-append">
                <input id="icpcSearchBox" name="searchBox" type="text" maxlength="50">
                <button id="icpcSearchBtn" class="btn" type="button">Search</button>
              </div>
              <select class="input-xlarge" id="icpcDropdown" name="icpcDropdown" size="8" data-error-container="#icpcValidation">
                <option value="">Select ICPC-2 code</option>
                <?php /* require('inc/icpccodes.php'); */ ?> 
              </select>
<!--              <button id="icpcClearBtn" class="btn" type="button" style="a">Reset</button> -->
          </div>
             
              
              
<?php
                break;
                case 2:
            ?>
            
          <!-- for the ICPC2 first option -->
              
          <div class="row">
            <div class="span8 offset2">
              <p>1. Search and (select) the <strong>ICPC-2</strong> code that represents the <?= $recordType; ?> you wish to record.</p>
              <div class="input-append">
                   <input id="icpcSearchBox" name="icpc2Search" type="text" maxlength="50">
                   <button id="icpcSearchBtn2" class="btn" type="button">Search</button>
              </div>
              <select class="input-xlarge" id="icpcDropdown" name="icpcDropdown" size="8" data-error-container="#icpcValidation">
              <option value="">Select ICPC-2 code</option>
                   <?php /* require('inc/icpccodes.php'); */ ?> 
              </select>
              <button id="icpcClearBtn2" class="btn" type="button">Reset</button>
              <div id="icpcValidation"></div>

              <!-- SCT mapped concepts -->
              
              <div id="SCT-Code" style="display: none;">
     			  <hr>
                  <p id="dropdownLabel">2. Select an associated SNOMED CT concept &nbsp;&nbsp;&nbsp;
                  <select class="input-xlarge" id="conceptsDropdown" name="conceptsDropdown" size="5" data-error-container="#conceptValidation">
                  </select>
                  </p>

                  <dl class="dl-horizontal synonyms">
                    <dt>Synonyms:</dt>
                    <dd></dd>
                  </dl>
                  
                  <hr>

                  <p>3. How well does this SNOMED CT concept adequately represent the <?= $recordType; ?> you wish to record?</p>
                  <div class="likert">
                    <label class="radio inline">
                      <span>1</span><input type="radio" name="conceptRepresentation" id="conceptRepresentation1" value="1" data-error-container="#representationValidation"><span>Very well</span>
                    </label>
                    <label class="radio inline">
                      <span>2</span><input type="radio" name="conceptRepresentation" id="conceptRepresentation2" value="2">
                    </label>
                    <label class="radio inline">
                      <span>3</span><input type="radio" name="conceptRepresentation" id="conceptRepresentation3" value="3">
                    </label>
                    <label class="radio inline">
                      <span>4</span><input type="radio" name="conceptRepresentation" id="conceptRepresentation4" value="4">
                    </label>
                    <label class="radio inline">
                      <span>5</span><input type="radio" name="conceptRepresentation" id="conceptRepresentation5" value="5"><span>Poorly</span>
                    </label>
                  </div>
                  <div id="representationValidation"></div>

                  <hr>
                  <p>4. If the SNOMED CT concept was not an accurate representation, or no appropriate SNOMED CT concept was found, please write in free text the clinical term you wished to record.</p>
                  <input type="text" class="span8" id="conceptFreeText" name="conceptFreeText" maxlength="250">
                  <hr>
             </div>
          
<?php
            break;
            case 3:
        ?>              

		<!-- Refset member verification only option -->
            
          <div class="row">
            <div class="span8 offset2">
              <p>1. Search for (and select) a <strong>SNOMED CT</strong> concept that represents the <?= $recordType; ?> you wish to record.</p>
              <div class="input-append">
                <input id="searchBox" name="searchBox" type="text" maxlength="50">
                <button id="searchBtn" class="btn" type="button">Search</button>
              </div>
   
              <select class="input-xlarge" id="conceptsDropdown" name="conceptsDropdown" size="8" data-error-container="#conceptValidation">
                <option value="">Select SNOMED concept</option>
                <?php /* require('inc/concepts.php'); */ ?> 
              </select>
              <button id="clearBtn" class="btn" type="button">Reset</button>
              <div id="conceptValidation"></div>
              <dl class="dl-horizontal synonyms">
                <dt>Synonyms:</dt>
                <dd></dd>
              </dl>
              <hr>
              <p>2. How well does this SNOMED CT concept adequately represent the <?= $recordType; ?> you wish to record?</p>
              <div class="likert">
                <label class="radio inline">
                  <span>1</span><input type="radio" name="conceptRepresentation" id="conceptRepresentation1" value="1" data-error-container="#representationValidation"><span>Very well</span>
                </label>
                <label class="radio inline">
                  <span>2</span><input type="radio" name="conceptRepresentation" id="conceptRepresentation2" value="2">
                </label>
                <label class="radio inline">
                  <span>3</span><input type="radio" name="conceptRepresentation" id="conceptRepresentation3" value="3">
                </label>
                <label class="radio inline">
                  <span>4</span><input type="radio" name="conceptRepresentation" id="conceptRepresentation4" value="4">
                </label>
                <label class="radio inline">
                  <span>5</span><input type="radio" name="conceptRepresentation" id="conceptRepresentation5" value="5"><span>Poorly</span>
                </label>
              </div>


              <div id="representationValidation"></div>
              <hr>
              <p>3. If the SNOMED CT concept was not an accurate representation, or no appropriate SNOMED CT concept was found, please write in free text the clinical term you wished to record.</p>
              <input type="text" class="span8" id="conceptFreeText" name="conceptFreeText" maxlength="250">
              <hr>
 
<?php
    break;
    }
?>              
              
                
              <div class="form-actions">
                <?php
                if(!is_null($_GET["type"]) && !is_null($_GET["enc"])){
                  ?>
                  <button type="submit" class="btn">Add this <?= $recordType; ?></button>
                   &nbsp;&nbsp;
                 
                  <?php
                } else {9
                  ?>
                  <input type="hidden" id="addAnother" name="addAnother" value="true">
                  <button type="submit" class="btn">Add another <?= $recordType; ?></button>
                  &nbsp;&nbsp;
                  <?php
                  if($_SESSION["add_mode"] == 0){
                    ?>
                    <a id="nextBtn" class="btn" href="#">Reason For Encounters complete - add Health Issues</a>
                    &nbsp;&nbsp;
                    <?php
                    } else {
                    ?>
                    <a id="finishedBtn" class="btn" href="#">Health Issues complete - review encounter</a>
                    &nbsp;&nbsp;
                    <?php
                    }
                    }
                ?>
                   <a id="cancelBtn" class="btn" href="index.php">Cancel</a>

              </div>

            </div>
          </div>
        </fieldset>
      </form>
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