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

    if (isset($_POST["task"])) {
        if (!empty($_POST["task_id"]) && !empty($_POST["done"])) {
            $task_id = SQLite3::escapeString($_POST["task_id"]);
            $done = ($_POST["done"] == "false") ? "0" : "1";

            $task_count = $conn->query("SELECT COUNT(*) as count FROM `task` WHERE `user_id`='{$user["id"]}' AND `id`='$task_id'")->fetchArray()["count"];

            if ($task_count == 1) {
                $conn->exec("UPDATE `task` SET `done`=$done WHERE `user_id`='{$user["id"]}' AND `id`='$task_id'");

                echo "<div class='alert alert-success'>Task successfully edited</div>";
            } else {
                echo "<div class='alert alert-warning'>Failed to edit task</div>";
            }
        } else {
            echo "<div class='alert alert-warning'>Failed to edit task</div>";
        }
    }

    header("refresh:5;url=index.php");
    ?>

    <a href="index.php" class="link-primary">Return to main page</a>
</body>

</html>