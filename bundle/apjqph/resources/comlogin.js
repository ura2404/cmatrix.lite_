import Session from './js/Session.class.js';
import Form from './js/Form.class.js';
import Alert from './js/Alert.class.js';
import Esc from './js/Esc.class.js';

const session = new Session($('#cm-session'));
const success = new Alert($('#cm-success'));
const error = new Alert($('#cm-error'));

//new Keyup();

$(document).ready(function(){
    session.Form = new Form($('#cm-login'));
    session.onSuccess = _success;
    session.onError = _error;
    session.init();
});

// --- --- --- --- ---
const _success = function(data){
    console.log('login success',data);
    success.show(data.message);
};

// --- --- --- --- ---
const _error = function(data){
    console.log('login error',data);
    error.show(data.message);
};
