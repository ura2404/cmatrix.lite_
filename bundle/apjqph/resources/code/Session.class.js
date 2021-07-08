/**
 * Class Session
 */

import Form from './Form.class.js';

export default class Session {
    
    // --- --- --- --- ---
    /**
     * @param $tag - tag кнопки авторизации
     */
    constructor($tag){
        this.$Tag = $tag;
        this.Form = new Form($($tag.attr('data-login-form'))).init({
            /*onShow : function(){
                this.$Tag.find('input:first').focus();
            }*/
        });
        console.log(this.Form);
    }
    
    // --- --- --- --- ---
    init(){
        const Instance = this;
        
        // click по копке сессии в header
        this.$Tag.on('click',function(e){
            e.preventDefault();
            Instance.Form.show();
        });
        
        return this;
    }
}