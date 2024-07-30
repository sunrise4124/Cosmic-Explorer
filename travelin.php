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
        <link rel="icon" type="image/png" href="images/solar-system.png"/> <!-- Icon created by Freepik - Flaticon -->
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

        <div class="reset">
            <p class="reset-description">If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>

            <form method="post" action="travelin.php">
                <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
                <input type="submit" value="Reset" name="reset">
            </form>
        </div>

        <!-- The following code is for Displaying, Inserting, Updating and Deleting tuples of A1 -->
        <h3>Display</h3>
        <div class="panel">
            <p class="panel-description">
                To display the tuples of TravelIn, press the show button below.
            </p>
            <form method="get" action="travelin.php"> <!--refresh page when submitted-->
                <input type="hidden" id="showTravelinTupleRequest" name="showTravelinTupleRequest">
                <input type="submit" value="Show" name="showTravelinTuples"></p>
            </form>
        </div>

        <h3>Insert</h3>
        <div class="panel">
            <p class="panel-description">To insert a tuple, enter an Astronaut ID and a SC Model of the tuple you wish to insert into the table shown below.</p>
            <form class="form-inline" action="travelin.php" method="post">
                <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
                <label for="aID">Astronaut ID:</label>
                <input type="number" id="aID" name="insaID" required>
                <label for="scModel">SC Model:</label>
                <input type="text" id="scModel" name="insscModel" required>
                <input type="submit" value="Insert" name="insertSubmit">
            </form>
        </div>

        <h3>Delete</h3>
        <div class="panel">
            <p class="panel-description">
                To delete a tuple, enter the Astronaut ID and SC Model of the tuple you wish to delete from the table shown below.
            </p>
            <form class="form-inline" action="travelin.php" method="post">
                <input type="hidden" id="deleteQueryRequest" name="deleteQueryRequest">
                <label for="aID">Astronaut ID:</label>
                <input type="number" id="aID" name="delaID" required>
                <label for="scModel">SC Model:</label>
                <input type="text" id="scModel" name="delscModel" required>
                <input type="submit" value="Delete" name="deleteSubmit">
            </form>
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

        function error_parse_handler($cmdstr) {
            global $db_conn, $success;
            $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
            echo "<script type='text/javascript'>";
            echo "alert('Could not parse the command due to this error: " . htmlentities($e['message']) . "')";
            echo "</script>";
            $success = False;
        }
        function error_statement_handler($cmdstr, $statement) {
            global $db_conn, $success;
            $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
            echo "<script type='text/javascript'>";
            echo "alert('Cannot execute your Input Due to this error: " . htmlentities($e['message']) . "')";
            echo "</script>";
            $success = False;
        }

        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            //echo "<br>running ".$cmdstr."<br>";
            global $db_conn, $success;

            $statement = OCIParse($db_conn, $cmdstr);
            //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

            if (!$statement) {
                error_parse_handler($cmdstr);
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                error_statement_handler($cmdstr, $statement);
            }

			return $statement;
		}

        function executeBoundSQL($cmdstr, $list) {
            /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

			global $db_conn, $success;
			$statement = OCIParse($db_conn, $cmdstr);

            if (!$statement) {
                error_parse_handler($cmdstr);
            }

            foreach ($list as $tuple) {
                foreach ($tuple as $bind => $val) {
                    //echo $val;
                    //echo "<br>".$bind."<br>";
                    OCIBindByName($statement, $bind, $val);
                    unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
				}

                $r = OCIExecute($statement, OCI_DEFAULT);
                if (!$r) {
                    error_statement_handler($cmdstr, $statement);
                }
            }
        }

        function printResult($result) { //prints results from a select statement
            echo "<table>";
            echo "<tr>
                    <th>aID</th>
                    <th>scModel</th>
                </tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] .
                        "</td><td>" . $row[1] .
                    "</td></tr>"; //or just use "echo $row[0]"
            }

            echo "</table>";
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

        function handleDeleteRequest() {
            global $db_conn, $success;
            $del_aID = $_POST['delaID'];
            $del_scModel = preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['delscModel']);
            executePlainSQL("DELETE FROM TravelIn WHERE aID='" . $del_aID . "' AND
                            scModel='" . $del_scModel . "'");
            OCICommit($db_conn);
            if ($success) {
                echo "<script type='text/javascript'>";
                echo "alert('Successful Delete: ')";
                echo "</script>";
            }
            handleShowRequest();
        }

        function handleResetRequest() {
            global $db_conn;
            // Drop old table
            executePlainSQL("DROP TABLE TravelIn CASCADE CONSTRAINTS");

            // Create new table
            executePlainSQL("CREATE TABLE TravelIn (
                            aId INTEGER, scModel VARCHAR(20), PRIMARY KEY(aID, scModel),
                            FOREIGN KEY (aID) REFERENCES A2(aID) ON DELETE CASCADE,
                            FOREIGN KEY (scModel) REFERENCES Spacecraft(scModel) ON DELETE CASCADE)");
            executePlainSQL("INSERT INTO TravelIn VALUES (101, '12847126')");
            executePlainSQL("INSERT INTO TravelIn VALUES (102, '12721356')");
            executePlainSQL("INSERT INTO TravelIn VALUES (103, '14782312')");
            executePlainSQL("INSERT INTO TravelIn VALUES (104, '14782361')");
            executePlainSQL("INSERT INTO TravelIn VALUES (105, '12481237')");
            executePlainSQL("INSERT INTO TravelIn VALUES (101, '12721356')");
            executePlainSQL("INSERT INTO TravelIn VALUES (101, '13689014')");
            executePlainSQL("INSERT INTO TravelIn VALUES (101, '15480297')");
            executePlainSQL("INSERT INTO TravelIn VALUES (101, '13491421')");
            OCICommit($db_conn);
            handleShowRequest();
        }

        function handleInsertRequest() {
            global $db_conn, $success;

            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $_POST['insaID'],
                ":bind2" => preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['insscModel'])
            );

            $alltuples = array (
                $tuple
            );

            executeBoundSQL("insert into TravelIn values (:bind1, :bind2)", $alltuples);
            OCICommit($db_conn);
            if ($success) {
                echo "<script type='text/javascript'>";
                echo "alert('Successful Insert: ')";
                echo "</script>";
            }
            handleShowRequest();
        }

        function handleShowRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT * FROM TravelIn ORDER BY aID ASC");
            printResult($result);
        }

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    handleResetRequest();
                } else if (array_key_exists('insertQueryRequest', $_POST)) {
                    handleInsertRequest();
                } else if (array_key_exists('deleteQueryRequest', $_POST)) {
                    handleDeleteRequest();
                }

                disconnectFromDB();
            }
        }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('showTravelinTuples', $_GET)) {
                    handleShowRequest();
                }

                disconnectFromDB();
            }
        }

		if (isset($_POST['reset']) || isset($_POST['insertSubmit'])
            || isset($_POST['deleteSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['showTravelinTupleRequest'])) {
            handleGETRequest();
        }
		?>
	</body>
</html>
