import Session from './js/Session.class.js';
import Form from './js/Form.class.js';
import Alert from './js/Alert.class.js';
import Esc from './js/Esc.class.js';

const session = new Session($('#cm-session'));
const success = new Alert($('#cm-success'),2000).init();
const error = new Alert($('#cm-error')).init();
const loginForm = new Form($('#cm-login'));

$(document).ready(function(){
    loginForm.onSuccess = _success;
    loginForm.onError = _error;
    loginForm.init();
    
    //session.LoginForm = new Form($('#cm-login'));
    //session.onSuccess = _success;
    //session.onError = _error;
    //session.init();
});

// --- --- --- --- ---
const _success = function(data){
    console.log('login success',data);
    success.show(data.message);
    setTimeout(() => window.location.reload(),1500);
};

// --- --- --- --- ---
const _error = function(data){
    console.log('login error',data);
    error.show(data.message);
};
