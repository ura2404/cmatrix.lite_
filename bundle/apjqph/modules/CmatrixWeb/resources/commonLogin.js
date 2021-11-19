import Session from './lib/Session.class.js';
import Window from './lib/Window.class.js';
import Form from './lib/Form.class.js';
import Menu from './lib/Menu.class.js';
import Alert from './lib/Alert.class.js';
import Esc from './lib/Esc.class.js';

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
    
    if(($Form = $('#wi-menu-session')).length){
        const formLogout = new Form($('#wi-logout'),(url,data) => session.login(url,data)).init();
        const menuSession = new Menu($Form,{
            '.wi-logout' : () => formLogout.show()
        });
        menuSession.onShow = () => $('#wi-caret-session').addClass('wi-opend');
        menuSession.onHide = () => $('#wi-caret-session').removeClass('wi-opend');
        
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
