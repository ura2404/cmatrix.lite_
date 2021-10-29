import Session from './js/Session.class.js';
import Window from './js/Window.class.js';
import Form from './js/Form.class.js';
import Menu from './js/Menu.class.js';
import Alert from './js/Alert.class.js';
import Esc from './js/Esc.class.js';

const success = new Alert($('#wi-success'),2000).init();
const error = new Alert($('#wi-error')).init();

const session = new Session($('#wi-session'));
const menuSession = new Menu($('#cm-s-menu'));

// --- --- --- --- ---
$(document).ready(function(){
    let $Form;
    
    session.onSuccess = _success;
    session.onError = _error;
    
    if(($Form = $('#wi-login')).length){
        session.Target = new Form($Form,(url,data) => session.login(url,data));
    }
    
    if(($Form = $('#wi-s-menu')).length){
        const FormLogout = new Form($('#wi-logout'),(url,data) => session.login(url,data)).init();
        const MenuSession = new Menu($Form,{
            '.wi-logout' : () => FormLogout.show()
        });
        MenuSession.onShow = () => $('#wi-s-caret').addClass('wi-opend');
        MenuSession.onHide = () => $('#wi-s-caret').removeClass('wi-opend');
        
        session.Target = MenuSession;
    }
    
    session.Target.init();
    session.init();
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
