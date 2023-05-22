<?php
session_start();

// Include the necessary files
include "includes/config.php"; // Configuration file
include "includes/header.php"; // Header file
include "includes/db.php"; // Database connection file

// Check if the 'url' parameter is set
if (isset($_GET['url'])) {
    $url = $_GET['url'];
    $stringArray = explode("/", $url);
    $page = $stringArray[count($stringArray)-1];
} else {
    $page = 'index'; // Default page
}

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    // Retrieve user data from the database
    $username = $_SESSION['username'];
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        die("Database query failed.");
    }

    $row_count = mysqli_num_rows($result);

    if ($row_count == 1) {
        // Successful query
        $row = mysqli_fetch_assoc($result);
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
    }
}

// Login process
if (!isset($_SESSION['username'])) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];

        // Check if all fields are filled
        if (empty($username) || empty($password)) {
            echo '<div class="alert alert-danger text-center">Please fill in all fields.</div>';
        } else {
            // Check username and password against the database
            $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
            $result = mysqli_query($connection, $query);

            if (!$result) {
                die("Database query failed.");
            }

            $row_count = mysqli_num_rows($result);

            if ($row_count == 1) {
                // Successful login
                echo '<div class="alert alert-success text-center">Login successful!</div>';

                // Retrieve user data
                $row = mysqli_fetch_assoc($result);
                $first_name = $row['first_name'];
                $last_name = $row['last_name'];
                $username = $row['username'];

                // Set the session variable for the logged-in user
                $_SESSION['username'] = $username;
            } else {
                // Failed login
                echo '<div class="alert alert-danger text-center">Invalid username or password.</div>';
            }
        }
    }
}
?>

<body>

    <!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <!-- Navigation toggle button -->
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <!-- Brand/logo -->
            <a class="navbar-brand" href="index.php">Homework Blog</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <?php if (!isset($_SESSION['username'])) : ?>
                    <!-- Register link -->
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
                <!-- Contact link -->
                <li><a href="contact.php">Contact</a></li>
                <?php if (isset($_SESSION['username'])) : ?>
                    <!-- New Post link (visible for logged-in users) -->
                    <li><a href="new_post.php">New Post</a></li>
                <?php endif; ?>
                <!-- About Us link -->
                <li><a href="about_us.php">About Us</a></li>
            </ul>
            <?php if (isset($_SESSION['username'])) : ?>
                <!-- User dropdown menu (visible for logged-in users) -->
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-user"></i>
                            <!-- Display user's full name and username -->
                            <?php echo $last_name . ' ' . $first_name . ' (' . $username . ')' ?>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- Logout link -->
                            <li><a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a></li>
                        </ul>
                    </li>
                </ul>
            <?php else : ?>
            
            <?php endif; ?>
        </div>
   
    </div>
    
</nav>


<!-- Blog Entries Column -->
<div class="col-md-8">
    <h1 class="page-header">Homework Blog</h1>

    <?php
    // Retrieve all posts from the database
    $query = "SELECT post_id, post_title, post_author, DATE_FORMAT(post_date, '%Y-%m-%d') AS post_date, TIME_FORMAT(post_date, '%H:%i:%s') AS post_time, post_image, post_content FROM posts ORDER BY post_date DESC";
    $select_all_posts_query = mysqli_query($connection, $query);

    // Check if there are any posts
    if (mysqli_num_rows($select_all_posts_query) > 0) {
        $total_posts = mysqli_num_rows($select_all_posts_query);
        $posts_per_page = 10;
        $total_pages = ceil($total_posts / $posts_per_page);
    }

    // Determine the current page
    if (isset($_GET['page'])) {
        $current_page = $_GET['page'];
    } else {
        $current_page = 1;
    }

    // Calculate the starting post index and modify the query
    $starting_post_index = ($current_page - 1) * $posts_per_page;
    $query .= " LIMIT $starting_post_index, $posts_per_page";
    $select_limited_posts_query = mysqli_query($connection, $query);

    // Display each post
    while ($row = mysqli_fetch_assoc($select_limited_posts_query)) {
        $post_id = $row['post_id'];
        $post_title = $row['post_title'];
        $post_author = $row['post_author'];
        $post_date = $row['post_date'];
        $post_content = $row['post_content'];
        $post_image = $row['post_image'];
        $post_excerpt = substr($post_content, 0, 100);
        ?>

        <div class="post">
            <h2><a href="post.php?id=<?php echo $post_id; ?>"><?php echo $post_title; ?></a></h2>
            <p class="lead">by <a href="index.php"><?php echo $post_author; ?></a></p>
            <p><span class="glyphicon glyphicon-time"></span> <?php echo $post_date; ?></p>
            <hr>
            <?php if (!empty($post_image)) : ?>
                <img class="img-responsive" src="<?php echo $post_image; ?>" alt="" width="300" height="150">
            <?php endif; ?>
            <hr>
            <p><?php echo $post_excerpt; ?></p>
            <a class="btn btn-primary" href="post.php?id=<?php echo $post_id; ?>">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>
            <hr>
        </div>

    <?php
    }
 // Pagination links
 ?>
 <ul class="pager">
     <?php
     for ($i = 1; $i <= $total_pages; $i++) {
         echo "<li><a href='index.php?page={$i}'>{$i}</a></li>";
     }
     ?>
 </ul>
 <?php

// buttons

if ($current_page == 1 && $total_posts > $posts_per_page) {
    // First page, show "Next" button
    ?>
    <ul class="pager">
        <li class="disabled"><a href="#">Previous</a></li>
        <li><a href="index.php?page=<?php echo $current_page + 1; ?>">Next</a></li>
    </ul>
    <?php
} elseif ($current_page > 1 && $current_page < $total_pages) {
    // Middle pages, show "Previous" and "Next" buttons
    ?>
    <ul class="pager">
        <li><a href="index.php?page=<?php echo $current_page - 1; ?>">Previous</a></li>
        <li><a href="index.php?page=<?php echo $current_page + 1; ?>">Next</a></li>
    </ul>
    <?php
} elseif ($current_page == $total_pages && $total_posts > $posts_per_page) {
    // Last page, show "Previous" button
    ?>
    <ul class="pager">
        <li><a href="index.php?page=<?php echo $current_page - 1; ?>">Previous</a></li>
        <li class="disabled"><a href="#">Next</a></li>
    </ul>
    <?php
}
?>

</div>
<!-- Blog Sidebar Widgets Column -->
<div class="col-md-4">
    <?php if (!isset($_SESSION['username']) && $_SERVER["REQUEST_METHOD"] === "POST") : ?>
        <?php
        // User login
        $username = $_POST["username"];
        $password = $_POST["password"];
        if (empty($username) || empty($password)) {
            echo '<div class="alert alert-danger text-center">Please fill in all fields.</div>';
        } else {
            // Check username and password in the database
            $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
            $result = mysqli_query($connection, $query);
            if (!$result) {
                die("Database query failed.");
            }

            $row_count = mysqli_num_rows($result);
        }
        ?>
    <?php endif; ?>

    <?php if (!isset($_SESSION['username'])) : ?>
        <div class="well">
            <form action="index.php" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
                <div class="text-center">
                    <p class="small">Don't have an account? <a href="register.php">Register</a></p>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>
</body>
</html>