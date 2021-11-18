import Alert from './lib/Alert.class.js';

const error = new Alert($('#wi-error')).init();

$(document).ready(function(){
    $('.cm-no-click').on('click', e => e.preventDefault());
    
    if($('.cm-error').length) error.show($('.cm-error').html());
});