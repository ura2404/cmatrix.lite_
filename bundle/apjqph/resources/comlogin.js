import Session from './js/Session.class.js';
import Form from './js/Form.class.js';

const session = new Session($('#cm-session'));

$(document).ready(function(){
    session.init({
        form : new Form($('#cm-login'))
    });
});
