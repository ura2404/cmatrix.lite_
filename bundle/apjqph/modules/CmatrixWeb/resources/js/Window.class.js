/**
 * Class Window
 * 
 * Необходимые элементы
 *  - .cm-a-close - кнопка закрытия окна
 */

import Esc from './Esc.class.js';

export default class Window {
    
    // --- --- --- --- ---
    /**
     * @param $tag - tag окна
     */
    constructor($tag){
        this.$Tag = $tag;
        
        this.Timeout = 0;
    }
    
    // --- --- --- --- ---
    init(opts){
        const Instance = this;
        
        console.log(this.$Tag.find('.cm-a-close'));
        
        this.$Tag.find('.cm-a-close').on('click',function(e){
            Instance.hide();
        });
        return this;
    }
    
    // --- --- --- --- ---
    show(){
        const Instance = this;
        
        Esc.push(function(){ Instance.hide() });
        this.$Tag.parent().addClass('cm-opend');
        
        if(this.Timeout) setTimeout(function(){
            Instance.hide();
        },this.Timeout);
    }
    
    // --- --- --- --- ---
    hide(){
        console.log('hide');
        
        const Instance = this;
        this.$Tag.parent().removeClass('cm-opend');
        Esc.pop();
    }
}