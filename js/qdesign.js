function setCookie(name, value, options) {
  options = options || {};

  var expires = options.expires;

  if (typeof expires == "number" && expires) {
    var d = new Date();
    d.setTime(d.getTime() + expires*1000);
    expires = options.expires = d;
  }
  if (expires && expires.toUTCString) { 
  	options.expires = expires.toUTCString();
  }

  value = encodeURIComponent(value);

  var updatedCookie = name + "=" + value;

  for(var propName in options) {
    updatedCookie += "; " + propName;
    var propValue = options[propName];    
    if (propValue !== true) { 
      updatedCookie += "=" + propValue;
     }
  }

  document.cookie = updatedCookie;
}


function SetColors()
{
 var r=$("#rotate").prop("checked");
 if (r) {
    var h=$("#dev option:selected").attr("w");
    var w=$("#dev option:selected").attr("h");
 } else {
    var w=$("#dev option:selected").attr("w");
    var h=$("#dev option:selected").attr("h");
  }
 $('#canvas').css("width", w);
 $('#canvas').css("height", h);


 var BGColor = $("#cpBoth").colorpicker("val");
 var BTNColor = $("#cpBoth1").colorpicker("val");
 var QSTColor = $("#cp_q").colorpicker("val");
 var QSTSize = $("#q_size").spinner( "value" );
 var QSTFont = $("#q_font").val();
 var ANSColor = $("#cp_a").colorpicker("val");
 var ANSSize = $("#a_size").spinner( "value" );
 var ANSFont = $("#a_font").val();
 //var QX = $("#q_offsx").val();
 var QY = $("#q_offsy").val();
 //var AX = $("#a_offsx").val();
 var AY = $("#a_offsy").val();

 $('#canvas').css("background-color", BGColor);

 $('#offset1').css("height", QY);
 //$('#offset1').css("padding-left", QX);
 $('#offset2').css("height", AY);
 //$('#offset2').css("padding-left", QX);

 $('#quest').css("color", QSTColor);
 $('#quest').css("font-size", QSTSize);
 $('#quest').css("font-family", QSTFont);


 $('.qbth').css("font-size", ANSSize);
 $('.qbth').css("font-family", ANSFont);
 $('.qbth').css("color", ANSColor);
 $('.qbth').css("background-color", BTNColor);



}
$(document).ready(function()
{
  $('#cpBoth,#cpBoth1,.cpick').colorpicker();
  $( ".spinner" ).spinner({
                        min: 0
                    });
   $( ".spinner" ).spinner({
     change: function( event, ui ) {
      SetColors();
   }
   });

   $( ".spinner" ).spinner({
     spin: function( event, ui ) {
      SetColors();
   }
   });

  $('#returnBtn').on('click', function() {
     var idq=$("#returnBtn").attr("idq");
     window.location = "questions_"+idq;
   });



  $('#cpBoth,#cpBoth1,.cpick').on('change.color', function(event, color){
    SetColors();
  });

  $('.fontsel').on('change', function() {
     SetColors();
   });

  $('#dev').on('change', function() {
     setCookie("dev",$("#dev").val(),{ expires: 9999999 });
     SetColors();
   });

  $('#rotate').on('change', function() {
     setCookie("rotate",$("#rotate").prop("checked"),{ expires: 9999999 });
     SetColors();
   });

 SetColors();
});

