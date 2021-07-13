/**
 * Class Session
 */
 
import Ajax from './Ajax.class.js';

export default class Session {
    
    // --- --- --- --- ---
    /**
     * @param $tag - tag кнопки авторизации
     */
    constructor($tag){
        this.$Tag = $tag;
        
        this.Form = undefined;
        this.onSuccess = undefined;
        this.onError = undefined;
    }
    
    // --- --- --- --- ---
    init(){
        const Instance = this;
        
        if(this.Form){
            this.Form.onSubmit = data => Instance.login(data);
            if(this.onSuccess) this.Form.onSuccess = this.onSuccess;
            if(this.onError) this.Form.onError = this.onError;
            this.Form.init();
            
            // click по копке сессии в header
            this.$Tag.on('click',function(e){
                e.preventDefault();
                Instance.Form.show();
            });
        }
        
        return this;
    }
    
    // --- --- --- --- ---
    login(data){
        new Ajax({
            url : this.Form.Url
        },this.onSuccess,this.onError).commitJson(Object.assign({
            m: 'li' // mode - login
        },data));
    }
    
    // --- --- --- --- ---
    logout(){
        
    }
}