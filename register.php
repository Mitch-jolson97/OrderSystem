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

            $nameErr = $passErr = "";
            $name = $pass = $comment = $password = "";
            //$userId = 0;
           $checkerr = 0;
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (empty($_POST["name"])) {
                    $nameErr = "Name is required";
                    $checkerr =1;
                } else {
                    $name = test_input($_POST["name"]);
                // check if name only contains letters and whitespace
                if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
                    $nameErr = "Only letters and white space allowed";
                    $checkerr =1;
                }
            }

            if (empty($_POST["pass"])) {
                $passErr = "password is required";
                $checkerr =1;
            } else {
                $pass = test_input($_POST["pass"]);
            }
           if($checkerr == 0){//check is there an error or not, if no error then connect database
            $conn = oci_connect('sizheng', 'Dec371996', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db2.ndsu.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))');

            //first do query for check the same user name in database
            $query = "SELECT * FROM USER_T where USERNAME = '$name'";
            $stid = oci_parse($conn,$query);
            oci_execute($stid,OCI_DEFAULT);
            //oci_define_by_name($stid, 'PASSWORD', $password);
            if(!($row = oci_fetch_array($stid,OCI_ASSOC)))//if no same name in database then insert
            {
            $query2 = "INSERT INTO USER_T Values(NULL,$name,$pass);";
            $stid2 = oci_parse($conn,$query2);
            oci_execute($stid2,OCI_DEFAULT);
            //iterate through each row
            //while ($row = oci_fetch_array($stid,OCI_ASSOC))
            echo "<h4>Register seccess $name!</h4>";
            oci_free_statement($stid2);
            }else { // The username is not available
            $nameErr = "That username has already been registered.";
            }

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
                        <label for="name">Name: </label>
                        <input id="name" type="text" name="name" value="<?php echo $name;?>">
                        <span class="error"> <?php echo $nameErr;?></span>
                    </div>
                    <div class="form-element">
                        <label for="pass">Password: </label>
                        <input id="pass" type="password" name="pass" value="<?php echo $pass;?>">
                        <span class="error"> <?php echo $passErr;?></span>
                    </div>
                    <div class="form-element">
                        <input id="submit" type="submit" name="submit" value="Submit" style="height: 30px;">
                    </div>
                </form>
                <a href="login1.php">log in</a>
            </div>
    </div>
</body>
</html>
