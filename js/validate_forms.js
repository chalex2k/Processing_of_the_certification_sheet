function validate(form) {
    fail = validateEmail(form.email.value);
    fail += validatePassword(form.password.value);
    if (fail == "") return true;
    else { alert(fail); return false }
}
function validateReg(form) {
    fail = validateEmail(form.email.value);
    fail += validateRole(form.role.value);
    if (fail == "") return true
    else { alert(fail); return false }
}
function validateEmail(field) {
    if (field == "") return "Введите логин"
    else if ((field.indexOf("@") < 0) || (field.indexOf(".") < 0))
        return "Введите email в верном формате"
    return ""
}

function validatePassword(field) {
    if (field == "") return "Введите пароль";
}

function validateRole(selector) {
    if (selector == "") return "Вы кто (who)";
}