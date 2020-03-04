 //进行切换
 var fullScreenClickCount=0;
 //调用各个浏览器提供的全屏方法
 var handleFullScreen = function () {
     var de = document.documentElement;

     if (de.requestFullscreen) {
         de.requestFullscreen();
     } else if (de.mozRequestFullScreen) {
         de.mozRequestFullScreen();
     } else if (de.webkitRequestFullScreen) {
         de.webkitRequestFullScreen();
     } else if (de.msRequestFullscreen) {
         de.msRequestFullscreen();
     }
     else {
         wtx.info("当前浏览器不支持全屏！");
     }

 };
 //调用各个浏览器提供的退出全屏方法
 var exitFullscreen=function() {
     if(document.exitFullscreen) {
         document.exitFullscreen();
     } else if(document.mozCancelFullScreen) {
         document.mozCancelFullScreen();
     } else if(document.webkitExitFullscreen) {
         document.webkitExitFullscreen();
     }
 }

 $(".fullscreen").click(function () {
     if (fullScreenClickCount % 2 == 0) {
         handleFullScreen();
     } else {
         exitFullscreen();
     }
     fullScreenClickCount++;
 });

 $(document).ready(function(){
   // $("a").click(function(e){  
   //     e.preventDefault();     
   // })
    $('.modal_button').click(function(){       
        var contentHeight =$(window).height() - 138;
        var $MODAL_FRAME =$('#iframe_modal'); 
        console.log(contentHeight);
        var $modal_src = $(this).attr("modal_src");
        var $modal_text = $(this).attr("modal_text");
        if ($modal_src == undefined){
            $('#modal_title').text('');
            $MODAL_FRAME.attr("src",'');
            $('#userModal').modal('show');
            return;
	    }else{
            $('#modal_title').text($modal_text);                        
            $MODAL_FRAME.css('height', contentHeight);
            $MODAL_FRAME.css('width', '100%');	    
            $MODAL_FRAME.attr("src",$modal_src);
            $('#userModal').modal('show');
	    }
        $('#userModal').modal('show');

       
    }) 
})
