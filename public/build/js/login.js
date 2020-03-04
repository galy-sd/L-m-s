
$(document).ready(function(){
      $('#ipwd').on('input propertychange', function() { 
        var pwd = $.trim($(this).val());
        var rpwd = $.trim($("#i2pwd").val());
        if(rpwd!=""){
        if(pwd==""&&rpwd==""){                 
        //若都为空，则提示密码不能为空，为了用户体验（在界面上用required同时做了处理）
            $("#msg_pwd").html("密码不能为空");
        }
        else{
        if(checkstr(pwd)){$("#msg_pwd").html("密码包含非法字符");$("#change_button").attr("disabled",false);return;};
        if(pwd.length<6){$("#msg_pwd").html("密码不能小于六位字符");$("#change_button").attr("disabled",false);return;}
        if(pwd==rpwd){                                 //相同则提示密码匹配
              $("#msg_pwd").html("两次密码匹配通过");
              //$("#change_button").attr("disabled",false); //使按钮无法点击
            }else{                                          //不相同则提示密码匹配
              $("#msg_pwd").html("两次密码不匹配");
              //$("#change_button").attr("disabled",true);
            }   
        }}
      })

  $('#i2pwd').on('input propertychange', function() {
            var pwd = $.trim($(this).val());
            var rpwd = $.trim($("#ipwd").val());
            if(pwd==""&&rpwd==""){
                $("#msg_pwd").html("密码不能为空");
            }
            else{
            if(checkstr(rpwd)){$("#msg_pwd").html("密码包含非法字符");$("#change_button").attr("disabled",false);return;};
            if(rpwd.length<6){$("#msg_pwd").html("密码不能小于六位字符");$("#change_button").attr("disabled",false);return;}
                if(pwd==rpwd){
                $("#msg_pwd").html("两次密码匹配通过");
                }else{
                $("#msg_pwd").html("两次密码不匹配");
             }
            }
        })
  $("#login_button").click(function(e){  
    e.preventDefault();      
    var login_ysbh = $.trim($("#login_ysbh").val());        
    var login_pwd = $.trim($("#login_pwd").val());              
  if(login_pwd=="" || login_ysbh==""){                  
     $("#msg_login").html("以上项目不能有空值!");
    }else{
      $("#msg_login").html("");
      $('#form1').submit();
  }
  })

  $("#change_button").click(function(e){    
    e.preventDefault();    
    var change_ysbh = $.trim($('#change_ysbh').val());     
    var old_pwd = $.trim($('#oldpwd').val());     
    var pwd = $.trim($('#ipwd').val());     
    var rpwd = $.trim($('#i2pwd').val());          
  if(pwd=="" || rpwd=="" || old_pwd=="" || change_ysbh==""){                   
     $("#msg_pwd").html("以上项目不能有空值!");
    }else{
      if(checkstr(pwd)){$("#msg_pwd").html("密码包含非法字符");$return;};
      if(pwd.length<6){$("#msg_pwd").html("密码不能小于六位字符");return;} 
      if(!pwd==rpwd){$("#msg_pwd").html("两次密码不匹配");return;}
      $("#msg_pwd").html(""); $('#form2').submit();
  }
  })
})   
