<?php
include('connection.php');
$is_Logged = false;
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : "";
$password = isset($_COOKIE['password']) ? $_COOKIE['username'] : "";
$db = new PDO('mysql:host=localhost;dbname=world', 'db', 'qIYlszWCCsWgGasdGqN');
$all_blogs = [];
if (isset($_SESSION['username'])) {

    $add_user_stmt = $db->prepare('SELECT * FROM accounts WHERE username = ?');
    $user = $add_user_stmt->execute(array($_SESSION['username']));
    $blogs_stmt = $db->prepare('SELECT blogs.title, blogs.body FROM blogs JOIN accounts ON  blogs.authors = ?');
    $all_blogs = $blogs_stmt->execute(array($user['id']));
    if ($user['password'] == $_SESSION['password']) {
        $is_Logged = true;
    }
}
if ($is_Logged) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $_SESSION['username'] = $username;
    $_SESSION['password'] = $password;
    if ($_POST['remember']) {
        setcookie("username", $username, time() + 60 * 60 * 24 * 365);
        setcookie("username", $password, time() + 60 * 60 * 24 * 365);
    }
} else {
    $title = $_POST['title'];
    $body = $_POST['body'];
    $create_post_stmt = $db->prepare("INSERT INTO blogs(title, body, author) VALUES(?,?,?)");
    $create_post_stmt->execute($title, $body, $user['id']);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>My Personal Page</title>
    <link href="style.css" type="text/css" rel="stylesheet"/>
</head>

<body>
<?php include('header.php'); ?>
<!-- Show this part if user is not signed in yet -->
<div class="twocols" style=<?php $is_Logged ? "display: none" : "" ?>>
    <form action="index.php" method="post" class="twocols_col">
        <ul class="form">
            <li>
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value=<?= $username ?>/>
            </li>
            <li>
                <label for="pwd">Password</label>
                <input type="password" name="pwd" id="pwd" value=<?= $password ?>/>
            </li>
            <li>
                <label for="remember">Remember Me</label>
                <input type="checkbox" name="remember" id="remember" checked/>
            </li>
            <li>
                <input type="submit" value="Submit"/> &nbsp; Not registered? <a href="register.php">Register</a>
            </li>
        </ul>
    </form>
    <div class="twocols_col">
        <h2>About Us</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consectetur libero nostrum consequatur dolor.
            Nesciunt eos dolorem enim accusantium libero impedit ipsa perspiciatis vel dolore reiciendis ratione quam,
            non sequi sit! Lorem ipsum dolor sit amet, consectetur adipisicing elit. Optio nobis vero ullam quae.
            Repellendus dolores quis tenetur enim distinctio, optio vero, cupiditate commodi eligendi similique
            laboriosam maxime corporis quasi labore!</p>
    </div>
</div>
<div style=<?php $is_Logged ? "display: none" : "" ?>>
    <!-- Show this part after user signed in successfully -->
    <div class="logout_panel"><a href="register.php">My Profile</a>&nbsp;|&nbsp;<a href="index.php?logout=1">Log Out</a>
    </div>
    <h2>New Post</h2>
    <form action="index.php" method="post">
        <ul class="form">
            <li>
                <label for="title">Title</label>
                <input type="text" name="title" id="title"/>
            </li>
            <li>
                <label for="body">Body</label>
                <textarea name="body" id="body" cols="30" rows="10"></textarea>
            </li>
            <li>
                <input type="submit" value="Post"/>
            </li>
        </ul>
    </form>
</div>
<?php foreach ($all_blogs as $blog) { ?>
    <div class="onecol">
        <div class="card">
            <h2><?= $blog['title'] ?></h2>
            <h5><?= $user['username'] ?></h5>
            <p><?= $blog['body'] ?></p>
        </div>
    </div>
<?php } ?>
</body>
</html>