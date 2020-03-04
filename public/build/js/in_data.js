
$(document).ready(function(){
    $.getJSON("query_tjlb_all",function(data){
        if(data == null){return;}
        var str_html  = '';    
        $.each(data,function(index,item){
            if(index == 0){
                str_html  += "<option data-subtext='"+item.bm+"' value='"+item.bm+"'selected>"+item.mc+"</option>"  
            }
            else{
                str_html  += "<option data-subtext='"+item.bm+"' value='"+item.bm+"'>"+item.mc+"</option>"  
            }                     
        });           
        $("#tj_type").empty();        
        $('#tj_type').html(str_html);
        $('#tj_type').selectpicker('refresh');
      });
      
    $.getJSON("query_dwxx_all",function(data){
        if(data == null){return;}
        var str_option_html  = "<option data-subtext='000001,GRTJ' selected>个人体检</option>";    
        $.each(data,function(index,item){
            str_option_html  += "<option data-subtext='"+item.dwbm +","+item.pym+"' value='"+item.dwbm+"'>"+item.dwmc+"</option>"            
        });           
        $("#patient_employer").empty();        
        $('#patient_employer').html(str_option_html);
        $('#patient_employer').selectpicker('refresh');
      });

    /*测试体检单位选择在控制台输出
    $('#patient_employer').change(function(){
        console.log($(this).find("option:selected").data("subtext"));
    });
    */
   /*测试体检类型选择在控制台输出
    $('#tj_type').change(function(){
        var seld =[];
        $('#tj_type option:selected').each(function(){
            seld.push($(this).data("subtext"));
        })
        console.log(seld);       
    })
    */    
})