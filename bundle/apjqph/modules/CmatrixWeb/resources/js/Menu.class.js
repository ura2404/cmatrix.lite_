/**
 * Class Menu
 */

import Window from './Window.class.js';

export default class Menu extends Window {
    
    // --- --- --- --- ---
    /**
     * @param $tag - tag контейфнера формы, tag фона на весь экран
     */
    constructor($tag,onClicks){
        super($tag);
        
        this.onClicks = onClicks;
    }
    
    // --- --- --- --- ---
    init(){
        const Instance = this;
        super.init();
        
        /*this.$Tag.find('li').on('click', e => {
            console.log('click',e);
            e.stopPropagation();
        });*/
        
        Object.keys(this.onClicks).forEach(function(key){
            Instance.$Tag.find(key).on('click',e => this[key].call(e.target));
        },this.onClicks);
        
        return this;
    }
    
}