<script>
$(document).ready(function(){
    	  var r = $("select[name=%areaselect%]");
          var id_c = c.val();
          if (id_c>=0)
            {

              r.empty();
              r.append( $('<option value="-1">Все регеоны</option>'));
              $.getJSON( "gcc.php?id="+id_c+"&obj=1", function( data ) {
                 $.each( data, function( key, val ) {
                 });
             });
            } else
              {
                $("select[name=%areaselect%] [value='-1']").attr("selected", "selected");

                $("select[name=%cityselect%]").attr("disabled","disabled");
                $("select[name=%cityselect%] [value='-1']").attr("selected", "selected");
              }
        });

    $("select[name=%areaselect%]").change(function () {
    	  var r = $("select[name=%areaselect%]");
    	  var g = $("select[name=%cityselect%]");
          var id_c = c.val();
          var id_r = r.val();
          if (id_r>=0)
            {
              g.empty();
              g.append( $('<option value="-1">Все города</option>'));
              $.getJSON( "gcc.php?id="+id_r+"&obj=2", function( data ) {
                 $.each( data, function( key, val ) {
                 g.append( $('<option value="'+val.id+'">'+val.value+'</option>'));
                 });
             });


            } else
              {
                $("select[name=%cityselect%] [value='-1']").attr("selected", "selected");
              }

        });
});
