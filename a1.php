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

            <form method="post" action="a1.php">
                <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
                <input type="submit" value="Reset" name="reset">
            </form>
        </div>

        <!-- The following code is for Displaying, Inserting, Updating and Deleting tuples of A1 -->
        <h3>Display</h3>
        <div class="panel">
            <p class="panel-description">
                To display the tuples of A1, press the show button below.
            </p>
            <form method="get" action="a1.php"> <!--refresh page when submitted-->
                <input type="hidden" id="showA1TupleRequest" name="showA1TupleRequest">
                <input type="submit" value="Show" name="showA1Tuples"></p>
            </form>
        </div>

        <h3>Insert</h3>
        <div class="panel">
            <p class="panel-description">To insert a tuple, enter the training ID, training start date and training end date of the tuple you wish to insert into the table shown below.</p>
            <form class="form-inline" action="a1.php" method="post">
                <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
                <label for="aTID">Training ID:</label>
                <input type="number" id="aTID" name="insATID" required>
                <label for="start-date">Training Start Date:</label>
                <input type="date" id="start-date" name="insATStart" required>
                <label for="end-date">Training End Date:</label>
                <input type="date" id="end-date" name="insATEnd" required>
                <input type="submit" value="Insert" name="insertSubmit">
            </form>
        </div>

        <h3>Update</h3>
        <div class="panel">
            <p class="panel-description">To update a tuple, enter the training ID and the training start date of the tuple you wish to update from the table shown below. Then enter the new training end date.</p>
            <form class="form-inline" action="a1.php" method="post">
                <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
                <label for="aTID">Training ID:</label>
                <input type="number" id="aTID" name="aTID" required>
                <label for="start-date">Training Start Date:</label>
                <input type="date" id="start-date" name="aTrainingStart" required>
                <label for="end-date">New Training End Date:</label>
                <input type="date" id="end-date" name="newTrainingEnd" required>
                <input type="submit" value="Update" name="updateSubmit">
            </form>
        </div>

        <h3>Delete</h3>
        <div class="panel">
            <p class="panel-description">
                To delete a tuple, enter the training ID and the training start date of the tuple you wish to delete from the table shown below.
            </p>
            <form class="form-inline" action="a1.php" method="post">
                <input type="hidden" id="deleteQueryRequest" name="deleteQueryRequest">
                <label for="aTID">Training ID:</label>
                <input type="number" id="aTID" name="delATID" required>
                <label for="start-date">Training Start Date:</label>
                <input type="date" id="start-date" name="delATStart" required>
                <input type="submit" value="Delete" name="deleteSubmit">
            </form>
        </div>
        <?php
        // $env = parse_ini_file(".env");
        // $user = $_ENV["USER"];
        // $pass = $_ENV["PASS"];
        // putenv("USER=$user");
        // putenv("PASS=$pass");

		//this tells the system that it's no longer just parsing html; it's now parsing PHP

        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = false; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

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
            echo "<tr><th>aTID</th><th>aTrainingStart</th><th>aTrainingEnd</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr>"; //or just use "echo $row[0]"
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

        function handleUpdateRequest() {
            global $db_conn, $success;

            $tid = $_POST['aTID'];
            // $old_tstart = $_POST['oldTrainingStart'];
            $tstart = $_POST['aTrainingStart'];
            // $old_tend = $_POST['oldTrainingEnd'];
            $new_tend = $_POST['newTrainingEnd'];

            if (empty($new_tend)) {
                $result = executePlainSQL("SELECT aTrainingEnd FROM A1 WHERE aTID='" . $tid . "'");
                while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {

                    $new_tend = date_create($row[0]);
                    $new_tend = date_format($new_tend, "Y-m-d");
                    // echo "<tr><td>" . $new_tend . "</td><tr>";//or just use "echo $row[0]"
                }
            }

            // you need the wrap the old name and new name values with single quotations
            executePlainSQL("UPDATE A1 SET aTrainingEnd = TO_DATE('" . $new_tend .
                            "', 'YYYY-MM-DD') WHERE aTID='" . $tid . "' AND
                            aTrainingStart = TO_DATE('" . $tstart . "', 'YYYY-MM-DD')");

            OCICommit($db_conn);
            if ($success) {
                echo "<script type='text/javascript'>";
                echo "alert('Successful Update: ')";
                echo "</script>";
            }
            handleShowRequest();
        }

        function handleDeleteRequest() {
            global $db_conn, $success;
            $del_tid = $_POST['delATID'];
            $del_tstart = $_POST['delATStart'];
            executePlainSQL("DELETE FROM A1 WHERE aTID='" . $del_tid . "' AND aTrainingStart=TO_DATE('" . $del_tstart . "', 'YYYY-MM-DD')");
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
            executePlainSQL("DROP TABLE A1 CASCADE CONSTRAINTS");

            // Create new table
            executePlainSQL("CREATE TABLE A1 (aTID INTEGER, aTrainingStart DATE, aTrainingEnd DATE, PRIMARY KEY(aTID, aTrainingStart))");
            executePlainSQL("INSERT INTO A1 VALUES (1, TO_DATE('2020-11-09', 'YYYY-MM-DD'), TO_DATE('2020-12-05', 'YYYY-MM-DD'))");
            executePlainSQL("INSERT INTO A1 VALUES (2, TO_DATE('1990-07-11', 'YYYY-MM-DD'), TO_DATE('1992-09-03', 'YYYY-MM-DD'))");
            executePlainSQL("INSERT INTO A1 VALUES (3, TO_DATE('2008-04-13', 'YYYY-MM-DD'), TO_DATE('2010-08-20', 'YYYY-MM-DD'))");
            executePlainSQL("INSERT INTO A1 VALUES (4, TO_DATE('2020-11-21', 'YYYY-MM-DD'), TO_DATE('2023-01-01', 'YYYY-MM-DD'))");
            executePlainSQL("INSERT INTO A1 VALUES (5, TO_DATE('2020-11-09', 'YYYY-MM-DD'), TO_DATE('2022-12-05', 'YYYY-MM-DD'))");
            executePlainSQL("INSERT INTO A1 VALUES (6, TO_DATE('2013-02-12', 'YYYY-MM-DD'), TO_DATE('2015-03-01', 'YYYY-MM-DD'))");
            executePlainSQL("INSERT INTO A1 VALUES (7, TO_DATE('2013-12-26', 'YYYY-MM-DD'), TO_DATE('2016-01-04', 'YYYY-MM-DD'))");
            OCICommit($db_conn);
            handleShowRequest();
        }

        function handleInsertRequest() {
            global $db_conn, $success;

            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $_POST['insATID'],
                ":bind2" => $_POST['insATStart'],
                ":bind3" => $_POST['insATEnd']
            );

            $alltuples = array (
                $tuple
            );

            executeBoundSQL("insert into A1 values (:bind1, TO_DATE(:bind2, 'YYYY-MM-DD'), TO_DATE(:bind3, 'YYYY-MM-DD'))", $alltuples);

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

            $result = executePlainSQL("SELECT * FROM A1 ORDER BY aTID ASC");
            printResult($result);
            // if (($row = oci_fetch_row($result)) != false) {
            //     echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
            // }
        }

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    handleResetRequest();
                } else if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleUpdateRequest();
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
                if (array_key_exists('showA1Tuples', $_GET)) {
                    handleShowRequest();
                }

                disconnectFromDB();
            }
        }

		if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])
            || isset($_POST['deleteSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['showA1TupleRequest'])) {
            handleGETRequest();
        }
		?>
	</body>
</html>
