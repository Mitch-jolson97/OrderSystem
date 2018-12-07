<!DOCTYPE HTML>
<html>
<head>
    <style>
        .error {color: #FF0000;}
        #center-login {
            width: 400px;
            margin: 0 auto;
        }
        h2, h4 {
            text-align: center;
        }
        .form-element {
            display: flex;
        }
        .form-element label {
            padding-right: 10px;
            margin-bottom: 15px;
        }
        .form-element input {
            height: 15px;
            flex: 1;
        }
        #submit {
            margin: 1em 5em;
            text-align: center;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="Main.css" >
</head>
<body>

    <?php
        include 'NavBar.html';
    ?>




        <?php
            // define variables and set to empty values
            //session_start();
            $username = $_SESSION['username'];
            $userId = "";
            $firstnameErr = $lastnameErr = $streetErr = $cityErr = $stateErr = $zipErr = "";
            $firstname = $lastname = $street = $city = $state = $zip = "";
            //$userId = 0;
            $checkerr = 0;
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
              if (empty($_POST["lastname"])) {
                $lastnameErr = "Lastname is required";
                $checkerr = 1;
              } else {
                $lastname = test_input($_POST["lastname"]);
              }
              if (empty($_POST["firstname"])) {
                $firstnameErr = "Firstname is required";
                $checkerr = 1;
              } else {
                $firstname = test_input($_POST["firstname"]);
              }
              if (empty($_POST["street"])) {
                 $streetErr = "street is required";
                 $checkerr = 1;
               } else {
                 $street = test_input($_POST["street"]);
               }
              if (empty($_POST["city"])) {
                 $cityErr = "city is required";
                 $checkerr = 1;
               } else {
                 $city = test_input($_POST["city"]);
               }
              if (empty($_POST["state"])) {
                 $stateErr = "state is required";
                 $checkerr = 1;
              } else {
                 $state = test_input($_POST["state"]);
              }
              if (empty($_POST["zip"])) {
                $zipErr = "zipcode is required";
                $checkerr = 1;
              } else {
                $zip = test_input($_POST["zip"]);
              }




           if($checkerr == 0){//check is there an error or not, if no error then connect database
            $conn = oci_connect('sizheng', 'Dec371996', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db2.ndsu.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))');


            $userQuery = "SELECT u.ID FROM USER_T u WHERE u.USERNAME = '$username'";
            $stid = oci_parse($conn, $userQuery);
            oci_define_by_name($stid, 'ID', $userId);

            oci_execute($stid, OCI_DEFAULT);
            oci_fetch($stid);
            oci_free_statement($stid);
            //

            $query2 = "INSERT INTO SHIP_ADDRESS Values(NULL,$userId,'$firstname','$lastname','$street','$city','$state','$zip')";
            $stid2 = oci_parse($conn,$query2);
            oci_execute($stid2,OCI_DEFAULT);
            //iterate through each row
            //while ($row = oci_fetch_array($stid,OCI_ASSOC))
            echo "<h4>Successfully created a new shipping address, $name!</h4>";
            oci_free_statement($stid2);
            oci_commit($conn);


            oci_free_statement($stid);
            oci_close($conn);
            }
            }

            function test_input($data) {
              $data = trim($data);
              $data = stripslashes($data);
              $data = htmlspecialchars($data);
              return $data;
            }
        ?>
        <div id="body-content">
            <div id="center-login">
                <h2>Register</h2>

                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <div class="form-element">
                        <label for="firstname">FirstName: </label>
                        <input id="firstname" type="text" name="firstname" value="<?php echo $firstname;?>">
                        <span class="error"> <?php echo $firstnameErr;?></span>
                    </div>
                    <div class="form-element">
                        <label for="lastname">LastName: </label>
                        <input id="lastname" type="text" name="lastname" value="<?php echo $lastname;?>">
                        <span class="error"> <?php echo $lastnameErr;?></span>
                    </div>
                    <div class="form-element">
                        <label for="street">Street: </label>
                        <input id="street" type="text" name="street" value="<?php echo $street;?>">
                        <span class="error"> <?php echo $streetErr;?></span>
                    </div>
                    <div class="form-element">
                        <label for="city">City: </label>
                        <input id="city" type="text" name="city" value="<?php echo $city;?>">
                        <span class="error"> <?php echo $cityErr;?></span>
                    </div>
                    <div class="form-element">
                        <label for="state">State: </label>
                        <input id="city" type="text" name="city" value="<?php echo $city;?>">
                        <span class="error"> <?php echo $cityErr;?></span>
                    </div>
                    <div class="form-element">
                        <label for="zip">Zipcode: </label>
                        <input id="zip" type="number" name="zip" value="<?php echo $zip;?>">
                        <span class="error"> <?php echo $zipErr;?></span>
                    </div>
                    <div class="form-element">
                        <input id="submit" type="submit" name="submit" value="Submit" style="height: 30px;">
                    </div>
                </form>
                <!-- <a href="login1.php">log in</a> -->
            </div>
    </div>
</body>
</html>
