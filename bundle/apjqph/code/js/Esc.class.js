/**
 * Class Esc
 */

export default class Esc {
    
    static INSTANCES = undefined;
    
    // --- --- --- --- ---
    /**
     * @param $tag - tag окна
     */
    constructor(key){
        
        /*
        Keyup.INSTANCES[key] = Keyup.INSTANCES[key] || [];
        
        $(document).on('keyup',function(e){
            if(e.keyCode == key){
                const Inst = Window.INSTANCES.pop();
                Inst.hide();
            }
        });
        */
    }
    
    // --- --- --- --- ---
    static event(e){
        console.log(Esc.INSTANCES[e.keyCode]);
        let _callback = 
        
        
        
        console.log('event');
    }
    
    // --- --- --- --- ---
    static push(key,callback){
        if(!Esc.INSTANCES){
            Esc.INSTANCES = {};
            $(document).on('keyup',function(e){
                Esc.event(e);
            });
        }
        
        if(!Esc.INSTANCES[key]) Esc.INSTANCES[key] = [];
        Esc.INSTANCES[key].push(callback);
    }
    
    // --- --- --- --- ---
    static pop(key){
        let _callback = Esc.INSTANCES[key].pop();
        if(!Esc.INSTANCES) $(document).off('keyup');
        return _callback;
    }
}
