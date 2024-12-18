<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right, #74ebd5, #acb6e5);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        .signup-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .signup-container .icon {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .signup-container .icon i {
            font-size: 3rem;
            color: #007bff;
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            color: #333;
            margin-bottom: 10px;
        }

        p {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input {
            width: 90%;
            padding: 10px;
            border: 1px solid #cccccc;
            border-radius: 5px;
            font-size: 1rem;
            display: block;
            margin: 10px auto;
        }

        .form-group input:focus {
            outline: none;
            border-color: #007bff;
        }

        .form-btn {
            text-align: center;
            margin-top: 20px;
        }

        .form-btn button {
            padding: 8px 10px; 
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #ffffff;
            cursor: pointer;
            display: block;
            margin: 10px auto;
            transition: background-color 0.3s;
        }

        .form-btn button:hover {
            background-color: #0056b3;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
            color: #666;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="icon">
            <i class="fas fa-user-plus"></i>
        </div>
        <h1>Create an Account</h1>
        <p>Join us and start your journey today!</p>

        <form action="signup_process.php" method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm your password" required>
            </div>

            <div class="form-btn">
                <button type="submit">
                    Sign Up
                </button>
            </div>
        </form>

        <p class="login-link">
            Already have an account? <a href="login.php">Login here</a>.
        </p>
    </div>
</body>
</html>
