<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Task Organizer App</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
</head>

<body>
    <?php
    require_once "../database.php";

    session_start();

    if (!isset($_SESSION["user"])) {
        header("Location: account.php");
        return;
    }

    $user = $_SESSION["user"];

    if (isset($_POST["time_frame"])) {
        $start = (!empty($_POST["start"])) ? $_POST["start"] : date("Y-m-d");
        $end = (!empty($_POST["end"])) ? $_POST["end"] : date("Y-m-d", strtotime("tomorrow"));

        $time_frame_count = $conn->query("SELECT COUNT(*) as count FROM `time_frame` WHERE `user_id`='{$user["id"]}' AND `start`='$start' AND `end`='$end'")->fetchArray()["count"];

        if ($time_frame_count == 0) {
            $conn->exec("INSERT INTO `time_frame` (user_id, start, end) VALUES('{$user["id"]}', '$start', '$end')");

            echo "<div class='alert alert-success'>Time frame successfully created</div>";
        } else {
            echo "<div class='alert alert-warning'>Time frame already created</div>";
        }
    } elseif (isset($_POST["task"])) {
        if (!empty($_POST["time_frame_id"])) {
            $time_frame_id = SQLite3::escapeString($_POST["time_frame_id"]);
            $description = (!empty($_POST["description"])) ? SQLite3::escapeString($_POST["description"]) : "";

            $conn->exec("INSERT INTO `task` (user_id, time_frame_id, description, done) VALUES('{$user["id"]}', '$time_frame_id', '$description', 0)");

            echo "<div class='alert alert-success'>Task successfully created</div>";
        } else {
            echo "<div class='alert alert-warning'>Failed to create task</div>";
        }
    }

    header("refresh:5;url=index.php");
    ?>

    <a href="index.php" class="link-primary">Return to main page</a>
</body>

</html>