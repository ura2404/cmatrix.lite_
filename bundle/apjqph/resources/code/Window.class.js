/**
 * Class Window
 * 
 * Необходимые элементы
 *  - .cm-a-close - кнопка закрытия окна
 */

export default class Window {
    
    // --- --- --- --- ---
    /**
     * @param $tag - tag окна
     */
    constructor($tag){
        this.$Tag = $tag;
        this.Opts = {};
    }
    
    // --- --- --- --- ---
    init(opts){
        const Instance = this;
        
        this.Opts = Object.assign({},opts);
        
        this.$Tag.find('.cm-a-close').on('click',function(e){
            Instance.hide();
        });
        return this;
    }
    
    // --- --- --- --- ---
    show(){
        const Instance = this;
        
        if(typeof this.Opts.onShow === 'function') this.Opts.onShow.call(this);
        
        this.$Tag.addClass('cm-opend');
        
        $(document).on('keyup',function(e){
            if(e.keyCode == 27) Instance.hide();
        });
    }
    
    // --- --- --- --- ---
    hide(){
        const Instance = this;
        
        this.$Tag.removeClass('cm-opend');
        $(document).off('keyup');
    }
}