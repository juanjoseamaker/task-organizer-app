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

    if (isset($_POST["login"])) {
        $username = SQLite3::escapeString($_POST["username"]);
        $password = SQLite3::escapeString($_POST["password"]);

        $user_count = $conn->query("SELECT COUNT(*) as count FROM `user` WHERE `username`='$username' AND `password`='$password'")->fetchArray()["count"];

        if ($user_count > 0) {
            $user_data = $conn->query("SELECT id, username, password FROM `user` WHERE `username`='$username' AND `password`='$password'")->fetchArray();
            $user = [
                "id" => $user_data["id"],
                "username" => $user_data["username"],
                "password" => $user_data["password"]
            ];

            $_SESSION["user"] = $user;

            echo "<div class='alert alert-success'>Login successful</div>";
        } else {
            echo "<div class='alert alert-warning'>Invalid username or password</div>";
        }

        header("refresh:5;url=index.php");
    } elseif (isset($_POST["signup"])) {
        $username = SQLite3::escapeString($_POST["username"]);
        $password = SQLite3::escapeString($_POST["password"]);

        $user_count = $conn->query("SELECT COUNT(*) as count FROM `user` WHERE `username`='$username'")->fetchArray()['count'];

        if ($user_count == 0) {
            $conn->exec("INSERT INTO `user` (username, password) VALUES('$username', '$password')");

            echo "<div class='alert alert-success'>Signup successful</div>";
        } else {
            echo "<div class='alert alert-warning'>Username already used</div>";
        }

        header("refresh:5;url=index.php");
    } elseif (isset($_POST["unlogin"])) {
        session_destroy();
        header("Location: index.php");
    } elseif (isset($_POST["delete"])) {
        if (isset($_SESSION["user"])) {
            $user = $_SESSION["user"];

            session_destroy();

            $conn->exec("DELETE FROM `user` WHERE `username`='{$user["username"]}' AND `id`='{$user["id"]}'");

            echo "<div class='alert alert-success'>Delete successful</div>";
        } else {
            echo "<div class='alert alert-warning'>Delete failed</div>";
        }

        header("refresh:5;url=index.php");
    } else {
    ?>
        <div class="jumbotron text-center bg-secondary">
            <h1 class="display-4">Account Settings</h1>
            <p>Login or signup - Unlogin or delete account</p>
            <?php
            if (isset($_SESSION["user"])) :
                $user = $_SESSION["user"];
            ?>
                <p class="my-4">Logged as <?= $user["username"] ?></p>
                <div class="container">
                    <form action="account.php" method="POST">
                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary" name="unlogin" value="unlogin">Unlogin</button>
                                </div>
                                <div class="col">
                                    <a href="/index.php" class="btn btn-primary">Main page</a>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-danger" name="delete" value="delete">Delete</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            <?php else : ?>
                <div class="container">
                    <form action="account.php" method="POST" class="form-inline">
                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <label for="username">Username</label>
                                </div>
                                <div class="col-xl-8">
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="password">Password</label>
                                </div>
                                <div class="col-xl-8">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary" name="login" value="login">Login</button>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-primary" name="signup" value="signup">Signup</button>
                                </div>
                            </div>
                    </form>
                </div>
            <?php endif ?>
            <br>
        </div>
    <?php } ?>
</body>

</html>