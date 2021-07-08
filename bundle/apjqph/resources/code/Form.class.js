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
     * @param $tag - tag формы
     */
    constructor($tag){
        super($tag);
    }
    
    // --- --- --- --- ---
    init(){
        const Instance = this;
        super.init();
        
        this.$Tag.find('.cm-a-submit').on('click',function(e){
            console.log('click');
            Instance.submit();
        });
        return this;
    }
    
    // --- --- --- --- ---
    show(){
        const Instance = this;
        super.show();
        
        this.$Tag.find('input:first').focus();
        
        $(document).on('keyup',function(e){
            console.log('13');
            if(e.keyCode == 13) Instance.submit();
        });

    }    

    // --- --- --- --- ---
    submit(){
        alert('submit');
    }
}