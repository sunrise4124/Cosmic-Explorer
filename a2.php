<?php
$selection_vars = 1;
// error_reporting(E_ALL);
?>
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

    <div class="reset">
        <p class="reset-description">If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>

        <form method="post" action="a2.php">
            <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
            <input type="submit" value="Reset" name="reset">
        </form>
    </div>

    <!-- The following code is for Displaying, Inserting, Updating and Deleting tuples of A2 -->
    <h3>Display</h3>
    <div class="panel">
        <p class="panel-description">
            To display the tuples of A2, press the show button below.
        </p>
        <form method="get" action="a2.php"> <!--refresh page when submitted-->
            <input type="hidden" id="showA2TupleRequest" name="showA2TupleRequest">
            <input type="submit" value="Show" name="showA2Tuples"></p>
        </form>
    </div>

    <h3>Insert</h3>
    <div class="panel">
        <p class="panel-description">
            To insert a new Astronaut into the database, please enter the following details.
        </p>
        <form class="form-inline" action="a2.php" method="post">
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            <label for="aID">Astronaut ID:</label>
            <input type="number" id="aID" name="insAID" required>
            <label for="aName">Astronaut Name:</label>
            <input id="aName" placeholder="Enter name" name="insAName" required>
            <label for="aAge">Astronaut Age:</label>
            <input type="number" id="aAge" name="insAAge" required>
            <label for="aNationality">Nationality:</label>
            <input id="aNationality" name="insANationality" required>
            <label for="aMissions">Number of Missions:</label>
            <input type="number" id="aMissions" name="insAMissions" required>
            <label for="start-date">Training Start Date:</label>
            <input type="date" id="start-date" name="insATStart" required>
            <label for="aTID">Training ID:</label>
            <input type="number" id="aTID" name="insATID" required>
            <input type="submit" value="Insert" name="insertSubmit">
        </form>
    </div>

    <h3>Update</h3>
    <div class="panel">
        <p class="panel-description">
            Enter the Astronaut ID of the tuple you wish to update from the table shown below.
            Then, optionally enter the new values of the attribute you wish to update.
        </p>
        <form class="form-inline" action="a2.php" method="post">
            <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
            <label for="aID">Astronaut ID:</label>
            <input type="number" id="aID" name="aID" required>
            <label for="aName">New Astronaut Name:</label>
            <input id="aName" placeholder="Enter name" name="newName">
            <label for="aAge">Astronaut Age:</label>
            <input type="number" id="aAge" name="newAge">
            <label for="aNationality">New Nationality:</label>
            <input id="aNationality" name="newNationality">
            <label for="aMissions">New Number of Missions:</label>
            <input type="number" id="aMissions" name="newMissions">
            <label for="start-date">New Training Start Date:</label>
            <input type="date" id="start-date" name="newTrainingStart">
            <label for="aTID">New Training ID:</label>
            <input type="number" id="aTID" name="newTID">
            <input type="submit" value="Update" name="updateSubmit">
        </form>
    </div>

    <h3>Delete</h3>
    <div class="panel">
        <p class="panel-description">
            Enter the Astronaut ID of the tuple you wish to delete from the table shown below.
        </p>
        <form class="form-inline" action="a2.php" method="post">
            <input type="hidden" id="deleteQueryRequest" name="deleteQueryRequest">
            <label for="aID">Astronaut ID:</label>
            <input type="number" id="aID" name="delAID" required>
            <input type="submit" value="Delete" name="deleteSubmit">
        </form>
    </div>

    <!-- The user is able to specify the filtering conditions for a given table. That is, the user is able to determine what shows up in the WHERE clause.
        The user should be allowed to search for tuples using any number of AND/OR clauses. It is fine to implement this as a dropdown of and/or options -->
    <h3>Selection</h3>
    <div class="panel" id="selectionFormContainer">
        <p class="panel-description">
            NOTE: Enter the DATES for Training Start exactly as shown on the printed table. Additionally, you will need to enter the names and nationalities as shown below (only first letter capitalized).
        </p>
        <form class="form-inline" id="filterForm" action="a2.php" method="post">
            <input type="hidden" id="selectRequest" name="selectRequest">
            <div id="cloneContainer">
                <label for="field">Select Attribute:</label>
                <select id="field" name="fields[]">
                    <option value="aID">ID</option>
                    <option value="aName">Name</option>
                    <option value="aAge">Age</option>
                    <option value="aNationality">Nationality</option>
                    <option value="aMissions">Missions</option>
                    <option value="aTrainingStart">Training Start</option>
                    <option value="aTID">Training ID</option>
                </select>

                <label for="operator">Select Operator:</label>
                <select id="operator" name="operators[]">
                    <option value="=">Equals</option>
                    <option value="!=">Not Equals</option>
                    <option value="<">Less Than</option>
                    <option value=">">Greater Than</option>
                    <option value="<=">Less Than Equal</option>
                    <option value=">=">Greater Than Equal</option>
                    <!-- Add more operators as needed -->
                </select>

                <label for="value">Enter Value:</label>
                <input type="text" id="value" name="values[]" required>

                <label for="logicalOperator" style="display: none;">Select Logical Operator:</label>
                <select id="logicalOperator" name="logicalOperators[]" style="display: none;">
                    <option value="AND">AND</option>
                    <option value="OR">OR</option>
                </select>
            </div>
            <button id="displayButton" type="submit" name="selectSubmit">Display</button>
        </form>
        <button type="button" onclick="addCondition()" >Add Condition</button>
    </div>
    <script>
        // This function adds a new form in the same format as above to the page for the user to enter another condition
        let clonedNode = 0;
        let fieldID = 0;
        let operatorID = 0;
        let valueID = 0;
        let logicalOperatorID = 0;

        function addCondition() {
                var form;
                if (clonedNode == 0) {
                    form = document.getElementById("cloneContainer").cloneNode(true);
                } else {
                    form = document.getElementById("cloneContainer" + clonedNode).cloneNode(true);
                }

                form.id = "cloneContainer" + ++clonedNode;
                // Changing the IDs of the elements in the new form to avoid conflicts
                form.getElementsByTagName("select")[0].id = "field" + ++fieldID;
                form.getElementsByTagName("select")[1].id = "operator" + ++operatorID;
                form.getElementsByTagName("input")[0].id = "value" + ++valueID;
                form.getElementsByTagName("select")[2].id = "logicalOperator" + ++logicalOperatorID;
                // Changing the for attributes of the labels in the new form to avoid conflicts
                form.getElementsByTagName("label")[0].setAttribute("for", "field" + fieldID);
                form.getElementsByTagName("label")[1].setAttribute("for", "operator" + operatorID);
                form.getElementsByTagName("label")[2].setAttribute("for", "value" + valueID);
                form.getElementsByTagName("label")[3].setAttribute("for", "logicalOperator" + logicalOperatorID);

                // Displaying the logical operator dropdown for the previous form
                if (logicalOperatorID > 1) {
                    document.getElementById("logicalOperator" + (logicalOperatorID - 1)).style.display = "inline-block";
                }
                else {
                    document.getElementById("logicalOperator").style.display = "inline-block";
                }

                // Displaying the label for the logical operator dropdown for the previous form
                if (logicalOperatorID > 1) {
                    document.getElementById("logicalOperator" + (logicalOperatorID - 1)).previousElementSibling.style.display = "inline-block";
                }
                else {
                    document.getElementById("logicalOperator").previousElementSibling.style.display = "inline-block";
                }

                // Adding the new form to the page before the Display button
                document.getElementById("filterForm").insertBefore(form, document.getElementById("displayButton"));
                // Emptying the text field for the value of the new condition
                form.getElementsByTagName("input")[0].value = "";
            }
    </script>
    <?php
    $success = True; //keep track of errors so it redirects the page only if there are no errors
    $db_conn = NULL; // edit the login credentials in connectToDB()
    $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

    function debugAlertMessage($message)
    {
        global $show_debug_alert_messages;

        if ($show_debug_alert_messages) {
            echo "<script type='text/javascript'>alert('" . $message . "');</script>";
        }
    }

    function error_parse_handler($cmdstr)
    {
        global $db_conn, $success;
        $e = OCI_Error($db_conn);
        echo "<script type='text/javascript'>";
        echo "alert('Could not parse the command due to this error: " . htmlentities($e['message']) . "')";
        echo "</script>";
        $success = False;
    }
    function error_statement_handler($cmdstr, $statement)
    {
        global $db_conn, $success;
        $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
        echo "<script type='text/javascript'>";
        echo "alert('Cannot execute your Input Due to this error: " . htmlentities($e['message']) . "')";
        echo "</script>";
        $success = False;
    }

    function executePlainSQL($cmdstr)
    { //takes a plain (no bound variables) SQL command and executes it
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

    function executeBoundSQL($cmdstr, $list)
    {
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
                unset($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                error_statement_handler($cmdstr, $statement);
            }
        }
    }

    function printResult($result)
    { //prints results from a select statement
        // echo "<br>Retrieved data from table A2:<br>";
        echo "<table>";
        echo "<tr>
            <th>aID</th>
            <th>aName</th>
            <th>aAge</th>
            <th>aNationality</th>
            <th>aMissions</th>
            <th>aTrainingStart</th>
            <th>aTID</th>
        </tr>";

        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>"
                . $row[0] . "</td><td>"
                . $row[1] . "</td><td>"
                . $row[2] . "</td><td>"
                . $row[3] . "</td><td>"
                . $row[4] . "</td><td>"
                . $row[5] . "</td><td>"
                . $row[6] . "</td></tr>";
        }

        echo "</table>";
    }

    function connectToDB()
    {
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

    function disconnectFromDB()
    {
        global $db_conn;

        debugAlertMessage("Disconnect from Database");
        OCILogoff($db_conn);
    }

    function handleUpdateRequest()
    {
        global $db_conn, $success;

        $id = preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['aID']);
        $new_name = preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['newName']);
        $new_age = $_POST['newAge'];
        $new_nat = preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['newNationality']);
        $new_miss = $_POST['newMissions'];
        $new_tstart = $_POST['newTrainingStart'];
        $new_tid = $_POST['newTID'];

        if (empty($new_name)) {
            $result = executePlainSQL("SELECT aName FROM A2 WHERE aID='" . $id . "'");
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                $new_name = $row[0];
                // echo "<br>" . $new_name . "<br>";//or just use "echo $row[0]"
            }
        }
        if (empty($new_age)) {
            $result = executePlainSQL("SELECT aAge FROM A2 WHERE aID='" . $id . "'");
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                $new_age = $row[0];
                // echo "<br>" . $new_age . "<br>";//or just use "echo $row[0]"
            }
        }
        if (empty($new_nat)) {
            $result = executePlainSQL("SELECT aNationality FROM A2 WHERE aID='" . $id . "'");
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                $new_nat = $row[0];
                // echo "<br>" . $new_nat . "<br>";//or just use "echo $row[0]"
            }
        }
        if (empty($new_miss)) {
            $result = executePlainSQL("SELECT aMissions FROM A2 WHERE aID='" . $id . "'");
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                $new_miss = $row[0];
                // echo "<br>" . $new_miss . "<br>";//or just use "echo $row[0]"
            }
        }
        if (empty($new_tstart)) {
            $result = executePlainSQL("SELECT aTrainingStart FROM A2 WHERE aID='" . $id . "'");
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {

                $new_tstart = date_create($row[0]);
                $new_tstart = date_format($new_tstart, "Y-m-d");
                // echo "<tr><td>" . $new_tstart . "</td><tr>";//or just use "echo $row[0]"
            }
        }
        if (empty($new_tid)) {
            $result = executePlainSQL("SELECT aTID FROM A2 WHERE aID='" . $id . "'");
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                $new_tid = $row[0];
                // echo "<br>" . $new_miss . "<br>";//or just use "echo $row[0]"
            }
        }

        executePlainSQL("UPDATE A2 SET aID='" . $id .
            "', aName = '" . $new_name .
            "', aAge = '" . $new_age .
            "', aNationality = '" . $new_nat .
            "', aMissions = '" . $new_miss .
            "', aTrainingStart = TO_DATE('" . $new_tstart .
            "', 'YYYY-MM-DD'), aTID = '" . $new_tid .
            "' WHERE aID='" . $id . "'");
        OCICommit($db_conn);
        if ($success) {
            echo "<script type='text/javascript'>";
            echo "alert('Successful Update: ')";
            echo "</script>";
        }
        handleShowRequest();
    }

    function handleResetRequest()
    {
        global $db_conn;
        // Drop old table
        executePlainSQL("DROP TABLE A2 CASCADE CONSTRAINTS");
        // Create new table
        executePlainSQL("CREATE TABLE A2 (aID INTEGER PRIMARY KEY, aName VARCHAR(20), aAge INTEGER, aNationality VARCHAR(20), aMissions INTEGER, aTrainingStart DATE, aTID INTEGER, FOREIGN KEY(aTID, aTrainingStart) REFERENCES A1(aTID, aTrainingStart) ON DELETE CASCADE)");
        executePlainSQL("INSERT INTO A2 VALUES (101, 'Travis', 34, 'American', 5, TO_DATE('2020/11/09', 'YYYY-MM-DD'), 1)");
        executePlainSQL("INSERT INTO A2 VALUES (102, 'Steven', 30, 'Canadian', 1, TO_DATE('1990/07/11', 'YYYY-MM-DD'), 2)");
        executePlainSQL("INSERT INTO A2 VALUES (103, 'Ariana', 41, 'Russian', 7, TO_DATE('2008/04/13', 'YYYY-MM-DD'), 3)");
        executePlainSQL("INSERT INTO A2 VALUES (104, 'Jimmy', 39, 'Italian', 2, TO_DATE('2020/11/21', 'YYYY-MM-DD'), 4)");
        executePlainSQL("INSERT INTO A2 VALUES (105, 'Travis', 34, 'American', 5, TO_DATE('2020/11/09', 'YYYY-MM-DD'), 5)");
        executePlainSQL("INSERT INTO A2 VALUES (106, 'Stephanie', 34, 'American', 4, TO_DATE('2013-02-12', 'YYYY-MM-DD'), 6)");
        executePlainSQL("INSERT INTO A2 VALUES (107, 'Alina', 40, 'Russian', 6, TO_DATE('2013-12-26', 'YYYY-MM-DD'), 7)");
        OCICommit($db_conn);
        handleShowRequest();
    }

    function handleInsertRequest()
    {
        global $db_conn, $success;

        //Getting the values from user and insert data into the table
        $tuple = array(
            ":bind1" => preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['insAID']),
            ":bind2" => preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['insAName']),
            ":bind3" => $_POST['insAAge'],
            ":bind4" => preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['insANationality']),
            ":bind5" => $_POST['insAMissions'],
            ":bind6" => $_POST['insATStart'],
            ":bind7" => $_POST['insATID']
        );

        $alltuples = array(
            $tuple
        );

        executeBoundSQL("insert into A2 values (:bind1, :bind2, :bind3, :bind4, :bind5, TO_DATE(:bind6, 'YYYY-MM-DD'), :bind7)", $alltuples);
        if ($success) {
            echo "<script type='text/javascript'>";
            echo "alert('Successful Insert: ')";
            echo "</script>";
        }
        OCICommit($db_conn);
        handleShowRequest();
    }

    function handleShowRequest()
    {
        global $db_conn;

        $result = executePlainSQL("SELECT * FROM A2 ORDER BY aID ASC");
        printResult($result);
        // if (($row = oci_fetch_row($result)) != false) {
        //     echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
        // }
    }

    function handleDeleteRequest()
    {
        global $db_conn, $success;
        $del_id = $_POST['delAID'];
        executePlainSQL("DELETE FROM A2 WHERE aID='" . $del_id . "'");
        if ($success) {
            echo "<script type='text/javascript'>";
            echo "alert('Successful Delete: ')";
            echo "</script>";
        }
        OCICommit($db_conn);
        handleShowRequest();
    }

    function handleSelectRequest()
    {
        global $success;
        $fields = $_POST['fields'];
        $operators = $_POST['operators'];
        $values = preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['values']);
        $los = $_POST['logicalOperators'];
        $queryParts = [];
        for($i = 0; $i < count($fields); $i++) {
            $queryParts[] = $fields[$i] . ' ' . $operators[$i]. ' \'' . $values[$i] . '\'';
            if ($i < count($operators) - 1) {
                $queryParts[] = $los[$i];
            }
        }

        $query = implode(' ', $queryParts);
        $result = executePlainSQL("SELECT * FROM A2 WHERE " . $query . "");
        // if ($success) {
        //     echo "<script type='text/javascript'>";
        //     echo "alert('Successful Selection: ')";
        //     echo "</script>";
        // }
        printResult($result);
    }

    // HANDLE ALL POST ROUTES
    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
    function handlePOSTRequest()
    {
        if (connectToDB()) {
            if (array_key_exists('resetTablesRequest', $_POST)) {
                handleResetRequest();
            } else if (array_key_exists('updateQueryRequest', $_POST)) {
                handleUpdateRequest();
            } else if (array_key_exists('insertQueryRequest', $_POST)) {
                handleInsertRequest();
            } else if (array_key_exists('deleteQueryRequest', $_POST)) {
                handleDeleteRequest();
            } else if (array_key_exists('selectRequest', $_POST)) {
                handleSelectRequest();
            }

            disconnectFromDB();
        }
    }

    // HANDLE ALL GET ROUTES
    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
    function handleGETRequest()
    {
        if (connectToDB()) {
            if (array_key_exists('showA2Tuples', $_GET)) {
                handleShowRequest();
            }

            disconnectFromDB();
        }
    }

    if (
        isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])
        || isset($_POST['deleteSubmit']) || isset($_POST['selectSubmit'])
    ) {
        handlePOSTRequest();
    } else if (isset($_GET['showA2TupleRequest'])) {
        handleGETRequest();
    }
    ?>
</body>

</html>
