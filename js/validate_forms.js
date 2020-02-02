function validate(form) {
    fail = validateEmail(form.email.value)
    if (fail == "") return true
    else { alert(fail); return false }
}
function validateEmail(field) {
    if (field == "") return "Пропущено поле"
    else if ((field.indexOf("@") < 0) || (field.indexOf(".") < 0))
        return "Введите email в верном формате"
    return ""
}