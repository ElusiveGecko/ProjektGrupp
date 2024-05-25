const loginModule = document.getElementById('login-module-id')
const openLogin = document.querySelector('.login-module-btn')
const closeLogin = document.querySelector('.login-close-btn')
const registerModule = document.getElementById('register-module-id')
const openRegister = document.querySelector('.register-module-btn')
const closeRegister = document.querySelector('.register-close-btn')

openLogin.addEventListener('click', (e) => {
    loginModule.style.display = 'block'
})

closeLogin.addEventListener('click', (e) => {
    loginModule.style.display = 'none'
})

openRegister.addEventListener('click', (e) => {
    registerModule.style.display = 'block'
})

closeRegister.addEventListener('click', (e) => {
    registerModule.style.display = 'none'
})

window.addEventListener('click', (e) => {
    if (e.target === loginModule || e.target === registerModule) {
        loginModule.style.display = 'none'
        registerModule.style.display = 'none'
    }
})
