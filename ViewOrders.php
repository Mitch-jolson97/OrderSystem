<!DOCTYPE html>
<html>
    <head>
        <?php
            require 'verification.php';
        ?>
        <link rel="stylesheet" type="text/css" href="Main.css" >
    </head>

    <body>
        <?php
            //Adds NavBar to top of page
            include 'NavBar.html';
        ?>

        <div id="body-content">

            <?php
                //Variables to hold Cart_Order values
                $cartId = "";
                $cartCost = "";
                $cartNumber = "";
                //Holds array of Card_Order values
                $cartArray = [];
                //Username saved to session data
                $username = $_SESSION['username'];

                //Open connection to database
                $conn = oci_connect('sizheng', 'Dec371996', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db2.ndsu.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))');

                //Query database for Cart_Order associated to username
                $cartQuery = "SELECT co.ID, co.TOTAL_COST, co.TOTAL_NUMBER FROM USER_T u INNER JOIN CART_ORDER co ON u.USERNAME = '$username' AND u.ID = co.USER_ID AND co.COMPLETED = 1";
                $stid = oci_parse($conn, $cartQuery);

                //Tells query handler to put Cart_Order values inside the variables
                oci_define_by_name($stid, 'ID', $cartId);
                oci_define_by_name($stid, 'TOTAL_COST', $cartCost);
                oci_define_by_name($stid, 'TOTAL_NUMBER', $cartNumber);

                //Execute Cart_Order query
                oci_execute($stid,OCI_DEFAULT);

                //Loop through returned query data and puts it as an array inside the cartArray
                while($row = oci_fetch_array($stid, OCI_ASSOC))
                {
                    array_push($cartArray, array($cartId, $cartCost, $cartNumber));
                }
                //Release the query
                oci_free_statement($stid);

                //Loop through each array of Cart_Order data
                foreach($cartArray as $cartRow) {

                    //Make container for Cart_Order data and Cart_Item table
                    echo "<div style='display:flex;'>";

                        //Display Cart_Order data inside left box
                        echo "<div style='flex: 1; border: solid black 1px; border-radius: 10px; padding: 10px; height: 90px; margin-right: 10px; background-color: #EFEFFF;'>";
                            echo "<br/><div style='float: left;'><b>ORDER ID:</b></div> <div style='float: right;'>$cartRow[0]</div><br/>";
                            echo "<div style='float: left;'><b>TOTAL COST:</b></div> <div style='float: right;'>$$cartRow[1]</div><br/>";
                            echo "<div style='float: left;'><b>TOTAL ITEMS:</b></div> <div style='float: right;'>$cartRow[2]</div><br/><br/>";
                        echo "</div>";

                        //Query for Cart_Items associated to the Cart_Order and join Item data associated to the Cart_Item
                        $itemQuery = "SELECT i.NAME, i.PRICE, i.CATEGORY, ci.NUMBER_OF_ITEM FROM CART_ITEM ci INNER JOIN ITEM i ON ci.ORDER_ID = '$cartRow[0]' AND ci.ITEM_ID = i.ID";
                        $stid = oci_parse($conn, $itemQuery);

                        //Execute Item/Cart_Item data query
                        oci_execute($stid,OCI_DEFAULT);

                        //Display Item data inside right box
                        echo "<div style='flex: 4;'>";
                            //Make a table for the item data
                            echo "<table>";
                                echo "<tr><th>NAME</th><th>PRICE</th><th>CATEGORY</th><th>NUMBER OF ITEM</th></tr>";
                                //Loop through rows in query
                                while($row = oci_fetch_array($stid, OCI_ASSOC))
                                {
                                    $count = 0;
                                    echo "<tr>";

                                    //Loop through each column inside the row of data
                                    foreach ($row as $item)
                                    {
                                        //Check which column it is to change display
                                        if($count == 1) {
                                            echo "<td style='text-align: right;'>$";
                                        } elseif ($count == 3) {
                                            echo "<td style='text-align: center;'>";
                                        } else {
                                            echo "<td>";
                                        }
                                        echo $item."</td>";
                                        $count += 1;
                                    }
                                    echo "</tr>";
                                }
                            echo "</table>";
                        echo "</div>";

                    //Close container for Cart_Order data and Cart_Item table
                    echo "</div><br/><br/>";

                    //Free up query statement
                    oci_free_statement($stid);
                }
                //Close database connection
                oci_close($conn);

                //Reference for alert output javascript using PHP
                //echo "<script type='text/javascript'>alert('$query');</script>";
            ?>

        </div>

    </body>
</html>
