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
    }
    
    // --- --- --- --- ---
    init(){
        const Instance = this;
        super.init();
        
        this.$Tag.on('submit',function(e){
            e.preventDefault();
            Instance.submit();
        }).find('.cm-a-submit').on('click',function(e){
            Instance.$Form.submit();
        });
        
        return this;
    }
    
    // --- --- --- --- ---
    show(){
        const Instance = this;
        super.show();
        
        this.$Tag.find('input:first').focus();
        
        $(document).on('keyup',function(e){
            if(e.keyCode == 13 && $(e.target).is('input')){
                Instance.$Tag.submit();
            }
        });
        
        return this;
    }    

    // --- --- --- --- ---
    submit(){
        console.log('submit');
        
        const Url = this.$Tag.attr('action');
    }
}