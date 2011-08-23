/*  relies on:
       https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js
       http://maps.google.com/maps/api/js?sensor=false
*/

var googlemaps = new Array();

function googlemaps_init(address,divid,mapoptions){
   var map;
   var geocoder;

   var sanitized_divid = divid.replace(/-/,'_');

   mapoptions = mapoptions || eval(sanitized_divid+'_mapoptions') || null;

   this.get = function(str){
      return(eval(str));
   }

   this.refresh = function(){
      google.maps.event.trigger(map,'resize'); 
   }

   function setup_map(results,status){
      if(status==google.maps.GeocoderStatus.OK){
         latlng = results[0].geometry.location;

         var options = Array();

         if(mapoptions){
            options = mapoptions;
            if(!options['mapTypeId']){
               options['mapTypeId']=google.maps.MapTypeId.ROADMAP;
            }
            if(!options['zoom']){
               options['zoom']=13;
            }
            options['center']=latlng;
         }else{
            options = {
               'zoom':13,
               'center': latlng,
               'mapTypeId':google.maps.MapTypeId.ROADMAP
            };
         }

         map = new google.maps.Map(document.getElementById(divid),options);

         var marker = new google.maps.Marker({'map':map,'position':latlng});
      }else{
         try{
            if(console){
               console.log('no success: '+status);
            }
         }catch(e){}
      }
   }

   geocoder = new google.maps.Geocoder();

   geocoder.geocode({'address':address},setup_map);

   googlemaps[divid]=this;
}

var togooglemaps = new Array();

function togooglemaps_init(id,settings){
   if(typeof settings == 'undefined'){
      settings = {'default-button':'top_left'};
   }

   this.get = function(str){
      return(eval(str));
   }

   function calc_width(){
      var pat = /(\d*)/;

      var width = 0;
 
      width+=parseInt(pat.exec($('#'+id).css('width')));
      width+=parseInt(pat.exec($('#'+id).css('padding-left')));
      width+=parseInt(pat.exec($('#'+id).css('padding-right')));
      width+=parseInt(pat.exec($('#'+id).css('margin-left')));
      width+=parseInt(pat.exec($('#'+id).css('margin-right')));
      width+=parseInt(pat.exec($('#'+id).css('border-left-width')));
      width+=parseInt(pat.exec($('#'+id).css('border-right-width')));

      return(width); 
   }

   function calc_height(){
      var pat = /(\d*)/;

      var width = 0;

      width+=parseInt(pat.exec($('#'+id).css('height')));
      width+=parseInt(pat.exec($('#'+id).css('padding-top')));
      width+=parseInt(pat.exec($('#'+id).css('padding-bottom')));
      width+=parseInt(pat.exec($('#'+id).css('margin-top')));
      width+=parseInt(pat.exec($('#'+id).css('margin-bottom')));
      width+=parseInt(pat.exec($('#'+id).css('border-top-width')));
      width+=parseInt(pat.exec($('#'+id).css('border-bottom-width')));

      return(width);
   }

   var count = 0;
   this.toggle = function(showhide){
      var img = $('#'+id); var map = $('#'+id+'-map');
      if(++count%2==0 || showhide){
         var inter = img;
         img = map; map = inter;
      }
      img.animate({'opacity': 0.0},1000,function(){
         map.css('z-index','2');
         img.css('z-index','1').css('opacity','1.0');
      });

      //default-button
      if(count%2==0){
         $('#'+id+'-default-button').html('Show Map').css('background-color','#00ff00').css('color','#000000');
      }else{
         $('#'+id+'-default-button').html('Go back...').css('background-color','#ff0000').css('color','#ffffff');
      }
   }

   this.resize = function(height,width){
      $('#'+id+'-wrap').animate({'height':height,'width':width},1000,googlemaps[id+'-map'].refresh);
   }

   this.restore = function(){
      $('#'+id+'-wrap').animate({'height':orig_height,'width':orig_width},1000,googlemaps[id+'-map'].refresh);
   }

   var orig_height = calc_height(), orig_width = calc_width();

   var wrap_css = "height:"+orig_height+"px;width:+"+orig_width+"px;position:relative;";

   var map_css = "position:absolute;height:100%;width:100%;z-index:1;";
   var map_html = "<div id='"+id+"-map' class='googlemaps-map' style='"+map_css+"'></div>";

   var button_css = "position:absolute;z-index:3;top:0px;right:0px;border:1px solid;margin:5px;background-color:#00ff00;";
   var button_js = "togooglemaps[\""+id+"\"].toggle();";
   var button_html = settings['default-button']?"<div id='"+id+"-default-button' class='googlemaps-button' style='"+button_css+"' onclick='"+button_js+"'>Show Map</div>":"";

   var wrap_html = "<div id='"+id+"-wrap' class='googlemaps-wrap' style='"+wrap_css+"'>"+map_html+button_html+"</div>";
   
   $('#'+id).parent().append(wrap_html);
   $('#'+id+'-wrap').insertBefore('#'+id);
   $('#'+id+'-wrap').css('display','block');
   $('#'+id).appendTo($('#'+id+'-wrap'));

   $('#'+id).css('position','absolute').css('z-index','2');

   if(settings['address']){
      setTimeout(function(){googlemaps_init(settings['address'],id+'-map');},250);
   }else{
      try{

         var pat = /googlemaps\-address_([^\s]+)/;
         var classes = $('#'+id).attr('class');

         var raw = pat.exec(classes);

         if(raw){
            var address = raw[1];

            address = address.replace(/\-\-\-/g,', ');
            address = address.replace(/\-\-/g,', ');
            address = address.replace(/\-/g,' ');

            setTimeout(function(){googlemaps_init(address,id+'-map');},250);
         }else{
            alert('#'+id+'-map has not been initialized, no address could be determined');
         }
      }catch(e){
         alert(e);
      }

   }

   togooglemaps[id]=this;
}

function init_togooglemaps(){
      $('.togooglemaps').each(function(){
      try{
         var id;
         if(!(id = $(this).attr('id'))){
            id='togooglemaps-'+Math.floor(Math.random()*1000000);
            $(this).attr('id',id);
         }

         setTimeout(function(){togooglemaps_init(id);},250);
      }catch(e){
         alert(e);
      }
   });
}

function init_googlemaps(){
   $('.googlemaps').each(function(){
      try{
         var id;
         if(!(id = $(this).attr('id'))){
            id='googlemaps-'+Math.floor(Math.random()*1000000);
            $(this).attr('id',id);
         }

         var pat = /googlemaps\-address_([^\s]+)/;
         var classes = $(this).attr('class');

         var raw = pat.exec(classes);
         if(!raw){return;} 

         var address = raw[1];

         address = address.replace(/\-\-\-/g,', ');
         address = address.replace(/\-\-/g,', ');
         address = address.replace(/\-/g,' ');

         setTimeout(function(){googlemaps_init(address,id);},250);
      }catch(e){
         alert(e);
      }
   });
}

$(document).ready(init_googlemaps);
$(document).ready(init_togooglemaps);
