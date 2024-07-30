<?php
    $success = True; //keep track of errors so it redirects the page only if there are no errors
    $db_conn = OCILogon("", "", ""); // username, password, path to your database
    // $q = "start ./sqlScript.sql";
    // OCIExecute(OCI_PARSE($db_conn, $q));
    // OCICommit($db_conn);

    $query = "SELECT table_name FROM user_tables";
    $statement = OCI_PARSE($db_conn, $query);
    $selected_table = "---";
    OCIExecute($statement);
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

    <h3>Select Projection: </h3>
    <div class="panel">
        <form class="form-inline" action="projection.php" method="post">
            <input type="hidden" id="projectionRequest" name="projectionRequest">
            <label for="table">Select A Table:</label>
            <select id="table" name="table" onchange="this.form.submit()">
                <option value="---">---</option>
                <?php
                    while($row = oci_fetch_assoc($statement)) {
                        echo "<option value=\"{$row['TABLE_NAME']}\">{$row['TABLE_NAME']}</option>";
                    }
                ?>
            </select>
            <?php
                $success = True; //keep track of errors so it redirects the page only if there are no errors
                $db_conn = OCILogon("", "", ""); // username, password, path to your database
                global $selected_table;
                if (isset($_POST['table'])) {
                    if (array_key_exists('projectionRequest', $_POST)) {
                        $table = $_POST['table'];
                        $selected_table = $table;
                        $query = "SELECT table_name, column_name FROM USER_TAB_COLUMNS WHERE table_name='" . $table . "'";
                        $statement = OCI_PARSE($db_conn, $query);
                        OCI_EXECUTE($statement);
                    }
                }
            ?>
        </form>
        <form class="form-inline" action="" method="post">
            <input type="hidden" id="project" name="project">
            <label>Chosen Table:</label>
            <select id="chosenTable" name="chosenTable">
                <?php
                    echo "<option value=\"{$selected_table}\">{$selected_table}</option>";
                ?>
            </select>
            <label>Select Columns:</label>
            <br>
            <?php
                while($row = oci_fetch_assoc($statement)) {
                    $columnName = $row["COLUMN_NAME"];
                    echo "<label><input type=\"checkbox\" name=\"tables[]\" value=\"$columnName\"> $columnName</label><br>";
                }
            ?>
            <input type="submit" value="Project" name="projectSubmit">
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

        function error_parse_handler($cmdstr)
        {
            global $db_conn, $success;
            echo "<br> <p class='error-handler'>Could not parse the command due to this error: ";
            $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
            echo htmlentities($e['message']);
            echo "</p>";
            $success = False;
        }
        function error_statement_handler($cmdstr, $statement)
        {
            global $db_conn, $success;
            echo "<br> <p class='error-handler'>Cannot execute your Input Due to this error: ";
            $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
            echo htmlentities($e['message']);
            echo "</p>";
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

        function printResult($result, $selectedColumns) { //prints results from a select statement
            echo "<table style='color:white;'>";
            echo "<tr>";
            foreach($selectedColumns as $column) {
                echo "<th>$column</th>";
            }
            echo "</tr>";
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr>";
                foreach($selectedColumns as $column) {
                    echo "<td>{$row[$column]}</td>";
                }
                echo "</tr>";
            }

            echo "</table>";
        }

        function handleShowRequest() {
            global $db_conn;
            $chosenTable = $_POST['chosenTable'];
            $columns = $_POST['tables'];
            if (!empty($columns)) {
                $selectedColumns = implode(', ', $columns);
                $result = executePlainSQL("SELECT " . $selectedColumns . " FROM " . $chosenTable . "");
                printResult($result, $columns);
            }

            // $result = executePlainSQL("SELECT table_name, column_name FROM USER_TAB_COLUMNS WHERE table_name='" . $table . "'");
            // printResult($result);
            // if (($row = oci_fetch_row($result)) != false) {
            //     echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
            // }
        }

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('project', $_POST)) {
                    handleShowRequest();
                }

                disconnectFromDB();
            }
        }

		if (isset($_POST['projectSubmit'])) {
            // echo "<br>projection request <br>";
            handlePOSTRequest();
        }
    ?>
</body>

</html>
