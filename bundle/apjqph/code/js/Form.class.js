/**
 * Class Window
 * 
 * Необходимые элементы
 *  - .cm-a-submit - кнопка подтверждения формы
 */

import Window from './Window.class.js';

export default class Form extends Window {
    
    // --- --- --- --- ---
    /**
     * @param $tag - tag контейфнера формы, tag фона на весь экран
     */
    constructor($tag){
        super($tag);
        
        this.Url       = undefined;
        this.onSuccess = undefined;
        this.onError   = undefined;
        this.onSubmit  = undefined;
    }
    
    // --- --- --- --- ---
    init(){
        const Instance = this;
        super.init();
        
        const Url = this.$Tag.attr('action');
        const SubmitButton = this.$Tag.find('.cm-a-submit');
        
        if(Url && SubmitButton){
            this.Url = Url;
            
            this.$Tag.on('submit',function(e){
                e.preventDefault();
                Instance.submit();
            });
            
            SubmitButton.on('click',function(e){
                Instance.$Tag.submit();
            });
        }
        
        return this;
    }
    
    // --- --- --- --- ---
    show(){
        const Instance = this;
        super.show();
        
        this.$Tag.find(':input').filter((index, element) =>$(element).is('input')).map((index, element) => $(element).val(''));
        
        this.$Tag.find('input:first').focus();
        
        $(document).on('keyup',function(e){
            if(e.keyCode == 13 && $(e.target).is('input')){
                Instance.$Tag.submit();
            }
        });
        
        return this;
    }    
    
    // --- --- --- --- ---
    required(){
        return this.$Tag.find(':input').map(function(index, element){
            if(!$(element).hasAttr('required')) return;
            
            $(element).removeClass('cm-invalid').next().text('');
            if(! element.validity.valid) $(element).addClass('cm-invalid').next().text(element.validationMessage);
            
            return element.validity.valid;
        }).get().every((current,index,array) => !!current);
    }

    // --- --- --- --- ---
    submit(){
        console.log('submit');
        
        if(typeof this.onSubmit === 'function' && this.required()){
            let Data = {};
            this.$Tag.find(':input').filter((index, element) =>$(element).is('input')).map((index, element) => {
                const Name = $(element).attr('name');
                Data[Name] = Name === 'p' ? $.md5($(element).val()) : $(element).val();
            });
            
            this.onSubmit(Data);
        }
        return this;
    }
}