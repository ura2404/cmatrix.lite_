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
        
        // поле поиска
        this.$Search
            .on('mouseover',() => this.$Search.addClass('cm-hover'))
            .on('mouseleave',() => this.$Search.removeClass('cm-hover'))
            .find('input').on('keydown',function(e){
                if(e.keyCode !== 13 && e.keyCode !== undefined) return;
                
                const Value = $(this).val();
                Instance.reload('r', Value ? Value : null);
            })
            .focus().map(function(){                                                            // фокус на поле ввода и курсор в конец
                $(this)[0].setSelectionRange($(this).val().length,$(this).val().length);
            })
            .end()
            .next().on('click',function(){ $(this).prev().val('').focus() })                    // кнопка очистки поля ввода
            .next().on('click',function(){ $(this).prev().prev().trigger('keydown').focus() }); // иконка поиска
        
        // кнопка full select
        this.$Tag.find('.cm-full-select').on('click',() => this.selectTrAll());
        
        // --- фильтр 
        this.$Filter
        .find('.cm-filter-toolbar')
            // --- кнопка сбросить фильтры
            .find('.cm-filter-reset').on('click',() => {
                this.$Filter.find('.cm-filer-prop-container').map((index,element) => {
                    $(element).find('.cm-filter-clear-button').trigger('click');
                });
                Instance.reload('f',null);
            })
            .end().
            // --- кнопка подтвердить фильтры
            find('.cm-filter-commit').on('click',() => {
                const Value = this.getFilter();
                Instance.reload('f', Value ? Value : null);
            })
            .end()
        .end()
        .find('.cm-filer-prop-container').map((index,element) => {
            const $Choices = $(element).find('.cm-filer-prop-choices');
            const $Button = $(element).find('button.cm-filter-choices-button');
            const $Empty = $(element).find('div.cm-filter-choices-button');
            const Count = $Choices.children().length;
            
            // --- показать кнопку, если есть более одного варинта
            Count>1 ? $Button.removeClass('cm-hidden') : $Empty.removeClass('cm-hidden');
            
            $(element)
            // --- клик по кнопке смены выбора
            .find('.cm-filter-choices-button').on('click',function(){
                const $Active = $Choices.children('.cm-active');
                const $Next = $Active.next().length ? $Active.next() : $Choices.children().eq(0);
                
                $Active.removeClass('cm-active');
                $Next.addClass('cm-active');
            })
            .end()
            
            // --- клик по кнопке очистки
            .find('.cm-filter-clear-button').on('click',function(){
                $(this).prev()
                    .find('.cm-filter-choice-b').removeClass('cm-active')
                    .end()
                    .find('.cm-filter-choice')
                        .find('input').val('')
                        .end()
                    .removeClass('cm-active')
                    .eq(0).addClass('cm-active');
            })
            .end()
            
            // клик по bool-выбору
            .find('.cm-filter-choice-b').on('click',function(){
                const $Current = $(this);
                
                if($Current.hasClass('cm-active')) $Current.removeClass('cm-active').find('i').removeClass('fas');
                else {
                    $Choices.children().removeClass('cm-active').find('i').removeClass('fas');
                    $Current.addClass('cm-active').find('i').addClass('fas');
                }
                
            });
        });

        
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
        
        if(this.$Body.find('tr').length) this.genHead();
        
        return this;
    }    
    
    reload(key,value){
        console.log('key=',key,'value=',value);
        
        const Page = window.location.href.split('?')[0];
        /*
        console.log(11,location.search.substring(1));
        console.log(22,location.search.substring(1).replace(/&/g, "\",\""));
        console.log(33,location.search.substring(1).replace(/&/g, "\",\"").replace(/=/g, "\":\""));
        console.log(44,decodeURI(location.search.substring(1).replace(/=+$/, '').replace(/&/g, "\",\"").replace(/=/g, "\":\"")));
        console.log(55,decodeURIComponent(location.search.substring(1).replace(/=+$/, '').replace(/&/g, "\",\"").replace(/=/g, "\":\"")));
        */
        //const Params = location.search.substring(1) ? JSON.parse('{"' + decodeURIComponent(location.search.substring(1).replace(/=+$/, '').replace(/&/g, "\",\"").replace(/=/g, "\":\"")) + '"}') : {};
        const Params = location.search.substring(1) ? JSON.parse('{"' + decodeURIComponent(location.search.substring(1).replace(/=+$/,"").replace(/&/g, "\",\"").replace(/=/g, "\":\"")) + '"}') : {};
        
        if(value) Params[key] = value;
        else delete Params[key];
        
        const Href = Page + (Object.keys(Params).length ? '?'+ decodeURIComponent($.param(Params)) : '');
        console.log('Href=',Href);
        window.location.href = Href;
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
    
    // --- --- --- --- ---
    getFilter(){
        let Filter = {};
        this.$Filter.find('.cm-filer-prop-container').map((index,element) => {
            const $Current = $(element);
            const Code = $Current.attr('code');
            
            $Current.find('.cm-filer-prop-choices').map((index,element) => {
                $(element).children('.cm-active').map((index,element) => {
                    const $Current = $(element);
                    const Type = $Current.attr('type'); 
                    
                    // --- bool
                    if($Current.hasClass('cm-filter-choice-b')){
                        Filter[Code] = { '=' : $Current.attr('value') };
                    }
                    // ---map
                    if($Current.hasClass('cm-filter-choice-m')){
                    }
                    else{
                        if(Type === 'dp'){
                            const Value1 = $Current.find('input').eq(0).val();
                            const Value2 = $Current.find('input').eq(1).val();
                            if(Value1 || Value2) Filter[Code] = { 'dp' : [ Value1 ? Value1 : null, Value2 ? Value2 : null ] };
                        }
                        else{
                            const Value = $Current.find('input').val();
                            if(Value){
                                Filter[Code] = {};
                                Filter[Code][Type] = Value;
                            }
                        }
                    }
                });
            });
        });
        
        console.log('123==============','123=='.replace(/=+$/, ''));
        
        /*
        F = btoa(JSON.stringify(Filter));
        console.log(111111111111,F);
        F = F.replace(/=+$/,"");
        console.log(222222222222,F);
        */
        
        //console.log(Filter,Object.keys(Filter).length ? JSON.stringify(Filter) : null);
        
        console.log(Filter);
        Filter = Object.keys(Filter).length ? btoa(JSON.stringify(Filter)).replace(/=+$/,"") : null;
        console.log(Filter);
        return Filter;
    }
}