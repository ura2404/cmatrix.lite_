/**
 * Class Session
 */

//import Form from './Form.class.js';

export default class Session {
    
    // --- --- --- --- ---
    /**
     * @param $tag - tag кнопки авторизации
     */
    constructor($tag){
        this.$Tag = $tag;
        this.Form = undefined;
        /*this.Form = new Form($($tag.attr('data-login'))).init();*/
    }
    
    // --- --- --- --- ---
    init(opts){
        const Instance = this;
        opts = opts || {};
        
        this.Form = opts.form || undefined;
        
        // click по копке сессии в header
        if(this.Form) this.$Tag.on('click',function(e){
            console.log(Instance.Form);
            e.preventDefault();
            Instance.Form.show();
        });
        
        return this;
    }
}