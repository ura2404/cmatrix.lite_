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
    }
    
    // --- --- --- --- ---
    init(opts){
        const Instance = this;
        
        this.$Tag.find('.cm-a-close').on('click',function(e){
            Instance.hide();
        });
        return this;
    }
    
    // --- --- --- --- ---
    show(){
        const Instance = this;
        
        Keyup.push(27,function(){Instance.hide()});
        
        //if(typeof this.Opts.onShow === 'function') this.Opts.onShow.call(this);
        
        this.$Tag.parent().addClass('cm-opend');
        
        /*
        $(document).on('keyup',function(e){
            if(e.keyCode == 27){
                const Inst = Window.INSTANCES.pop();
                Inst.hide();
            }
        });*/
    }
    
    // --- --- --- --- ---
    hide(){
        const Instance = this;
        this.$Tag.parent().removeClass('cm-opend');
        //$(document).off('keyup');
    }
}