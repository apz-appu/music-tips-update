const wrapper=document.querySelector('.wrapper');
const loginLink=document.querySelector('.login-link');
const registerLink=document.querySelector('.register-link');
const btnpop=document.querySelector('.popup');
const btnclose=document.querySelector('.close');

const privacyCheckbox = document.querySelector('form[name="sign"] input[name="privacy"]');

 // Get the sidebar and menu toggle element
 const sidebar = document.querySelector('.slidebar');
 const menuToggle = document.querySelector('.ti-menu-alt');
 
//privacy check

registerLink.addEventListener('click',()=> {
     wrapper.classList.add('active');
});

loginLink.addEventListener('click',()=> {
     wrapper.classList.remove('active');
});

btnpop.addEventListener('click',()=> {
     wrapper.classList.add('active-popup');
});

btnclose.addEventListener('click',()=> {
     wrapper.classList.remove('active-popup');
});

function validateLogin() {// validation for log in page
     var email = document.log.email.value;
     var password = document.log.password.value;

     if (email == null || email == "") {//email
         alert("Enter your email id");
         return false;
     }
     if (email.indexOf("@", 0) < 0 || email.indexOf(".", 0) < 0) {//email @ and .
         alert("Please enter a valid email address.");
         return false;
     }
     if (password == null || password == "") {//passsword
         alert("Enter your password");
         return false;
     }
     return true;
 }

 function validateSignup() {// validation for sign up page
     var fname = document.sign.fname.value;
     var email = document.sign.email.value;
     var phone = document.sign.phone.value;
     var password = document.sign.password.value;
     const privacyCheckbox = document.querySelector('form[name="sign"] input[name="privacy"]'); // Ensure selection

     if (fname == null || fname == "") {//name
         alert("Please Enter your Full Name");
         return false;
     }
     if (email == null || email == "") {//email
         alert("Enter your email id");
         return false;
     }
     if (email.indexOf("@", 0) < 0 || email.indexOf(".", 0) < 0) {//email @ and .
         alert("Please enter a valid email address.");
         return false;
     }
     if (phone == null || phone == "") {//phone no
         alert("Enter your phone number");
         return false;
     }
     if (!/^\d{10}$/.test(phone)) {//phone no digits
         alert("Please enter a valid 10-digit phone number.");
         return false;
     }
     if (password == null || password == "") {//password
         alert("Enter your password");
         return false;
     }

     if (!privacyCheckbox.checked) {// privacy check
        alert("Please accept the terms and conditions.");
        return false;
    }
     return true;
 }

// Add click event listener to the menu toggle
menuToggle.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
});

function validateforgot() {// validation for log in page
    var email = document.forg.email.value;

    if (email == null || email == "") {//email
        alert("Enter your email id");
        return false;
    }
    if (email.indexOf("@", 0) < 0 || email.indexOf(".", 0) < 0) {//email @ and .
        alert("Please enter a valid email address.");
        return false;
    }
    return true;
}