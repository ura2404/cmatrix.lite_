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
        this.TC = 0;
        this.$Tag = $tag;
        
        this.Target = undefined;    // цель клика на панели сессии
        
        //this.LoginForm = undefined;
        this.onSuccess = undefined;
        this.onError = undefined;
    }
    
    // --- --- --- --- ---
    init(){
        if(this.TC) return;
        
        const Instance = this;
        
        this.$Tag.on('click',function(e){
            e.preventDefault();
            Instance.Target.show();
        });
        
        /*
        if(this.LoginForm){
            this.LoginForm.onSubmit = data => Instance.login(data);
            if(this.onSuccess) this.Form.onSuccess = this.onSuccess;
            if(this.onError)   this.Form.onError   = this.onError;
            this.Form.init();
            
            // click по копке сессии в header
            this.$Tag.on('click',function(e){
                console.log('click on session panel');
                e.preventDefault();
                Instance.LoginForm.show();
            });
        }
        */
        
        this.TC++;
        return this;
    }
    
    // --- --- --- --- ---
    login(url,data){
        new Ajax({
            url : url
        },this.onSuccess,this.onError).commitJson(data);
        
        /*
        const Mode = this.Form.$Tag.hasClass('cm-login') ? 'li' : 'lo';
        
        new Ajax({
            url : this.Form.Url
        },this.onSuccess,this.onError).commitJson(Object.assign({
            m: Mode
        },data));
        */
    }
    
    // --- --- --- --- ---
    logout(){
        
    }
}