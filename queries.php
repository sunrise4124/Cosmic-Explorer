<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- Making application responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Link to CSS stylesheet-->
    <link rel="stylesheet" href="css/style.css">

    <title>Cosmic Explorer</title>
    <style>
        .error-handler {
            color: white;
            font-size: 22px;
        }

        table {
            font-size: 22px;
            color: #f7f4f3;
        }
    </style>
    <link rel="icon" type="image/png" href="images/solar-system.png" /> <!-- Icon created by Freepik - Flaticon -->
</head>

<body>
    <!-- Navigation Bar -->
    <div class="topnav">
        <a href="a1.php">A1</a>
        <a href="a2.php">A2</a>
        <a href="spacecraft.php">Space Craft</a>
        <a href="travelin.php">Travel In</a>
        <a href="projection.php">Projection</a>
        <a href="queries.php">Queries</a>
    </div>
    <h3>Join Query: </h3>
    <br>
    <div class="panel">
        <p class="panel-description">
            Perform a JOIN Operation on TravelIn and Spacecraft to find the Names and Astronaut ID's of all
            astronauts that have traveled in a Spacecraft associated to the given Space Agency.
        </p>
        <form class="form-inline" action="queries.php" method="post">
            <input type="hidden" id="joinRequest" name="joinRequest">
            <label for="saName">Space Agency Name:</label>
            <input type="text" id="saName" name="joinsaName" required>
            <input type="submit" value="Join" name="joinSubmit">
        </form>

        <?php
            $db_conn = OCILogon("", "", ""); // username, password, path to your database
            if (isset($_POST['joinSubmit'])) {
                if (array_key_exists('joinRequest', $_POST)) {
                    $saName = preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['joinsaName']);
                    $query = "SELECT t.aID, a.aName FROM TravelIn t, Spacecraft s, A2 a WHERE
                        t.scModel=s.scModel
                        AND a.AID = t.aID
                        AND upper(s.saName)=upper('" . $saName . "') ORDER BY t.aID ASC";
                    $result = executePlainSQL($query);
                    echo "<table style='color:white;'>";
                    echo "<tr>
                        <th>aID</th>
                        <th>aName</th>
                    </tr>";
                    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                        echo "<tr><td>"
                        . $row[0] . "</td><td>"
                        . $row[1] . "</td><tr>";
                    }
                    echo "</table>";
                }
                disconnectFromDB();
            }
        ?>
    </div>

    <h3>Aggregation with GROUP BY Query: </h3>
    <br>
    <div class="panel">
        <p class="panel-description">
            Perform an Aggregation with GROUP BY on A2 to find the number of Astronauts in each nationality.
        </p>
        <form class="form-inline" action="queries.php" method="get">
            <input type="hidden" id="aggGbRequest" name="aggGbRequest">
            <input type="submit" value="Aggregate GB" name="aggGbSubmit">
        </form>

        <?php
            $db_conn = OCILogon("", "", ""); // username, password, path to your database
            if (isset($_GET['aggGbSubmit'])) {
                if (array_key_exists('aggGbRequest', $_GET)) {
                    $result = executePlainSQL("SELECT aNationality, count(*) FROM A2 GROUP BY aNationality");
                    echo "<table style='color:white;'>";
                    echo "<tr>
                        <th>aNationality</th>
                        <th>Count</th>
                    </tr>";
                    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                        echo "<tr><td>"
                        . $row[0] . "</td><td>"
                        . $row[1] . "</td><tr>";
                    }
                    echo "</table>";
                }
                disconnectFromDB();
            }
        ?>
    </div>

    <h3>Aggregation with Having Query: </h3>
    <br>
    <div class="panel">
        <p class="panel-description">
            Perform an Aggregation with Having on A2 to find the each nationality that have an Astronaut count more than 1.
        </p>
        <form class="form-inline" action="queries.php" method="get">
            <input type="hidden" id="aggHavingRequest" name="aggHavingRequest">
            <input type="submit" value="Aggregate Having" name="aggHavingSubmit">
        </form>
        <?php
            $db_conn = OCILogon("", "", ""); // username, password, path to your database
            if (isset($_GET['aggHavingSubmit'])) {
                if (array_key_exists('aggHavingRequest', $_GET)) {
                    $result = executePlainSQL("SELECT aNationality, count(*) FROM A2 GROUP BY aNationality HAVING count(*)>=2");
                    echo "<table style='color:white;'>";
                    echo "<tr>
                        <th>aNationality</th>
                        <th>Count</th>
                    </tr>";
                    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                        echo "<tr><td>"
                        . $row[0] . "</td><td>"
                        . $row[1] . "</td><tr>";
                    }
                    echo "</table>";
                }
                disconnectFromDB();
            }
        ?>
    </div>

    <h3>Nested Aggregation with GROUP BY: </h3>
    <br>
    <div class="panel">
        <p class="panel-description">
            Perform a Nested Aggregation with GROUP BY on A2 to find the all Nationality groups that have an average age greater than the total average age of all astronauts.
        </p>
        <form class="form-inline" action="queries.php" method="get">
            <input type="hidden" id="nestedAggGbRequest" name="nestedAggGbRequest">
            <input type="submit" value="Nested Aggregate GB" name="nestedAggGbSubmit">
        </form>
        <?php
            $db_conn = OCILogon("", "", ""); // username, password, path to your database
            if (isset($_GET['nestedAggGbSubmit'])) {
                if (array_key_exists('nestedAggGbRequest', $_GET)) {
                    $result = executePlainSQL("SELECT A2.aNationality, AVG(A2.aAge) FROM A2 GROUP BY A2.aNationality HAVING AVG(A2.aAge) > (SELECT AVG(aAge) FROM A2)");
                    echo "<table style='color:white;'>";
                    echo "<tr>
                        <th>aNationality</th>
                        <th>Average Age</th>
                    </tr>";
                    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                        echo "<tr><td>"
                        . $row[0] . "</td><td>"
                        . $row[1] . "</td><tr>";
                    }
                    echo "</table>";
                }
                disconnectFromDB();
            }
        ?>
    </div>

    <h3>Division Query: </h3>
    <br>
    <div class="panel">
        <p class="panel-description">
            Perform a Division to find information on all Astronauts who have worked for all Space Agencies.
        </p>
        <form class="form-inline" action="queries.php" method="get">
            <input type="hidden" id="divisionRequest" name="divisionRequest">
            <input type="submit" value="Division" name="divisionSubmit">
        </form>
        <?php
            $db_conn = OCILogon("", "", ""); // username, password, path to your database
            if (isset($_GET['divisionSubmit'])) {
                if (array_key_exists('divisionRequest', $_GET)) {
                    $result = executePlainSQL("SELECT DISTINCT A.aName, A.aNationality, A.aMissions
                                                FROM A2 A
                                                WHERE NOT EXISTS (
                                                SELECT SA.saName
                                                FROM SA2 SA
                                                MINUS
                                                SELECT S.saName
                                                FROM TravelIn T
                                                JOIN Spacecraft S ON T.scModel = S.scModel
                                                WHERE T.aID = A.aID
                                                )");
                    echo "<table style='color:white;'>";
                    echo "<tr>
                        <th>aName</th>
                        <th>aNationality</th>
                        <th>aMissions</th>
                    </tr>";
                    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                        echo "<tr><td>"
                        . $row[0] . "</td><td>"
                        . $row[1] . "</td><td>"
                        . $row[2] . "</td><tr>";
                    }
                    echo "</table>";
                }
                disconnectFromDB();
            }
        ?>
    </div>

    <?php
        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

        function debugAlertMessage($message) {
            global $show_debug_alert_messages;

            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
            }
        }

        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            //echo "<br>running ".$cmdstr."<br>";
            global $db_conn, $success;

            $statement = OCIParse($db_conn, $cmdstr);
            //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                echo htmlentities($e['message']);
                $success = False;
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                $success = False;
            }

			return $statement;
		}

        function connectToDB() {
            global $db_conn;
            global $user, $pass;

            // Your username is ora_(CWL_ID) and the password is a(student number). For example,
			// ora_platypus is the username and a12345678 is the password.
            $db_conn = OCILogon("", "", ""); // username, password, path to your database

            if ($db_conn) {
                debugAlertMessage("Database is Connected");
                return true;
            } else {
                debugAlertMessage("Cannot connect to Database");
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
                return false;
            }
        }

        function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

        // function printResult($result) {
        //     echo "<table style='color:white;'>";
        //     echo "<tr>
        //         <th>aID</th>
        //         <th>aName</th>
        //     </tr>";
        //     while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        //         echo "<tr><td>"
        //         . $row[0] . "</td><td>"
        //         . $row[1] . "</td><tr>";
        //     }
        //     echo "</table>";
        // }

        // function handleJoinRequest() {
        //     $saName = strtolower($_POST['joinsaName']);
        //     $query = "SELECT t.aID, a.aName FROM TravelIn t, Spacecraft s, A2 a WHERE
        //                 t.scModel=s.scModel
        //                 AND a.AID = t.aID
        //                 AND s.saName = '" . $saName . "' ORDER BY t.aID ASC";
        //     $result = executePlainSQL($query);
        //     printResult($result);
        // }

        // function handleAggGbRequest() {
        //     $result = executePlainSQL("SELECT aNationality, count(*) FROM A2 GROUP BY aNationality");
        //     echo "<table style='color:white;'>";
        //     echo "<tr>
        //         <th>aNationality</th>
        //         <th>Count</th>
        //     </tr>";
        //     while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        //         echo "<tr><td>"
        //         . $row[0] . "</td><td>"
        //         . $row[1] . "</td><tr>";
        //     }
        //     echo "</table>";
        // }

        // function handleAggHavingRequest() {
        //     $result = executePlainSQL("SELECT aNationality, count(*) FROM A2 GROUP BY aNationality HAVING count(*)>=2");
        //     echo "<table style='color:white;'>";
        //     echo "<tr>
        //         <th>aNationality</th>
        //         <th>Count</th>
        //     </tr>";
        //     while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        //         echo "<tr><td>"
        //         . $row[0] . "</td><td>"
        //         . $row[1] . "</td><tr>";
        //     }
        //     echo "</table>";
        // }

        // function handleNestedAggGbRequest() {
        //     $result = executePlainSQL("SELECT A2.aNationality, AVG(A2.aAge) FROM A2 GROUP BY A2.aNationality HAVING AVG(A2.aAge) > (SELECT AVG(aAge) FROM A2)");
        //     echo "<table style='color:white;'>";
        //     echo "<tr>
        //         <th>aNationality</th>
        //         <th>Average Age</th>
        //     </tr>";
        //     while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        //         echo "<tr><td>"
        //         . $row[0] . "</td><td>"
        //         . $row[1] . "</td><tr>";
        //     }
        //     echo "</table>";
        // }
    ?>
</body>
</html>
