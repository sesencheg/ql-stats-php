 function showCursorMessage(event, pos, obj) {
    if (!obj)
      return;
    percent = parseFloat(obj.series.percent).toFixed(2);
    $.cursorMessage(obj.series.label+' ('+percent+'%)');
  }
  $("#mapgraph").mouseout($.hideCursorMessage);

  function makeGraph(data, graph, colour, min, max) {
    $.plot($(graph), data, {
      lines: { show: true, fill: true },
      colors: [colour, colour, colour],
      points: { show: true },
      yaxis: {minTickSize: 1, min: min, max: max
      },
      xaxis: {  
                mode: "time",
                minTickSize: [1, "day"],                
      }
    });
  }