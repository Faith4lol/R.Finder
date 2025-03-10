<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Delius&family=Delius+Swash+Caps&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="login-page">
    <div class="container" id="container">
        <!-- Sign Up Form -->
        <div class="sign-up">
          <form action="{{ route('register') }}" method="POST">
            @csrf
            <h1>Create Account</h1>
            <div class="icons">
              <a href="#" class="icon"><i class="fa-brands fa-meta"></i></a>
              <a href="#" class="icon"><i class="fa-brands fa-instagram"></i></a>
              <a href="#" class="icon"><i class="fa-brands fa-google"></i></a>
            </div>
            <span>or use email for registration</span>
            <input type="text" name="name" placeholder="Username" required />
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="password" placeholder="Password" required />
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required />
            <button type="submit">Sign Up</button>
          </form>
        </div>

        <!-- Sign In Form -->
        <div class="sign-in">
          <form action="{{ route('login') }}" method="POST">
            @csrf
            <h1>Sign In</h1>
            <div class="icons">
              <a href="#" class="icon"><i class="fa-brands fa-meta"></i></a>
              <a href="#" class="icon"><i class="fa-brands fa-instagram"></i></a>
              <a href="#" class="icon"><i class="fa-brands fa-google"></i></a>
            </div>
            <span>or use your email and password</span>
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="password" placeholder="Password" required />
            <a href="{{ route('password.request') }}">Forgot password?</a>
            <button type="submit">Sign In</button>
          </form>
        </div>

        <!-- Toggle Container -->
        <div class="toogle-container">
          <div class="toogle">
            <div class="toogle-panel toogle-left">
              <h1>Welcome Back!</h1>
              <p>If you already have an account</p>
              <button class="hidden" id="login">Sign In</button>
            </div>
            <div class="toogle-panel toogle-right">
              <h1>Hello, User!</h1>
              <p>If you don't have an account</p>
              <button class="hidden" id="register">Sign Up</button>
            </div>
          </div>
        </div>
      </div>

      <script src="{{ asset('js/login.js') }}"></script>
</body>
</html>
