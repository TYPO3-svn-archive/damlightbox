var Slimbox;(function(){var g=0,options,images,activeImage,prevImage,nextImage,top,fx,preload,preloadPrev=new Image(),preloadNext=new Image(),overlay,center,image,iframe,prevLink,nextLink,bottomContainer,bottom,caption,number,printB,saveB;window.addEvent("domready",function(){$(document.body).adopt($$([overlay=new Element("div",{id:"lbOverlay"}).addEvent("click",close),center=new Element("div",{id:"lbCenter"}),bottomContainer=new Element("div",{id:"lbBottomContainer"})]).setStyle("display","none"));image=new Element("div",{id:"lbImage"}).injectInside(center).adopt(prevLink=new Element("a",{id:"lbPrevLink",href:"#"}).addEvent("click",previous),nextLink=new Element("a",{id:"lbNextLink",href:"#"}).addEvent("click",next));iframe=new Element("iframe",{id:"lbImage"}).addEvent("load",nextEffect).injectInside(image);bottom=new Element("div",{id:"lbBottom"}).injectInside(bottomContainer).adopt(new Element("a",{id:"lbCloseLink",href:"#"}).addEvent("click",close),printB=new Element("a",{id:'lbPrintLink',href:'#'}).addEvent("click",print),saveB=new Element("a",{id:'lbSaveLink',href:'#'}).addEvent("click",save),caption=new Element("div",{id:"lbCaption"}),number=new Element("div",{id:"lbNumber"}),new Element("div",{styles:{clear:"both"}}));fx={overlay:new Fx.Tween(overlay,{property:"opacity",duration:500}).set(0),image:new Fx.Tween(image,{property:"opacity",duration:500,onComplete:nextEffect}),bottom:new Fx.Tween(bottom,{property:"margin-top",duration:400})}});Slimbox={open:function(a,b,c){options=$extend({loop:false,overlayOpacity:0.8,resizeDuration:400,resizeTransition:false,initialWidth:250,initialHeight:250,psScriptPath:'',enablePrintButton:0,enableSaveButton:0,animateCaption:true,showCounter:true,counterText:"Item {x} of {y}",defaultIframeWidth:850,defaultIframeHeight:500},c||{});if(typeof a=="string"){a=[[a,b]];b=0}images=a;options.loop=options.loop&&(images.length>1);position();setup(true);top=window.getScrollTop()+(window.getHeight()/15);fx.resize=new Fx.Morph(center,$extend({duration:options.resizeDuration,onComplete:nextEffect},options.resizeTransition?{transition:options.resizeTransition}:{}));center.setStyles({top:top,width:options.initialWidth,height:options.initialHeight,marginLeft:-(options.initialWidth/2),display:""});fx.overlay.start(options.overlayOpacity);g=1;return changeImage(b)}};Element.implement({slimbox:function(a,b){$$(this).slimbox(a,b);return this}});Elements.implement({slimbox:function(b,c,d){c=c||function(a){return[a.href,a.title,a.rev]};d=d||function(){return true};var e=this;e.removeEvents("click").addEvent("click",function(){var a=e.filter(d,this);return Slimbox.open(a.map(c),a.indexOf(this),b)});return e}});function position(){overlay.setStyles({top:window.getScrollTop(),height:window.getHeight()})}function setup(c){["object",window.ie?"select":"embed"].forEach(function(b){Array.forEach(document.getElementsByTagName(b),function(a){if(c)a._slimbox=a.style.visibility;a.style.visibility=c?"hidden":a._slimbox})});overlay.style.display=c?"":"none";var d=c?"addEvent":"removeEvent";window[d]("scroll",position)[d]("resize",position);document[d]("keydown",keyDown)}function keyDown(a){switch(a.code){case 27:case 88:case 67:close();break;case 37:case 80:previous();break;case 39:case 78:next()}return false}function previous(){return changeImage(prevImage)}function next(){return changeImage(nextImage)}function changeImage(a){if((g==1)&&(a>=0)){g=2;activeImage=a;prevImage=((activeImage||!options.loop)?activeImage:images.length)-1;nextImage=activeImage+1;if(nextImage==images.length)nextImage=options.loop?0:-1;$$(prevLink,nextLink,image,iframe,bottomContainer).setStyle("display","none");fx.bottom.cancel().set(0);fx.image.set(0);center.className="lbLoading";var b=images[activeImage][0];var c=/\.(jpe?g|png|gif|bmp)/i;if(b.match(c)){$$(printB,saveB).setStyle("display","");preload=new Image();preload.datatype='image';preload.onload=nextEffect;preload.src=b}else{preload=new Object();preload.datatype='iframe';rev=images[activeImage][2];preload.w=matchOrDefault(rev,new RegExp("width=(\\d+)","i"),options.defaultIframeWidth);preload.h=matchOrDefault(rev,new RegExp("height=(\\d+)","i"),options.defaultIframeHeight);scroll=options.iframeScrolling;iframe.setProperties({id:"lbFrame_"+new Date().getTime(),width:preload.w,height:preload.h,scrolling:scroll,frameBorder:0,src:b})}}return false}function nextEffect(){switch(g++){case 2:center.className="";if(preload.datatype=='image'){image.setStyles({backgroundImage:"url("+preload.src+")",display:""});$$(image,bottom).setStyle("width",preload.width);$$(image,prevLink,nextLink).setStyle("height",preload.height);$$(prevLink,nextLink).setStyle("width","50%")}else{image.setStyles({backgroundImage:"",display:""});$$(image,bottom).setStyle("width",preload.w);$$(image).setStyle("height",preload.h);$$(prevLink,nextLink).setStyle("width","50%");$$(prevLink,nextLink).setStyle("height","100%");iframe.setStyles({display:""})}caption.set('html',images[activeImage][1]||"");number.set('html',(options.showCounter&&(images.length>1))?options.counterText.replace(/{x}/,activeImage+1).replace(/{y}/,images.length):"");if(prevImage>=0)preloadPrev.src=images[prevImage][0];if(nextImage>=0)preloadNext.src=images[nextImage][0];if(center.clientHeight!=image.offsetHeight){fx.resize.start({height:image.offsetHeight});break}g++;case 3:if(center.clientWidth!=image.offsetWidth){fx.resize.start({width:image.offsetWidth,marginLeft:-image.offsetWidth/2});break}g++;case 4:bottomContainer.setStyles({top:top+center.clientHeight,width:center.style.width,marginLeft:center.style.marginLeft,visibility:"hidden",display:""});fx.image.start(1);break;case 5:if(prevImage>=0)prevLink.style.display="";if(nextImage>=0)nextLink.style.display="";if(options.animateCaption){fx.bottom.set(-bottom.offsetHeight).start(0)}bottomContainer.style.visibility="";g=1}}function close(){if(g){g=0;preload.onload=$empty;for(var f in fx)fx[f].cancel();$$(center,bottomContainer).setStyle("display","none");fx.overlay.chain(setup).start(0)}return false}function matchOrDefault(a,b,c){var d=a.match(b);return d?d[1]:c}function print(){return printOrSave('print')}function save(){return printOrSave('save')}function printOrSave(a){if(options.psScriptPath){fullpath=matchOrDefault(rev,new RegExp("src=(.+)","i"));var b=window.open(options.psScriptPath+'?mode='+a+'&image='+fullpath,'printsave','left=0,top=0,width='+(parseInt(image.style.width))+',height='+(parseInt(image.style.height))+',toolbar=0,resizable=1');return false}return true}})();