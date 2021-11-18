/**
 * Class Ajax
 */
 
export default class Ajax {
    
    // --- --- --- --- ---
    /**
     * @param $tag - tag кнопки авторизации
     */
    constructor(opts,success,error){
        opts = opts || {};
        
        this.Opts = Object.assign({
            url : 'res/res/post.php',
            method : 'post',
            async : true,
            //dataType : 'json'
        },opts);
        
        this.onSuccess = success;
        this.onError = error;
    }
    
    // --- --- --- --- ---    
    commitJson(data){
        const Instance = this;
        
        $.ajax(Object.assign(this.Opts,{
            dataType : 'json',
            data : data
        }))
        .done(function(data){
            if(!~data.status) Instance.onError(data);
            else Instance.onSuccess(data);
        })
        .fail(function(data, textStatus, jqXHR){
            Instance.onError(data);
        });
        
    }
}