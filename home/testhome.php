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
               
               $sql="SELECT * FROM sign_up WHERE email='$email'";
               $res=mysqli_query($conn,$sql);
                    if(mysqli_num_rows($res)>0)
                    {
                         $row=mysqli_fetch_assoc($res);
                         if(password_verify($password,$row['password']))
                         {
                              $_SESSION['signup_id'] = $row['signup_id'];
                              $_SESSION['user_name'] = $row['user_name'];
                              $_SESSION['user_type'] = $row['user_type'];  // Store user type in session
                              $_SESSION['is_admin'] = $row['is_admin'];
                              $_SESSION['email'] = $row['email'];
                              $_SESSION['phone'] = $row['phone'];
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
          <title>Melophile</title>
          <link rel="icon" type="image/png" href="../image/indexnbg.png">
          <link rel="stylesheet" href="css/testhome.css">      
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
     <style>
         .popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: black;
    padding: 30px;  
    border-radius: 15px; 
    box-shadow: 0 15px 40px rgba(20, 204, 255, 0.976);  
    z-index: 1000;
    display: none;
    width: 80%;  
    max-width: 450px; 
    min-height: 200px;  
}

.popup-content {
    text-align: center;
}

.popup-message {
    margin-bottom: 30px; 
    font-size: 20px;  
    color: #2f8ad0;
    line-height: 1.4;  
}

.popup-close {
    display: inline-block;
    margin-bottom: 15px; 
    padding: 10px 20px;
    background-color: #2f8ad0;
    color: white;
    text-decoration: none;
    border-radius: 6px; 
    transition: background-color 0.3s, transform 0.2s; 
    font-size: 16px;  
}

.popup-close:hover {
    background-color: rgb(122, 189, 213);
    transform: scale(1.05); 
}
     </style>
     </head>
     <body>
          <header><h2 class="logo">🦋ⓜelophile</h2>
               <nav class="navigator">
                    <a href="#home"  class="nav-link active">Home</a>
                    <a href="#about" class="nav-link">About</a>
                    <a href="#contact" class="nav-link">contact</a>
                    <button class="login-button">Login</button>
               </nav>
          </header>
          
          <div class="allcont">
               
          <section class="home_container" id="home">
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
                              <span>M</span><span>e</span><span>l</span><span>o</span><span>p</span><span>h</span><span>i</span><span>l</span><span>e</span><br>
                         </b>
                         <br>
                    </div>
               </div>


               <p class="p1">
               <br><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Welcome to Melophile.com —your ultimate destination for enhancing your musical journey! Whether you're a seasoned musician or just starting,
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
          
          
          <div class="aboutc" id="about">
             <h1 class="hh1">About</h1>
             <p class="p2">
                 The Melophile.com is a web-based application designed to 
                 provide musicians and music enthusiasts with a platform to share, view, and manage helpful music
                 related tips and tricks. Our platform brings together beginners and experts alike, creating
                 a vibrant community where knowledge flows freely and creativity thrives. Whether you're
                 looking to master your instrument, understand music theory, or discover new practice
                 techniques, our portal is your go-to resource for musical growth and development.
             </p>
         </div>
                                   
         <div class="contain1">
             <div class="image-card">
                 <img src="../home/image/c1.jpg" alt="Music Learning">
                 <div class="image-text">
                     <h3>Learn Music Theory</h3>
                     <p>Master the fundamentals of music theory with our comprehensive guides and resources.</p>
                 </div>
             </div>
                                   
             <div class="image-card">
                 <img src="../home/image/c2.jpg" alt="Practice Techniques">
                 <div class="image-text">
                     <h3>Practice Techniques</h3>
                     <p>Discover effective practice methods to improve your musical skills and performance.</p>
                 </div>
             </div>
                                   
             <div class="image-card">
                 <img src="../home/image/c3.jpg" alt="Community Support">
                 <div class="image-text">
                     <h3>Community Support</h3>
                     <p>Join our vibrant community of musicians and share your knowledge and experiences.</p>
                 </div>
             </div>
         </div>

         <div class="contac" id="contact">
        <h1 class="hh1">Contact Us</h1>
        <p class="p2">
            You can give feedback through the feedback section.<br>
            Or otherwise you can contact us on our social media:
        </p>
        <div class="social-icons">
            
            <a href="https://www.instagram.com/apz._.appu/profilecard/?igsh=MTNkam44azcxbG5mbA==" 
               target="_blank" 
               class="social-link instagram"
               aria-label="Follow us on Instagram">
                <ion-icon name="logo-instagram"></ion-icon>
                <span class="icon-label">Instagram</span>
            </a>
            
            
            <a href="https://wa.me/7356072362" 
               target="_blank" 
               class="social-link whatsapp"
               aria-label="Contact us on WhatsApp">
                <ion-icon name="logo-whatsapp"></ion-icon>
                <span class="icon-label">WhatsApp</span>
            </a>
        </div>
    </div><br><br><br>
          </div>
          <div id="popup" class="popup">
              <div class="popup-content">
                  <p class="popup-message" id="popupMessage"></p>
                  <a href="#" class="popup-close" id="popupClose">OK</a>
              </div>
          </div>
     </body>
     <script src="Js/scriptlog.js"></script> 
     <script src="Js/scrollnav.js"></script>
</html>