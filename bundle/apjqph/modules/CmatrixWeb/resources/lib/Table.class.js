/**
 * Class Table
 */

export default class Table {

    // --- --- --- --- ---
    constructor($tag){
        this.$Tag = $tag;
        this.$Scroll = $tag.find('.cm-scroll');
        
        this.$Head = $tag.find('.cm-head');
        this.$Body = $tag.find('.cm-body');
        
        this.$Setup = $tag.find('.cm-setup');
        this.$SetupButton = $tag.find('.cm-setup-button');
        
        this.$Filter = $tag.find('.cm-filter');
        this.$FilterButton = $tag.find('.cm-filter-button');
        
        this.$Tr = this.$Body.find('tr'); // строки
        
        this.Mode = null;
        this.CountSelected = 0;
        this.$CountSelected = $tag.find('.cm-count-selected');
    }
    
    // --- --- --- --- ---
    init(){
        const Instance = this;
        
        this.head();
        
        this.$FilterButton.on('click',e => this.filter(this.Mode));
        this.$SetupButton.on('click',e => this.setup(this.Mode));
        
        // click по строке
        this.$Body.on('click','tr',function(e){ Instance.selectTr(e,this) });
        
        return this;
    }    

    // --- --- --- --- ---
    /**
     * формирование мнимой шапки & scroll
     */
    head(){
        const Instance = this;
        
        this.$Head.find('table').width(this.$Body.find('table').width());
        this.$Tag.find('thead').clone().appendTo(this.$Head.find('table'));

        // --- head
        const $Th = this.$Head.find('th');
        setTimeout(()=> {
            this.$Body.find('thead th').map((index,element) => {
                $($Th[index]).width($(element).width());
            });
        },0);
        
        // --- scroll
        //const Left = this.$Body.find('table').offset().left;
        this.$Scroll.on('scroll',() => {
            this.scrollHead();
            //const NewLeft = this.$Body.find('table').offset().left;
            //this.$Head.find('table').offset({ left : NewLeft});
            
            //if(this.Mode === 'setup') this.$Setup.offset({ left : Left });
            //if(this.Mode === 'filter') this.$Filter.offset({ left : Left });
        });

        /*
        const $Head = this.$Head.find('thead tr:first th');
        
        this.$Body.find('tbody tr:first td').map((index,element) => {
            $($Head[index]).width($(element).width());
        });
        //console.log(index,$(element).width()));
        */
    }

    // --- --- --- --- ---
    scrollHead(){
        const Left = this.$Body.find('table').offset().left;
        this.$Head.find('table').offset({ left : Left});
    }   
    
    // --- --- --- --- ---
    selectTr(e,tr){
        const $Current = $(tr);
        const IsSelected = $Current.hasClass('cm-tr-selected');
        
        if(IsSelected){
            $Current.removeClass('cm-tr-selected');
            this.CountSelected--;
        }
        else{
            if(e.ctrlKey){
                $Current.addClass('cm-tr-selected');
                this.CountSelected++;
            }
            else{
                this.$Tr.removeClass('cm-tr-selected');
                if(this.CountSelected > 1){
                    this.CountSelected = 0;
                }
                else{
                    $Current.addClass('cm-tr-selected');
                    this.CountSelected = 1;
                }
            }
        }
        
        if(this.CountSelected) this.$CountSelected.text(' / '+this.CountSelected);
        else this.$CountSelected.text('');
        
        //console.log(this.CountSelected);
    }
    
    // --- --- --- --- ---
    tbOff(){
        this.$SetupButton.removeClass('cm-active');
        this.$Setup.removeClass('cm-active');
        this.$FilterButton.removeClass('cm-active');
        this.$Filter.removeClass('cm-active');
        this.$Head.removeClass('cm-hidden');
        this.$Scroll.removeClass('cm-x-noscroll');
        this.scrollHead();
        this.Mode = null;
    }

    // --- --- --- --- ---
    setup(mode){
        this.tbOff();
        if(mode === 'setup') return;
        this.Mode = 'setup';
        this.$SetupButton.addClass('cm-active');
        this.$Setup.addClass('cm-active');
        this.$Head.addClass('cm-hidden');
        
        console.log(this.$Scroll.position().left);
        console.log(this.$Body.position().left);
        
        const Left = this.$Scroll.position().left + this.$Body.position().left;
        console.log(Left);
        
        this.$Setup.offset({
            left : Left
        });
        this.$Scroll.addClass('cm-x-noscroll').animate({ scrollTop: 0 },200);
    }
    
    // --- --- --- --- ---
    filter(mode){
        this.tbOff();
        if(mode === 'filter') return;
        this.Mode = 'filter';
        this.$FilterButton.addClass('cm-active');
        this.$Filter.addClass('cm-active');
        this.$Head.addClass('cm-hidden');
        
        const Left = this.$Body.find('table').position().left;
        console.log(Left);
        this.$Filter.position({
            left : Left
        });
        this.$Scroll.addClass('cm-x-noscroll').animate({ scrollTop: 0 },200);
    }
}