function contains(list, k) {
  for(var i=0; i < list.length; i++){
    if(list[i] === k){
      return true;
    }
  }
  return false;
}

var links = [];
var count = 0;
var re = /(https?:\/\/teespring.com\/[a-zA-Z0-9-]+)/gmi; 

function craw() {
    var str = document.getElementById("contentArea").innerHTML;
    var m;
    
    while ((m = re.exec(str)) !== null) {
        if (m.index === re.lastIndex) {
            re.lastIndex++;
        }
        if (!contains(links, m[0])) {
            count++;
            links.push(m[0]);
            console.log(count + ": " + m[0]);
        }
    }


    document.getElementById("u_0_r").click();
}

var t = setInterval(craw, 1000 * 60);