function dclickLogin() {
    if(window.location.href.indexOf('action=login') < 0){
        window.location.href = window.location.href + '?action=login';
    }
}
