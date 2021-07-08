import Session from './code/Session.class.js';

const session = new Session($('#cm-session'));

$(document).ready(function(){
    session.init();
});