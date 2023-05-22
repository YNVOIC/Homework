

<?php

session_start();
include "includes/header.php";
include "includes/db.php";
include "includes/config.php";


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


?>

<body>

<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <a class="navbar-brand" href="index.php">Homework Blog</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
            <?php if (!isset($_SESSION['username'])) : ?>
                    <li>
                        <a href="index.php">Login</a>
                    </li>
                <?php endif; ?>
            <?php if (!isset($_SESSION['username'])) : ?>
                    <li>
                        <a href="register.php">Register</a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="contact.php">Contact</a>
                </li>
                <?php if (isset($_SESSION['username'])) : ?>
                    <li>
                        <a href="new_post.php">New Post</a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="about_us.php">About Us</a>
                </li>
            </ul>
            <?php if (isset($_SESSION['username'])) : ?>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-user"></i>
                            <?php echo $last_name . ' ' . $first_name . ' (' . $username . ')' ?>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                        <li>
                                <a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                        </li>
                        </ul>
                    </li>
                </ul>
            <?php else : ?>
                <!-- Code for non-logged in users -->
            <?php endif; ?>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>

    <div class="container text-center">
  <h4>Contact Information:</h4>
  <p><strong>Company Name:</strong> ABC Corporation</p>
  <p><strong>Email:</strong> <a href="contact.php">info@abccorp.com</a></p>
  <p><strong>Location:</strong> Kecskemét, Hungary</p>
  <div class="embed-responsive embed-responsive-16by9">
    <iframe class="embed-responsive-item" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2726.3375296155727!2d19.66695091525771!3d46.89607994478184!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4743da7a6c479e1d%3A0xc8292b3f6dc69e7f!2sPallasz+Ath%C3%A9n%C3%A9+Egyetem+GAMF+Kar!5e0!3m2!1shu!2shu!4v1475753185783" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
  </div>
</div>
