const wrapper=document.querySelector('.wrapper');
const loginLink=document.querySelector('.login-link');
const registerLink=document.querySelector('.register-link');
const btnclose=document.querySelector('.close');
const btnpop = document.querySelector('.login-button');

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

btnpop.addEventListener('click', () => {
    wrapper.classList.add('active-login-button');
});

btnclose.addEventListener('click',()=> {
     wrapper.classList.remove('active-login-button');
});

function showPopup(message) {
    document.getElementById('popupMessage').textContent = message;
    document.getElementById('popup').style.display = 'block';
}

document.getElementById('popupClose').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('popup').style.display = 'none';
});

function validateLogin() {
    var email = document.log.email.value;
    var password = document.log.password.value;

    if (email == null || email == "") {
        showPopup("Enter your email id");
        return false;
    }
    if (email.indexOf("@", 0) < 0 || email.indexOf(".", 0) < 0) {
        showPopup("Please enter a valid email address.");
        return false;
    }
    if (password == null || password == "") {
        showPopup("Enter your password");
        return false;
    }
    return true;
}

function validateSignup() {
    var fname = document.sign.fname.value;
    var email = document.sign.email.value;
    var phone = document.sign.phone.value;
    var password = document.sign.password.value;
    const privacyCheckbox = document.querySelector('form[name="sign"] input[name="privacy"]');

    if (fname == null || fname == "") {
        showPopup("Please Enter your Full Name");
        return false;
    }
    if (email == null || email == "") {
        showPopup("Enter your email id");
        return false;
    }
    if (email.indexOf("@", 0) < 0 || email.indexOf(".", 0) < 0) {
        showPopup("Please enter a valid email address.");
        return false;
    }
    if (phone == null || phone == "") {
        showPopup("Enter your phone number");
        return false;
    }
    if (!/^\d{10}$/.test(phone)) {
        showPopup("Please enter a valid 10-digit phone number.");
        return false;
    }
    if (password == null || password == "") {
        showPopup("Enter your password");
        return false;
    }

    if (!privacyCheckbox.checked) {
        showPopup("Please accept the terms and conditions.");
        return false;
    }
    return true;
}

function validateforgot() {
    var email = document.forg.email.value;

    if (email == null || email == "") {
        showPopup("Enter your email id");
        return false;
    }
    if (email.indexOf("@", 0) < 0 || email.indexOf(".", 0) < 0) {
        showPopup("Please enter a valid email address.");
        return false;
    }
    return true;
}
