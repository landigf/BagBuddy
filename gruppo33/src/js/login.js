// FUNZIONI CHE USANO AJAX
// Check mail gia presente nel sistema
function checkEmailAvailability() {
    const email = document.getElementById('emailRegister').value;
    const errorMessage = document.getElementById('checkMailDb'); 
    if (!email) return;

    const currentUrl = window.location.pathname; 
    const apiUrl = currentUrl.substring(0, currentUrl.lastIndexOf('gruppo33/') + 'gruppo33/'.length) + 'server/api/login_action.php';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', apiUrl, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if (xhr.responseText === 'exists') {
                errorMessage.style.display = 'inline';
            } else {
                errorMessage.style.display = 'none';
            }
        }
    }
    xhr.send(`checkEmailDb=${encodeURIComponent(email)}`);
}

// Recupero domanda di sicurezza associata all'utente
function getSecurityQuestion() {
    const emailSecurity = document.getElementById("emailSecurityQuestion").value; 
    if (!emailSecurity) {
        alert('Inserisci la tua email per recuperare la domanda di sicurezza.');
        return;
    }
    const currentUrl = window.location.pathname; 
    const apiUrl = currentUrl.substring(0, currentUrl.lastIndexOf('gruppo33/') + 'gruppo33/'.length) + 'server/api/login_action.php';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', apiUrl, true); 
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = xhr.responseText;
            if (response) {
                document.getElementById('shownUserSecurityQuestion').innerText = getSecurityQuestionByValue(response);
                /* $('#answerSecurityQuestion').fadeIn('slow', function() {
                    $(this).removeClass('hidden');
                }); */
                document.getElementById('answerSecurityQuestion').classList.remove("hidden");
            } else {
                alert('Domanda di sicurezza non trovata per questo utente');
            }
        }
    };
    xhr.send(`getSecurityEmail=${encodeURIComponent(emailSecurity)}`);
}

// Verifica della corrispondenza della risposta alla domanda di sicurezza inserita dall'utente nel form di recupero password con
// quella inserita in fase di registrazione 
function showChangePasswordFormWithAnswerCheck() {
    const emailSecurity = document.getElementById('emailSecurityQuestion').value;
    const answerSecurity = document.getElementById('securityAnswer').value;

    if (!emailSecurity || !answerSecurity) {
        alert("Inserisci sia l'email che la risposta alla domanda di sicurezza.");
        return;
    }

    const currentUrl = window.location.pathname; 
    const apiUrl = currentUrl.substring(0, currentUrl.lastIndexOf('gruppo33/') + 'gruppo33/'.length) + 'server/api/login_action.php';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', apiUrl, true); 
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = xhr.responseText;

            if (response === 'success') {
                $('#securityQuestionContainer').fadeOut('slow', function() {
                    $(this).addClass('hidden');
                });
                $('#changePasswordContainer').fadeIn('slow', function() {
                    $(this).removeClass('hidden');
                });
                document.getElementById('emailChangePassword').value = emailSecurity;
            } else if(response === 'errorAnswer') {
                alert("Risposta errata, riprovare.");
            } else {
                alert("Risposta non trovata "); 
            }
        }
    };

    xhr.send(`checkSecurityAnswer=1&emailSecurityQuestion=${encodeURIComponent(emailSecurity)}&securityAnswer=${encodeURIComponent(answerSecurity)}`);
}


// UTILITY
// Mostra domanda di sicurezza associata al value del database 
function getSecurityQuestionByValue(value) {
    switch(value) {
        case "motherMaidenName":
            return "Qual è il nome da nubile di tua madre?";
        case "firstPetName":
            return "Qual è il nome del tuo primo animale domestico?";
        case "favoriteColor":
            return "Qual è il tuo colore preferito?";
        case "firstSchool":
            return "Qual è il nome della tua prima scuola?";
        case "birthCity":
            return "In quale città sei nato?";
        case "bestFriend":
            return "Chi è il tuo migliore amico/a dell'infanzia?";
        case "favoriteSport":
            return "Qual è il tuo sport preferito?";
        default:
            return "Domanda di sicurezza non trovata.";
    }
}

// Event listeners e funzioni utilizzati per gestione della vista e dei form 
document.addEventListener('DOMContentLoaded', function() {
    const passwordRegister = document.getElementById('passwordRegister');
    const repeatPasswordRegister = document.getElementById('repeatPasswordRegister');
    
    if (passwordRegister) {
        passwordRegister.addEventListener('input', inputPasswordCheck);
    }
    if (repeatPasswordRegister) {
        repeatPasswordRegister.addEventListener('input', inputRepeatPasswordCheck);
    }

    const newPassword = document.getElementById('newPasswordOfChangePassword');
    const confirmPassword = document.getElementById('confirmPasswordOfChangePassword');
    
    if (newPassword) {
        newPassword.addEventListener('input', inputPasswordCheck);
    }
    if (confirmPassword) {
        confirmPassword.addEventListener('input', inputRepeatPasswordCheck);
    }
});


document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const registerMode = urlParams.get('register');
    if (registerMode === 'true') {
        showRegisterHideLogin();
    } else {
        showLoginHideRegister();
    }
});

// Porta l'utente all'inizio della pagina alla ricarica della stessa
function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
window.onload = scrollToTop;


// Gestisce l'abilitazione del pulsante registrati: è abilitato solo se tutti i campi del form sono compilati. 
function checkRegisterForm() {
    const form = document.forms["registerForm"];
    const inputs = form.querySelectorAll("input[required]");
    const registerButton = document.getElementById("registerButton");

    let allFilled = true;
    inputs.forEach(input => {
        if (!input.value.trim()) {
            allFilled = false;
        }
    });

    registerButton.disabled = !allFilled;
}

document.addEventListener("DOMContentLoaded", function () {
    const form = document.forms["registerForm"];
    const inputs = form.querySelectorAll("input[required]");

    inputs.forEach(input => {
        input.addEventListener("input", checkRegisterForm);
    });
});


// FUNZIONI PER GESTIONE DELL'APPARIZIONE DINAMICA DEI FORM 
function showLoginHideRegister() {
    $('#loginContainer').fadeIn('slow', function() {
        $(this).removeClass('hidden');
    });
    document.getElementById("registerContainer").classList.add("hidden");
    document.getElementById("securityQuestionContainer").classList.add("hidden");
    document.getElementById("changePasswordContainer").classList.add("hidden");
}

function showRegisterHideLogin() {
    $('#loginContainer').fadeOut('slow', function() {
        $(this).addClass('hidden');
    }); 
    $('#registerContainer').fadeIn('slow', function() {
        $(this).removeClass('hidden');
    });
    document.getElementById("securityQuestionContainer").classList.add("hidden");
    document.getElementById("changePasswordContainer").classList.add("hidden");
}

function showLoginHideRegisterByRegister(){
    $('#registerContainer').fadeOut('slow', function() {
        $(this).addClass('hidden');
    });
    $('#loginContainer').fadeIn('slow', function() {
        $(this).removeClass('hidden');
    });
    document.getElementById("securityQuestionContainer").classList.add("hidden");
    document.getElementById("changePasswordContainer").classList.add("hidden");   
}

function showSecurityQuestion()  {
    $('#loginContainer').fadeOut('slow', function() {
        $(this).addClass('hidden');
    }); 
    $('#securityQuestionContainer').fadeIn('slow', function() {
        $(this).removeClass('hidden');
    }); 
    document.getElementById('answerSecurityQuestion').classList.add('hidden');
    
}


// CHECKER DEI VARI CAMPI DI INPUT
function inputNamesCheck(event) {
    const input = event.target;  
    let errorMessage;
    if(input.id == "nameRegister")
        errorMessage = document.getElementById('checkName');  
    else if(input.id == "surnameRegister")
        errorMessage = document.getElementById('checkSurname');

    const isValid = /^[A-Za-z\s]*$/.test(input.value);  // Controlla se solo lettere (A-Z, a-z) e spazi sono presenti

    if (!isValid) {
        errorMessage.classList.add("visible");
    } else {
        errorMessage.classList.remove("visible");
    }
}

function inputMailCheck(event) {
    const input = event.target; 
    const errorMessage = document.getElementById('checkMail'); 

    const isValid = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(input.value); // Controlla che non ci siano spazi o simboli strani

    if (errorMessage) { 
        if (!isValid && input.value.trim() !== "") {
            errorMessage.classList.add("visible");
        } else {
            errorMessage.classList.remove("visible");
        }
    }
}

function checkPasswordMatch(passwordInput, repeatPasswordInput, errorMessage) {
    if (errorMessage) {
        if (repeatPasswordInput.value !== passwordInput.value && repeatPasswordInput.value.trim() !== "") {
            errorMessage.classList.add("visible");
        } else {
            errorMessage.classList.remove("visible");
        }
    }
}

function inputRepeatPasswordCheck(event) {
    const repeatPasswordInput = event.target;
    let passwordInput, errorMessage;
    
    if(repeatPasswordInput.id == "confirmPasswordOfChangePassword") {
        passwordInput = document.getElementById('newPasswordOfChangePassword');
        errorMessage = document.getElementById('checkRepeatNewPassword');
    } else {
        passwordInput = document.getElementById('passwordRegister');
        errorMessage = document.getElementById('checkRepeatPassword');
    }
    
    checkPasswordMatch(passwordInput, repeatPasswordInput, errorMessage);
}

function inputPasswordCheck(event) {
    const passwordInput = event.target;
    let repeatPasswordInput, errorMessage;
    
    if(passwordInput.id == "newPasswordOfChangePassword") {
        repeatPasswordInput = document.getElementById('confirmPasswordOfChangePassword');
        errorMessage = document.getElementById('checkRepeatNewPassword');
    } else {
        repeatPasswordInput = document.getElementById('passwordRegister');
        errorMessage = document.getElementById('checkRepeatPassword');
    }
    
    checkPasswordMatch(passwordInput, repeatPasswordInput, errorMessage);
}


function inputPasswordFormCheck(event) {
    const password = event.target.value;
    document.querySelectorAll(".passwordCriteria").forEach(ul => {
        const liElements = ul.querySelectorAll('li');

        const minLength = password.length >= 8 && password.length <= 16;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasSymbol = /[%&@!]/.test(password);

        liElements[0].classList.toggle('valid', minLength);
        liElements[0].classList.toggle('invalid', !minLength);

        liElements[1].classList.toggle('valid', hasUpperCase);
        liElements[1].classList.toggle('invalid', !hasUpperCase);

        liElements[2].classList.toggle('valid', hasSymbol);
        liElements[2].classList.toggle('invalid', !hasSymbol);
    })

}



// ADD LISTENERS PER GESTIONE CANCELLAZIONE MESSAGGIO DI ERRORE ALLA MODIFICA DEGLI INPUT

document.getElementById('emailRegister').addEventListener('input', function () {
    document.getElementById('checkMailDb').classList.remove("visible");
    document.getElementById('checkMail').classList.remove("visible");
});

document.getElementById('passwordRegister').addEventListener('input', function () {
    document.getElementById('checkPassword').classList.remove("visible");
});


document.getElementById('repeatPasswordRegister').addEventListener('input', function () {
    document.getElementById('checkRepeatPassword').classList.remove("visible");
});

document.getElementById('newPasswordOfChangePassword').addEventListener('input', function () {
    document.getElementById('checkNewPassword').classList.remove("visible");
});

document.getElementById('confirmPasswordOfChangePassword').addEventListener('input', function () {
    document.getElementById('checkRepeatNewPassword').classList.remove("visible");
});

