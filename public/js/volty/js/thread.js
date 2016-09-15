self.addEventListener('message', function(e) {
  var data = e.data;
  var clr;
  switch (data.cmd) {
    case 'start': clr=setTimeout(function(){self.postMessage(data.msg);},data.msg1);
   // console.log('Started clr');
    	          break;
    case 'stop':  clearTimeout(clr);//self.close(); // Terminates the worker.
    //console.log('please stop me   :');
                  break;
    default:    break;// self.postMessage('Unknown command: ' + data.msg);break;
  };
}, false);