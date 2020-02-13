var verifPasswordFirst = document.getElementById('my_user_newpassword_first');
var verifPasswordSecond = document.getElementById('my_user_newpassword_second');

const noRequired = () => {
    if (verifPasswordFirst.value.length >= 1){
        verifPasswordSecond.setAttribute('required', 'true');
    } else if (verifPasswordFirst.value.length < 1) {
        verifPasswordSecond.removeAttribute('required');
    }

};