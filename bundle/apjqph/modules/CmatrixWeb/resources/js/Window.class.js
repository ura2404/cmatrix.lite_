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
        this.TC = 0;
        this.$Tag = $tag;
        
        this.$Back = this.$Tag.closest('.cm-back');
        
        this.Timeout = 0;
        
        this.onShow = undefined;
        this.onHide = undefined;
    }
    
    // --- --- --- --- ---
    init(opts){
        const Instance = this;
        
        //console.log(this.$Tag.find('.cm-a-close'));
        
        this.$Tag.find('.cm-a-close').on('click',function(e){
            Instance.hide();
        });
        
        this.TC++;
        return this;
    }
    
    // --- --- --- --- ---
    isExists(){
        return this.$Tag.length;
    }
    
    // --- --- --- --- ---
    show(isHidable){
        const Instance = this;
        
        this.isHidable = isHidable;
        
        // если форма не закрывемая, удалить кнопку закрытия
        if(this.isHidable === false) this.$Tag.find('.cm-a-close').remove();
        
        Esc.push(function(){ Instance.hide() });
        
        this.$Back
            .on('click',() => Instance.hide())
            //.removeClass('cm-behind').delay(0).queue(function(){ $(this).addClass('cm-opend'); $(this).dequeue(); });
            .removeClass('cm-behind').addClass('cm-opend');
            
        // если обозначет timeout, то закрыть отреагировать на это
        if(this.Timeout) setTimeout(function(){
            Instance.hide();
        },this.Timeout);
        
        // не передавать click на форме нижестоящим элементам
        this.$Tag.on('click',e => e.stopPropagation());
        
        if(typeof this.onShow === 'function') this.onShow();
    }
    
    // --- --- --- --- ---
    hide(){
        if(this.isHidable === false) return;
        
        const Instance = this;
        
        this.$Back
            .off()
            //.removeClass('cm-opend').delay(0).queue(function(){ $(this).addClass('cm-behind'); $(this).dequeue(); });
            .removeClass('cm-opend').addClass('cm-behind');
            
        Esc.pop();
        
        if(typeof this.onHide === 'function') this.onHide();
    }
}