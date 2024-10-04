<?php
session_start();
include('table.php');
     $fname=$uname=$email=$phone=$password="";
     $logerror=$signerror=$signsuccess="";
     if($_SERVER['REQUEST_METHOD']==='POST')
     {
          if(isset($_POST['login']))
          {
               $email=mysqli_real_escape_string($conn,$_POST['email']);
               $password=mysqli_real_escape_string($conn,$_POST['password']);
               
               $sql="SELECT user_name, password, is_admin, user_type FROM sign_up WHERE email='$email'";
               $res=mysqli_query($conn,$sql);
                    if(mysqli_num_rows($res)>0)
                    {
                         $row=mysqli_fetch_assoc($res);
                         if(password_verify($password,$row['password']))
                         {
                              $_SESSION['user_name'] = $row['user_name'];
                              $_SESSION['user_type'] = $row['user_type'];  // Store user type in session
                              $_SESSION['is_admin'] = $row['is_admin'];
                              $logerror = "Login successful. Welcome, " . htmlspecialchars($row['user_name']) . "!";

                              $insertLog = "INSERT INTO log_in (email) VALUES ('$email')";
                              if (!mysqli_query($conn, $insertLog))
                              {
                                   die("Error logging login: " . mysqli_error($conn));
                              }
                              
                              if ($row['is_admin']) {
                                   header("Location: ../admin/test.php");
                               } else {
                                   header("Location: ../user/user_dashboard.php"); 
                               }
                               exit;
                              
                         }
                    else{
                         $logerror="incorrect password";
                         }        
                    }
               else{
                    $logerror="email not found";
                    }
          }
          elseif(isset($_POST['signup']))
          {
               $fname=mysqli_real_escape_string($conn,$_POST['fname']);
               $email=mysqli_real_escape_string($conn,$_POST['email']);
               $phone=mysqli_real_escape_string($conn,$_POST['phone']);
               $password=mysqli_real_escape_string($conn,$_POST['password']);

               $privacyAccepted = isset($_POST['privacy']);

               if (!$privacyAccepted) {
                    $signerror = "You must accept the terms and conditions.";
                }
               
               else{     
               $hasspass=password_hash($password,PASSWORD_DEFAULT);

               $sql="INSERT INTO sign_up (user_name,email,phone,password) VALUES ('$fname','$email','$phone','$hasspass')";
               if (mysqli_query($conn, $sql)) 
               {
                    $signsuccess="signup successfull";
               } 
               else
               {
                    $signerror="error in sign up".mysqli_error($conn);
               }
          }
     }
     }
?>
<html>
     <head>
          <title>Music Tips</title>
          <link rel="stylesheet" href="css/testhome.css">      
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
     </head>
     <body>
          <header><h2 class="logo">🦋ⓜ®</h2>
               <nav class="navigator">
                    <a href="testhome.php" class="active">Home</a>
                    <a href="#">About</a>
                    <a href="#">contact</a>
                    <button class="popup">Login</button>
               </nav>
          </header>
          
          <div class="allcont">
               
          <section class="home_container">
               <div class="homec">
               <!-- <div class="w w1">
                    <b><span>M </span><span> u </span><span> s </span><span> i </span><span> c </span></b><BR>
               </div>
               <div class="w w2">
                    <b><span> </span><span> T </span><span> i </span><span> p </span><span> s </span><span> <span> </span>& </span></b><br>
               </div>
               <div class="w w3">
                    <b><span> T </span><span> r </span><span> i </span><span> c </span><span> k </span><span> s </span></b>
               </div> -->
               <div class="word">
                    <div class="w w1">
                         <b>
                              <span>M</span><span>u</span><span>s</span><span>i</span><span>c</span><br>
                         </b>
                         <br>
                    </div>
                    <div class="w w2">
                        <b>
                            <span>T</span><span>i</span><span>p</span><span>s</span><span>&nbsp;</span><span>&</span>
                        </b>
                        <br>
                    </div>
                    <div class="w w3">
                        <b>
                            <span>T</span><span>r</span><span>i</span><span>c</span><span>k</span><span>s</span>
                        </b>
                    </div>
               </div>


               <p class="p1">
               <br><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Welcome to MUsic Tips and Tricks—your ultimate destination for enhancing your musical journey! Whether you're a seasoned musician or just starting,
                     our platform offers a treasure trove of valuable tips, tricks, and insights to help you master your craft. Explore a wide range of categories,
                      from instrument techniques and vocal exercises to music theory and production tips. Join our community, log in to share and see MUsic tips and tricks and develope your musical skills today! </p>
                      <br><br><br><br><br><br>
                    
               </div>

               <div class="contain">
                    <div class="wrapper">
                         <span class="close"><ion-icon name="close-outline"></ion-icon>
                         </span>
                         <!--LOGIN-->
                         <div class="form-box login">
                              <h2>Login</h2>
                              <?php if ($logerror): ?>
                              <script type="text/javascript">
                                   alert('<?php echo addslashes($logerror); ?>');
                              </script>
                              <?php endif; ?>
                              <form name="log" method="post" onsubmit="return validateLogin()">
                                   <div class="input-box">

                                        <span class="icon">
                                             <ion-icon name="mail"></ion-icon>
                                        </span>
                                        <input type="email" name="email">
                                        <label>Email</label>
                              
                                   </div>

                                   <div class="input-box">

                                        <span class="icon">
                                             <ion-icon name="lock-closed"></ion-icon>
                                        </span>
                                        <input type="password" name="password">
                                        <label>Password</label>

                                   </div>

                                   <div class="remember">

                                        <label><input type="checkbox">Remember me</label>
                                        <a href="forgot_password.php">Forgot Password</a>

                                   </div>
                                        <button type="submit" name="login" class="btn">Login</button>
                                   <div class="register">

                                        <p>Don't have an account?<a href="#" class="register-link">Register</a></p>
                                   </div>
                              </form>
                         </div>
                              
                         <!--SIGNUP-->
                         <div class="form-box register">
                              <h2>Signup</h2>
                              <?php if ($signerror): ?>
                                   <script type="text/javascript">
                                        alert('<?php echo addslashes($signerror); ?>');
                                   </script>
                              <?php elseif ($signsuccess): ?>
                                   <script type="text/javascript">
                                        alert('<?php echo addslashes($signsuccess); ?>');
                                   </script>
                              <?php endif; ?>
                         
                              <form name="sign" method="post" onsubmit="return validateSignup()">

                                   <div class="input-box">
                                        <span class="icon">
                                             <ion-icon name="person"></ion-icon>
                                        </span>
                                        <input type="text" name="fname">
                                        <label>Full Name</label>
                         
                                   </div>
                         
                                   <div class="input-box">
                                        <span class="icon">
                                             <ion-icon name="mail"></ion-icon>
                                        </span>
                                        <input type="email" name="email">
                                        <label>Email</label>
                         
                                   </div>
                         
                                   <div class="input-box">
                                        <span class="icon">
                                             <ion-icon name="call"></ion-icon>
                                        </span>
                                        <input type="tel" name="phone">
                                        <label>Phone</label>
                         
                                   </div>
                         
                                   <div class="input-box">
                                        <span class="icon">
                                             <ion-icon name="lock-closed"></ion-icon>
                                        </span>
                                        <input type="password" name="password">
                                        <label>Password</label>
                                   </div>

                                   <div class="remember">
                                        <label><input type="checkbox" name="privacy">I agree terms and conditions</label>

                                   </div>

                                   <button type="submit" name="signup" class="btn">Signup</button>

                                   <div class="register">
                                        <p>Already have an account?<a href="#" class="login-link">Login</a></p>
                                   </div>
                              </form>
                         </div>
                    </div>
               </div>
          </section>
          
          <section class="about_container">
               <div class="aboutc">
                    <h1 class="hh1">About</h1>
                         <p class="p2">
                              The Music Tips and Tricks Portal is a web-based application designed to 
                              provide musicians and music enthusiasts with a platform to share, view, and manage helpful music
                              related tips and tricks.
                         </p>
               </div>  
               <div class="contain1">
                    <img name="img1" src="image/2.jpg">
               </div>   
          </section> 

          <section class="contact_container">
               <div class="contac">
                    <h1 class="hh1">Contact Us</h1>
                         <p class="p2">
                              You can give feed back through the feedback section.<br>
                              Or other wise you can contact us one the social medias:<br>
                              <ion-icon name="logo-instagram"></ion-icon>&nbsp;&nbsp;&nbsp;&nbsp;
                              <ion-icon name="logo-whatsapp"></ion-icon>
                         </p>
               </div>     
          </section> 
          </div>
     </body>
     <script src="Js/scriptlog.js"></script> 
</html>