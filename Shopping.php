<html>
    <head>
        <?php 
            require 'verification.php';
        ?>
        <link rel="stylesheet" type="text/css" href="Main.css" >
        <style>
            #amount {
                margin-right: 10px;
            }
            .itemBox {
                border: solid grey 1px;
                border-radius: 10px;
                margin-bottom: 10px;
                padding: 10px;
                background-color: azure;
            }
            .itemInfo {
                margin-bottom: 10px;
                
            }
            .itemInfoVal {
                
            }
            #itemId {
                
            }
            .itemAction {
                
            }
        </style>
    </head>
    
    <body>
        <?php 
            include 'NavBar.html';
        ?>
        
        <div id="body-content">
            <?php 
                $cartId = "";
                $userId = "";
                $shipId = "";
                $itemIdArray = [];
                $username = $_SESSION['username'];
            
                $conn = oci_connect('sizheng', 'Dec371996', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db2.ndsu.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))');
            
                $userQuery = "SELECT u.ID FROM USER_T u WHERE u.USERNAME = '$username'";
                $stid = oci_parse($conn, $userQuery);
                oci_define_by_name($stid, 'ID', $userId);

                oci_execute($stid, OCI_DEFAULT);
                oci_fetch($stid);
                oci_free_statement($stid);
            
                //echo "User ID: $userId <br/>";
            
            
                /* NEED QUERY TO GET SHIP_ADDRESS OF USER_T HERE */
                
                
                $cartQuery = "SELECT co.ID FROM CART_ORDER co WHERE co.USER_ID = '$userId' AND co.COMPLETED = 0";
                $stid = oci_parse($conn, $cartQuery);
                oci_define_by_name($stid, 'ID', $cartId);

                oci_execute($stid, OCI_DEFAULT);
                oci_fetch($stid);
                oci_free_statement($stid);
                

                if(empty($cartId)) {
                    $insertQuery = "INSERT INTO CART_ORDER VALUES(NULL, 1, $userId, NULL, NULL, 0)";
                    $stid = oci_parse($conn, $insertQuery);
                    oci_execute($stid);
                    oci_free_statement($stid);
                    oci_commit($conn);
                    
                    $cartQuery = "SELECT co.ID FROM CART_ORDER co WHERE co.USER_ID = '$userId' AND co.COMPLETED = 0";
                    $stid = oci_parse($conn, $cartQuery);
                    oci_define_by_name($stid, 'ID', $cartId);

                    oci_execute($stid, OCI_DEFAULT);
                    oci_fetch($stid);
                    oci_free_statement($stid);
                }
                
                //echo "Cart ID: $cartId <br/><br/>";
                
                $allItemQuery = "SELECT * FROM ITEM";
                $stid = oci_parse($conn, $allItemQuery);
                oci_execute($stid, OCI_DEFAULT);
                
                
                $count = 0;
                while($row = oci_fetch_array($stid, OCI_ASSOC)){
                    array_push($itemIdArray, $row['ID']);
                    echo "<div class='itemBox'>";
                        echo "<div class='itemInfo'>";
                            echo "<div class='itemInfoVal' id='itemId'>";
                                echo "ID: ".$row['ID'];
                            echo "</div>";
                            echo "<div class='itemInfoVal'>";
                                echo "Name: ".$row['NAME'];
                            echo "</div>";
                            echo "<div class='itemInfoVal'>";
                                echo "Cost: $".$row['PRICE'];
                            echo "</div>";
                            echo "<div class='itemInfoVal'>";
                                echo "Category: ".$row['CATEGORY'];
                            echo "</div>";
                        echo "</div>";
                    
                        echo "<div class='itemAction'>";
                            echo "<form method='post' action='Shopping.php'>";
                                echo "<select name='amount' id='amount'>";
                                    echo "<option value='1'>1</option>";
                                    echo "<option value='2'>2</option>";
                                    echo "<option value='3'>3</option>";
                                    echo "<option value='4'>4</option>";
                                    echo "<option value='5'>5</option>";
                                echo "</select>";

                                echo "<button type='submit' name='submit' id='submit' value='$count'>Add</button>";
                            echo "</form>";
                        echo "</div>";
                    echo "</div>";
                    $count += 1;
                }
                
            
                oci_free_statement($stid);
            
            
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    
                    $item = $_POST["submit"];
                    $amount = $_POST["amount"];
                    $insertQuery = "INSERT INTO CART_ITEM VALUES(NULL, $cartId, $itemIdArray[$item], $amount)";
                    $stid = oci_parse($conn, $insertQuery);
                    oci_execute($stid);
                    oci_free_statement($stid);
                    oci_commit($conn);
                }
            
                oci_close($conn);
            
                
            ?>
        </div>
        
    </body>
</html>
