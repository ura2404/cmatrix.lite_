/**
 * Class Alert
 */
import Window from './Window.class.js';

export default class Alert extends Window {
    
    // --- --- --- --- ---
    /**
     * @param $tag - tag контейфнера формы, tag фона на весь экран
     */
    constructor($tag,timeout){
        super($tag);
        //this.$Tag = $tag;
        
        this.Timeout = timeout;
    }
    
    // --- --- --- --- ---
    init(message){
        super.init();
        return this;
    }
    
    // --- --- --- --- ---
    show(message){
        this.$Tag.find('.cm-text').html(message);
        super.show();
    }
    
    // --- --- --- --- ---
    hide(){
        this.$Tag.find('.cm-text').text('');
        super.hide();
    }
    
}