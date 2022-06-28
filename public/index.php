<?php
require_once "../database.php";

session_start();

if (!isset($_SESSION["user"])) {
    header("Location: account.php");
    return;
}

$user = $_SESSION["user"];
$time_frames = get_time_frames($user["id"]);

usort($time_frames, function($a, $b) {
    $date_a = new DateTime($a["start"]);
    $date_b = new DateTime($b["start"]);
    return $date_a < $date_b;
});
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Task Organizer App</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
</head>

<body>
    <div class="jumbotron text-center bg-secondary">
        <div class="container">
            <div class="row">
                <div class="col-xl-6">
                    <h1 class="display-4">Task Organizer App</h1>
                    <p class="lead">Organize your tasks and goals - Created by Juan José Aristizábal</p>
                    <hr class="my-4">
                    <p>Create a task or goal - Record your progress</p>
                </div>
                <div class="col">
                    <p class="my-4">You are logged as <?= $user["username"] ?></p>
                    <a href="/account.php" class="btn btn-primary">Account settings</a>
                </div>
                <div class="col">
                    <form action="add.php" method="POST">
                        <div class="container">
                            <div class="row">
                                <p class="my-4">Add a time frame</p>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="start">Start date</label>
                                </div>
                                <div class="col-xl-8">
                                    <input type="date" class="form-control" id="start" name="start" value="2022-01-00" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="end">End date</label>
                                </div>
                                <div class="col-xl-8">
                                    <input type="date" class="form-control" id="end" name="end" value="2022-01-00" required>
                                </div>
                            </div>
                            <div class="row">
                                <div clas="col">
                                    <button type="submit" class="btn btn-primary" name="time_frame" value="time_frame">Add time frame</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <br>

    <div class="container">
        <div class="row">
            <?php foreach ($time_frames as $time_frame) :
                $today_date = new DateTime();
                $start_date = new DateTime($time_frame["start"]);
            ?>
                <div class="col-xl-6 bg-primary border border-dark">
                    <h2>From <?= $time_frame["start"] ?> to <?= $time_frame["end"] ?></h2>

                    <?php if ($start_date < $today_date) :
                        $difference = $today_date->diff($start_date);
                    ?>
                        <h3>Started <?= $difference->days ?> days and <?= $difference->h ?> hours ago</h3>
                    <?php else : ?>
                        <h3>Has not started yet</h3>
                    <?php endif ?>

                    <form action="add.php" method="POST">
                        <div class="container">
                            <input type="hidden" id="time_frame_id" name="time_frame_id" value="<?= $time_frame["id"] ?>">
                            <div class="row">
                                <div class="col-xl-2">
                                    <label for="description">Add a task</label>
                                </div>
                                <div class="col-xl-6">
                                    <input type="text" class="form-control" id="description" name="description" placeholder="Description" required>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-warning" name="task" value="task">Add task</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="container">
                        <?php
                        $tasks = get_tasks($user["id"], $time_frame["id"]);
                        foreach ($tasks as $task) :
                            $checked = ($task["done"] == 0) ? "" : "checked";
                        ?>
                            <div class="row text-nowrap">
                                <div class="col">
                                    <p><b><?= $task["description"] ?> - <input type="checkbox" <?= $checked ?> onclick="return false;"></b></p>
                                </div>
                                <div class="col">
                                    <form action="edit.php" method="POST">
                                        <input type="hidden" id="task_id" name="task_id" value="<?= $task["id"] ?>">
                                        <input type="hidden" id="task_id" name="done" value="<?= ($task["done"] == 0) ? "true" : "false" ?>">
                                        <button type="submit" class="btn btn-warning" name="task" value="task">Edit</button>
                                    </form>
                                </div>
                                <div class="col">
                                    <form action="delete.php" method="POST">
                                        <input type="hidden" id="task_id" name="task_id" value="<?= $task["id"] ?>">
                                        <button type="submit" class="btn btn-danger" name="task" value="task">Delete task</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>

                    <hr class="my-4">

                    <form action="delete.php" method="POST">
                        <input type="hidden" id="time_frame_id" name="time_frame_id" value="<?= $time_frame["id"] ?>">
                        <button type="submit" class="btn btn-danger" name="time_frame" value="time_frame">Delete time frame</button>
                    </form>

                    <br>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</body>

</html>