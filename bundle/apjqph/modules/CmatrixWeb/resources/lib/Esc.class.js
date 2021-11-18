/**
 * Class Esc
 */

export default class Esc {
    
    static INSTANCES = [];

    // --- --- --- --- ---
    static event(e){
        if(e.keyCode !== 27) return;
        
        let _callback = Esc.INSTANCES[Esc.INSTANCES.length-1];
        if(typeof _callback === 'function') _callback();
    }
    
    // --- --- --- --- ---
    static push(callback){
        if(!Esc.INSTANCES.length){
            $(document).on('keyup',e => Esc.event(e));
        }
        
        Esc.INSTANCES.push(callback);
    }
    
    // --- --- --- --- ---
    static pop(key){
        if(Esc.INSTANCES.length) Esc.INSTANCES.pop();
        if(!Esc.INSTANCES.length) $(document).off('keyup');
    }
}
