const icon_full_screen = document.getElementById('icon-full-screen')
const elem = document.documentElement;

icon_full_screen.onclick = fullScreen

function fullScreen() {
    if (window.innerHeight == screen.height) {
        closeFullscreen()
    } else {
        openFullscreen()
    }

}

function openFullscreen() {
    if (elem.requestFullscreen) {
        elem.requestFullscreen();
    } else if (elem.webkitRequestFullscreen) {
        /* Safari */
        elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) {
        /* IE11 */
        elem.msRequestFullscreen();
    }
}

function closeFullscreen() {
    if (document.exitFullscreen) {
        document.exitFullscreen();
    } else if (document.webkitExitFullscreen) {
        /* Safari */
        document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) {
        /* IE11 */
        document.msExitFullscreen();
    }
}
/* 
const form_search_short = document.getElementById('form-search-short')
const form_search_short_input = document.getElementById('form-search-short-input')
const ul_headers_icons = document.getElementById('ul-headers-icons')
const btn_search_header = document.getElementById('btn-search-header')
const div_logo = document.getElementById('div-logo')
const div_profil = document.getElementById('div-profil')

btn_search_header.onclick = () => {
    ul_headers_icons.className = "ms-auto d-none m-0 list-unstyled me-5 ul-headers-icons"
    div_logo.style.display = "none"
    div_profil.className = "d-none me-4 ms-4 div-profil"
    form_search_short.className = "d-flex w-100 px-5 m-0"
    form_search_short_input.focus()
}
form_search_short_input.onblur = () => {
    ul_headers_icons.className = "ms-auto d-flex m-0 list-unstyled me-5 ul-headers-icons"
    form_search_short.className = "d-none"
    div_logo.style.display = "flex"
    div_profil.className = "d-flex me-4 ms-4 div-profil"
}  */