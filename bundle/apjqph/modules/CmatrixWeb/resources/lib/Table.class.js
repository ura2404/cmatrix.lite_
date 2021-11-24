/**
 * Class Table
 */

export default class Table {

    // --- --- --- --- ---
    constructor($tag){
        this.$Tag = $tag;
        this.$Scroll = $tag.find('.cm-scroll');
        this.$Search = $tag.find('.cm-search');
        
        this.$Head = $tag.find('.cm-head');
        this.$Body = $tag.find('.cm-body');
        
        this.$Setup = $tag.find('.cm-setup');
        this.$SetupButton = $tag.find('.cm-setup-button');
        
        this.$Filter = $tag.find('.cm-filter');
        this.$FilterButton = $tag.find('.cm-filter-button');
        
        this.$Th = this.$Tag.find('thead th'); // поля шапки
        this.$Tr = this.$Body.find('tbody tr'); // строки
        
        this.Mode = null;
        
        this.Count = this.$Tr.length;   // --- кол-во строк
        this.CountSelected = 0;         // --- кол-во выделенных строк
        this.$CountSelected = $tag.find('.cm-table-footer').find('.cm-count-selected'); // контенер для отображения кол-ва выделенных строк
    }
    
    // --- --- --- --- ---
    init(){
        const Instance = this;
        
        this.$FilterButton.on('click',e => this.openFilter(this.Mode));
        this.$SetupButton.on('click',e => this.openSetup(this.Mode));
        
        // click по строке
        this.$Body.on('click','tr',function(e){ Instance.selectTr(e,this) });
        
        //
        this.$Search
            .on('mouseover',() => this.$Search.addClass('cm-hover'))
            .on('mouseleave',() => this.$Search.removeClass('cm-hover'))
            .find('input').on('keydown',function(e){
                if(e.keyCode !== 13 && e.keyCode !== undefined) return;
                
                const Page = window.location.href.split('?')[0];
                const Params = location.search.substring(1) ?
                    JSON.parse('{"' + decodeURI(location.search.substring(1).replace(/&/g, "\",\"").replace(/=/g, "\":\"")) + '"}') : {};
                const Value = $(this).val();
                
                if(Value) Params.r = Value;
                else delete Params.r;
                
                //console.log(Params);
                //console.log(Page);
                //console.log($.param(Params));
                //console.log(new URLSearchParams(Params).toString());
                
                window.location.href = Page + (Object.keys(Params).length ? '?'+ $.param(Params) : '');
            })
            .focus().map(function(){                                                            // фокус на поле ввода и курсор в конец
                $(this)[0].setSelectionRange($(this).val().length,$(this).val().length);
            })
            .end()
            .next().on('click',function(){ $(this).prev().val('').focus() })                    // кнопка очистки поля ввода
            .next().on('click',function(){ $(this).prev().prev().trigger('keydown').focus() }); // иконка поиска
        
        // кнопка full select
        this.$Tag.find('.cm-full-select').on('click',() => this.selectTrAll());
        
        /*setTimeout(()=> {
            console.log(localStorage.getItem('scrolLeft'));
            this.$Body.find('table').offset({
            left : localStorage.getItem('scrolLeft')
            });
        },10);*/
        
        this.$Scroll.animate({
            scrollRight : localStorage.getItem('scrolLeft')
        },100);
        
        //this.$Scroll.addClass('cm-x-noscroll').animate({ scrollTop: 0 },200);
        
        this.genHead();
        
        return this;
    }    

    // --- --- --- --- ---
    /**
     * формирование мнимой шапки & scroll
     */
    genHead(){
        const Instance = this;
        
        this.$Head.find('table').width(this.$Body.find('table').width());
        this.$Tag.find('thead').clone(true).appendTo(this.$Head.find('table'));
        setTimeout(()=> {
            this.$Head.find('table').css('opacity',1);
            this.$Body.find('table').css('opacity',1);
        },100);

        // --- head
        const $Th = this.$Head.find('th');
        setTimeout(()=> {
            this.$Body.find('thead th').map((index,element) => {
                $($Th[index]).width($(element).width());
            });
        },10);
        
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
        localStorage.setItem('scrolLeft',Left);
        this.$Head.find('table').offset({ left : Left});
    }   
    
    // --- --- --- --- ---
    /**
     * пометка строки
     */
    selectTr(e,tr){
        const $Current = $(tr);
        const IsSelected = $Current.hasClass('cm-tr-selected');
        
        if(e.ctrlKey){
            if(IsSelected){
                $Current.removeClass('cm-tr-selected');
                this.CountSelected--;
            }
            else{
                $Current.addClass('cm-tr-selected');
                this.CountSelected++;
            }
        }
        else{
            this.selectTrAll(false);
            $Current.addClass('cm-tr-selected');
            this.CountSelected = 1;
        }
        
        this.showCountSelected();
    }
    
    // --- --- --- --- ---
    /**
     * Выделени всех строк
     * 
     * @param bool select
     *      -true выделить все
     *      -false освободить все
     */
    selectTrAll(select){
        if(select === false || (select === undefined && this.CountSelected == this.Count)){
            this.$Tr.removeClass('cm-tr-selected');
            this.CountSelected = 0;
        }
        else if(select === true || (select === undefined && this.CountSelected != this.Count)){
            this.$Tr.addClass('cm-tr-selected');
            this.CountSelected = this.Count;
        }
        this.showCountSelected();
    }
    
    // --- --- --- --- ---
    /**
     * Отрисовка кол-ва выделенных строк
     */
    showCountSelected(){
        if(this.CountSelected) this.$CountSelected.text(this.CountSelected).prev().text('/');
        else this.$CountSelected.text('').prev().text('');
    }
    
    /**
     * Отрисовать иконку сортировку в шапке
     */
    // --- --- --- --- ---
    showSortIcon(th){
        const $Current = $(th);
        /*
        if($Current.hasClass('cm-sort')) $Current.find('.cm-sort-container i.cm-sort').removeClass('cm-hidden');
        if($Current.hasClass('cm-asc')) $Current.find('.cm-sort-container i.cm-asc').removeClass('cm-hidden');
        if($Current.hasClass('cm-desc')) $Current.find('.cm-sort-container i.cm-desc').removeClass('cm-hidden');
        */
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
    openSetup(mode){
        this.tbOff();
        if(mode === 'setup') return;
        
        this.Mode = 'setup';
        this.$SetupButton.addClass('cm-active');
        this.$Setup.addClass('cm-active');
        
        // скрыть горизонтальный scroll
        this.$Head.addClass('cm-hidden');
        
        // смесить окно по горизонтали
        this.$Setup.offset({ left : this.$Scroll.position().left });
        
        // скролировать вверх
        this.$Scroll.addClass('cm-x-noscroll').animate({ scrollTop: 0 },200);
    }
    
    // --- --- --- --- ---
    openFilter(mode){
        this.tbOff();
        if(mode === 'filter') return;
        
        this.Mode = 'filter';
        this.$FilterButton.addClass('cm-active');
        this.$Filter.addClass('cm-active');
        
        // скрыть горизонтальный scroll
        this.$Head.addClass('cm-hidden');
        
        // смесить окно по горизонтали
        this.$Filter.offset({ left : this.$Scroll.position().left });
        
        // скролировать вверх
        this.$Scroll.addClass('cm-x-noscroll').animate({ scrollTop: 0 },200);
    }
}