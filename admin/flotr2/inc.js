<div id="container" style="width:98%;height:98%;min-width:200px;min-height:200px;"></div>
    <!--[if IE]>
    <script type="text/javascript" src="path/to/flashcanvas.js"></script>
    <![endif]-->
    <script type="text/javascript" src="flotr2/flotr2.min.js"></script>
    <script type="text/javascript">
      (function () {

 var
    d1    = [],
    start = new Date("%%DATE%% 01:00").getTime(),
    options,
    graph,
    dat1 = [[0, 13], [1, 8], [2, 5], [3, 13],[4,10]], 
    %%DAT%%,
    i, x, o;

    for(var i=0; i<dat.length; i++) {
      
      x = start-(dat[i][0]*1000*3600*24);
      d1.push([x, dat[i][1]]);
    }
        
  options = {
    xaxis : {
      mode : 'time', 
      labelsAngle : 45
    },
    selection : {
      mode : 'x'
    },
    HtmlText : false,
    title : 'Statistic Graph'
  };
        
  // Draw graph with default options, overwriting with passed options
  function drawGraph (opts) {

    // Clone the options, so the 'options' variable always keeps intact.
    o = Flotr._.extend(Flotr._.clone(options), opts || {});

    // Return a new graph.
    return Flotr.draw(
      container,
      [ d1 ],
      o
    );
  }

  graph = drawGraph();      
        
  Flotr.EventAdapter.observe(container, 'flotr:select', function(area){
    // Draw selected area
    graph = drawGraph({
      xaxis : { min : area.x1, max : area.x2, mode : 'time', labelsAngle : 45 },
      yaxis : { min : area.y1, max : area.y2 }
    });
  });
        
  // When graph is clicked, draw the graph with default area.
  Flotr.EventAdapter.observe(container, 'flotr:click', function () { graph = drawGraph(); });




})();
    </script>
