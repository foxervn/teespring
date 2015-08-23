function contains(list, k) {
  for(var i=0; i < list.length; i++){
    if(list[i] === k){
      return true;
    }
  }
  return false;
}


var re = /(https?:\/\/teespring.com\/[a-zA-Z0-9-]+)/gmi; 
var str = document.getElementById("contentArea").innerHTML;
var m;
var links = [];
var count = 0; 
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


document.getElementById("u_0_r").click()