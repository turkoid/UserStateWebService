<?php
/**
 * Created by PhpStorm.
 * User: turkoid
 * Date: 8/11/2016
 * Time: 15:34
 */

require("config.inc.php");
$requestOperation = $_SERVER["REQUEST_METHOD"];
$sql = "";
$sqlGet = ""; //returns the updated or newly created user if set
switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        //get the user with given id or if none passed, return all
        $sql = "select * from tUsers";
        if (isset($_GET["id"])) {
            $sql .= " where id = " . $_GET["id"];
        }
        break;
    case "POST":
        //create a new user
        if (isset($_POST["name"])) {
            $sql = "insert into tUsers";
            $cols = "name";
            $values = $_POST["name"];
            if (isset($_POST["status"])) {
                $cols .= ",status";
                $values .= "," . $_POST["status"];
            }
            $sql .= "(" . $cols .") values (" . $values . ")";
            $sqlGet = "select * from tUsers where id = last_insert_id()";
        }
        break;
    case "PUT":
        //update user with id
        if (isset($_GET["id"])) {
            $params = array();
            parse_str(file_get_contents('php://input'), $params);  //// F U - took me way to long to figure this out
            if (isset($params["name"])) {
                $sql .= "name = '" . $params["name"] . "'";
            }
            if (isset($params["status"])) {
                if ($sql != "") {
                    $sql .= ", ";
                }
                $sql .= "status = {$params["status"]}";
            }
            if ($sql != "") {
                $sql = "update tUsers set " . $sql . " where id = " . $_GET["id"];
            }
            $sqlGet = "select * from tUsers where id = " . $_GET["id"];
        }
        break;
    case "DELETE":
        //delete a user with given id
        if (isset($_GET["id"])) {
            $sql = "delete from tUsers where id = " . $_GET["id"];
        }
        break;
}

//return an empty array by default
$arrUsers = array();
if ($sql != "") {
    $connection = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName) or die("Error connecting to DB " . mysqli_error($connection));
    $result = mysqli_query($connection, $sql) or die("Error in selecting " . mysqli_error($connection));
    if ($sqlGet != "") {
        $result = mysqli_query($connection, $sqlGet) or die("Error in selecting " . mysqli_error($connection));
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $arrUsers[] = $row;
    }
}
echo json_encode($arrUsers);
