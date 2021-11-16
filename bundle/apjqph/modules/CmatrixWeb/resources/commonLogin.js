import Session from './js/Session.class.js';
import Window from './js/Window.class.js';
import Form from './js/Form.class.js';
import Menu from './js/Menu.class.js';
import Alert from './js/Alert.class.js';
import Esc from './js/Esc.class.js';

const success = new Alert($('#wi-success'),2000).init();
const error = new Alert($('#wi-error')).init();

const session = new Session($('#wi-session'));

// --- --- --- --- ---
$(document).ready(() => {
    let $Form;
    
    session.onSuccess = _success;
    session.onError = _error;
    
    
    if(($Form = $('#wi-login')).length){
        session.Target = new Form($Form,(url,data) => session.login(url,data));
    }
    
    if(($Form = $('#wi-s-menu')).length){
        const formLogout = new Form($('#wi-logout'),(url,data) => session.login(url,data)).init();
        const menuSession = new Menu($Form,{
            '.wi-logout' : () => formLogout.show()
        });
        menuSession.onShow = () => $('#wi-s-caret').addClass('wi-opend');
        menuSession.onHide = () => $('#wi-s-caret').removeClass('wi-opend');
        
        session.Target = menuSession;
    }
    
    session.Target.init();
    session.init();
    
    if($('.wi-need-login').length) session.Target.show(false);
}); 

// --- --- --- --- ---
const _success = function(data){
    //console.log('login success',data);
    //success.show(data.message);
    //setTimeout(() => window.location.reload(),1500);
    window.location.reload();
};

// --- --- --- --- ---
const _error = function(data){
    //console.log('login error',data);
    error.show(data.message);
};
