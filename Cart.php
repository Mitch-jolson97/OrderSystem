<!DOCTYPE html>
<html>
    <head>
        <?php
            require 'verification.php';
        ?>
        <link rel="stylesheet" type="text/css" href="Main.css" >
        <style>
            form {
                margin: 1em auto;
            }
            #submit {
                text-align: center;
                font-size: 20px;
                width: 100%;
                height: 80px;
            }
        </style>
    </head>

    <body>
        <?php
        $userId ="";
        $conn = oci_connect('sizheng', 'Dec371996', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db2.ndsu.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))');

        include 'NavBar.html';
            if($_SERVER['QUERY_STRING'] == "action=remove&code")


        ?>

        <div id="body-content">
            <div style='display:flex;'>
                <?php
                    $cartId = "";
                    $username = $_SESSION['username'];
                    $totalCost = 0;
                    $totalItems = 0;
                    $hasItems = false;

                    $conn = oci_connect('sizheng', 'Dec371996', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db2.ndsu.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))');

                    $cartQuery = "SELECT co.ID FROM USER_T u INNER JOIN CART_ORDER co ON u.USERNAME = '$username' AND u.ID = co.USER_ID AND co.COMPLETED = 0";
                    $stid = oci_parse($conn, $cartQuery);

                    oci_define_by_name($stid, 'ID', $cartId);

                    oci_execute($stid, OCI_DEFAULT);
                    oci_fetch($stid);
                    oci_free_statement($stid);


                    $itemQuery = "SELECT i.NAME, i.PRICE, i.CATEGORY, ci.NUMBER_OF_ITEM FROM CART_ITEM ci INNER JOIN ITEM i ON ci.ORDER_ID = $cartId AND ci.ITEM_ID = i.ID";

                    $stid = oci_parse($conn, $itemQuery);

                    oci_execute($stid, OCI_DEFAULT);
                ?>


                <div style='flex: 3;'>
                    <table>
                        <tr>
                            <th>NAME</th>
                            <th>PRICE</th>
                            <th>CATEGORY</th>
                            <th>NUMBER OF ITEM</th>
                            <th style="text-align:center;" width="5%">Remove</th>
                        </tr>
                        
                        <?php
                            while($row = oci_fetch_array($stid, OCI_ASSOC))
                            {
                                $totalCost = (float)$row['PRICE'] * (float)$row['NUMBER_OF_ITEM'] + (float)$totalCost;
                                $totalItems = $totalItems + (int)$row['NUMBER_OF_ITEM'];
                                $count = 0;
                                echo "<tr>";
                                foreach ($row as $item)
                                {
                                    if($count == 1) {
                                        echo "<td style='text-align: right;'>$";
                                    } elseif ($count == 3) {
                                        echo "<td style='text-align: center;'>";
                                    } else {
                                        echo "<td>";
                                    }
                                    echo $item."</td>";
                                    $count += 1;
                                    
                                    if(!empty($item)) {
                                        $hasItems = true;
                                    }
                                }
                                    echo "<td style=\"text-align:center;\">";
                                        echo "<a href=\"Cart.php?action=remove&code\" class=\"btnRemoveAction\"><img src=\"icon-delete.png\" alt=\"Remove Item\" /></a>";
                                    echo "</td>";
                                echo "</tr>";
                            }
                            
                            oci_free_statement($stid);
                        ?>
                    </table>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column;">
                    <div style='flex: 1; border: solid black 1px; border-radius: 10px; padding: 10px; height: 80px; background-color: #EFEFFF;'>
                        <?php
                            echo "<div style='float: left;'><b>TOTAL COST:</b></div> <div style='float: right;'>$$totalCost</div><br/>";
                            echo "<div style='float: left;'><b>TOTAL ITEMS:</b></div> <div style='float: right;'>$totalItems</div>";
                        ?>
                    </div>

                    <div style='flex: 1;'>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                            <input type="submit" id="submit" name="submit" value="Confirm Purchase">
                            <br/><br/>
                            <label for="reoccuring">Reoccuring Order?</label>
                            <input type="checkbox" name="reoccuring" id="reoccuring">
                            <br/><br/>
                            <label for="dayAmount">Amount of Days?</label>
                            <select name="dayAmount" id="dayAmount">
                                <option value="15">15 Days</option>
                                <option value="30">30 Days</option>
                                <option value="45">45 Days</option>
                            </select>
                            <br/><br/>
                            <h4>Shipping Address</h4>
                            <div style="display: flex; flex-direction: column;">
                                <?php 
                                    $addressQuery = "SELECT sa.ID, sa.FIRST_NAME, sa.LAST_NAME, sa.STREET, sa.CITY, sa.STATE, sa.ZIP FROM USER_T u INNER JOIN SHIP_ADDRESS sa ON u.USERNAME = '$username' AND u.ID = sa.USER_ID";
                                    $stid = oci_parse($conn, $addressQuery);
                                    oci_execute($stid, OCI_DEFAULT);

                                    while($row = oci_fetch_array($stid, OCI_ASSOC)) {
                                        $shipId = $row['ID'];
                                        $shipName = $row['FIRST_NAME']." ".$row['LAST_NAME'];
                                        $shipStreet = $row['STREET'];
                                        $shipCity = $row['CITY'];
                                        $shipState = $row['STATE'];
                                        $shipZip = $row['ZIP'];
                                        
                                        $formattedAddress = "<div style='flex: 4;'>$shipName<br/>$shipStreet<br/>$shipCity, $shipState $shipZip</div>";

                                        echo "<div style='display: flex; flex: 1; margin-bottom: 10px;'>";
                                        
                                            echo "<input type='radio' name='address' value='$shipId' required='required' style='flex: 1; margin: auto 0;'> $formattedAddress <br/>";
                                        
                                        echo "</div>";
                                    }
                                
                                    oci_free_statement($stid);
                                ?>
                            </div>
                        </form>
                        <a href="newaddress.php">Add New Shipping Address</a>
                    </div>
                </div>

                <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && $hasItems) {

                        $reoccuring = $_POST["reoccuring"];
                        $dayAmount = $_POST["dayAmount"];
                        $addressId = $_POST["address"];
                        
                        
                        $updateQuery = "UPDATE CART_ORDER SET TOTAL_COST = $totalCost, TOTAL_NUMBER = $totalItems, ADDRESS_ID = $addressId, COMPLETED = 1 WHERE ID = $cartId";
                        $stid = oci_parse($conn, $updateQuery);
                        oci_execute($stid, OCI_DEFAULT);
                        oci_free_statement($stid);
                        
                        if($reoccuring) {
                            $insertQuery = "INSERT INTO REOCCURING_ORDER VALUES(NULL, $cartId, $dayAmount)";
                            $stid = oci_parse($conn, $insertQuery);
                            oci_execute($stid);
                            oci_free_statement($stid);
                        }
                        
                        oci_commit($conn);
                        
                        header("Refresh:0");
                    }

                    oci_close($conn);
                ?>

            </div>
        </div>

    </body>
</html>