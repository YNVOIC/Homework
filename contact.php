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
        $email = $row['email'];

        // Save the email address in the session variable
        $_SESSION['email'] = $email;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Form data validation
    $errors = array();

    if (empty($name) || strlen($name) < 5) {
        $errors[] = "Name is required and must be at least 5 characters long.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($message) || strlen($message) < 10) {
        $errors[] = "Message must be at least 10 characters long.";
    }

    if (!empty($errors)) {
        // Display error messages
        echo '<div class="alert alert-danger">';
        foreach ($errors as $error) {
            echo "<p class='text-danger'>$error</p>";
        }
        echo '</div>';
    } else {
        // Save form data to the database
        // Make sure to have a valid database connection established before executing this query
        $query = "INSERT INTO messages (name, email, message) VALUES ('$name', '$email', '$message')";
        $result = mysqli_query($connection, $query);

        if (!$result) {
            die("Database query failed.");
        }

        // Display success message
        echo '<div class="alert alert-success">Message sent successfully!</div>';
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

    <div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h1>Contact Us</h1>

            <form action="" method="POST">
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control" id="name" name="name" <?php if(isset($_SESSION['username'])) { echo 'value="' . $_SESSION['username'] . '"'; } ?> <?php if(isset($_SESSION['username'])) { echo 'readonly'; } ?> required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" <?php if(isset($_SESSION['email'])) { echo 'value="' . $_SESSION['email'] . '"'; } ?> <?php if(isset($_SESSION['email'])) { echo 'readonly'; } ?> required>
    </div>
    <div class="form-group">
        <label for="message">Message</label>
        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary" name="submit">Send Message</button>
</form>
</body>

</html>