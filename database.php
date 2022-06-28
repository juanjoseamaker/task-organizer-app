<?php
$conn = new SQLite3("../database.db") or die("Unable to open database!");

function create_database()
{
    global $conn;

    $conn->exec("CREATE TABLE IF NOT EXISTS `user`(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username TEXT, password TEXT)");

    $user_count = $conn->query("SELECT COUNT(*) as count FROM `user`")->fetchArray()["count"];

    if ($user_count == 0) {
        $conn->exec("INSERT INTO `user` (username, password) VALUES('admin', 'admin')");
    }

    $conn->exec("CREATE TABLE IF NOT EXISTS `time_frame`(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, start TEXT, end TEXT, FOREIGN KEY(user_id) REFERENCES user(id))");
    $conn->exec("CREATE TABLE IF NOT EXISTS `task`(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, time_frame_id INTEGER NOT NULL, description TEXT, done BOOLEAN, FOREIGN KEY(user_id) REFERENCES user(id), FOREIGN KEY(time_frame_id) REFERENCES time_frame(id))");
}

function get_time_frames($user_id)
{
    global $conn;

    $time_frames = [];

    $query = $conn->query("SELECT id, start, end FROM `time_frame` WHERE `user_id`='$user_id'");
    while ($row = $query->fetchArray()) {
        $time_frames[] = [
            "id" => $row["id"],
            "start" => $row["start"],
            "end" => $row["end"],
        ];
    }

    return $time_frames;
}

function get_tasks($user_id, $time_frame_id) {
    global $conn;

    $tasks = [];

    $query = $conn->query("SELECT id, description, done FROM `task` WHERE `time_frame_id`='$time_frame_id' AND `user_id`='$user_id'");
    while ($row = $query->fetchArray()) {
        $tasks[] = [
            "id" => $row["id"],
            "description" => $row["description"],
            "done" => $row["done"],
        ];
    }

    return $tasks;
}

create_database();
