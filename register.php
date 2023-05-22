<?php include "includes/header.php";
 include "includes/db.php";
 include "includes/config.php";
?>

<?php
$errors = array(); // Array to store errors

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Check if all fields are filled
    if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "Please fill in all fields.";
    }
    
    // Check if email is in valid format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format!";
    }
    
    // Check if password and confirm password match and are at least 6 characters long
    if ($password !== $confirm_password || strlen($password) < 6) {
        $errors[] = "Password does not match!";
    }
    
    // Check if email or username already exist in the database
    $email_query = "SELECT * FROM users WHERE email = '$email'";
    $email_result = mysqli_query($connection, $email_query);
    $username_query = "SELECT * FROM users WHERE username = '$username'";
    $username_result = mysqli_query($connection, $username_query);

    if (mysqli_num_rows($email_result) > 0) {
        $errors[] = "Email address is already taken!";
    }
    
    if (mysqli_num_rows($username_result) > 0) {
        $errors[] = "Username is already taken!";
    }

    // If there are any errors, display them
    if (!empty($errors)) {
        echo '<div class="alert alert-danger text-center">';
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
        echo '</div>';
    } else {
        // Save data to the database
        $query = "INSERT INTO users (first_name, last_name, username, email, password) VALUES ('$first_name', '$last_name', '$username', '$email', '$password')";
        $result = mysqli_query($connection, $query);
        
        if (!$result) {
            die("Database query failed.");
        } else {
            echo '<div class="alert alert-success text-center">Registration successful!</div>';
        }
        // Additional operations can be added here, such as redirecting to another page
    }
}
?>

<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                   <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">Homework Blog</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    
                <li>
                        <a href="index.php">Login</a>
                    </li>
                    
                    <li>
                        <a href="about_us.php">About Us</a>
                    </li>
                    <li>
                        <a href="contact.php">Contact</a>
                    </li>
                    <?php if (isset($_SESSION['username'])) : ?>
                    <li>
                        <a href="new_post.php">New Post</a>
                    </li>
                <?php endif; ?>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav> 
    <div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="well">
                <h3 class="text-center">Registration</h3>
                <form action="" method="post">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" class="form-control" name="first_name" placeholder="Enter your first name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" class="form-control" name="last_name" placeholder="Enter your last name" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" placeholder="Choose a username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Enter your email address" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Enter a password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" class="form-control" name="confirm_password" placeholder="Confirm your password" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>