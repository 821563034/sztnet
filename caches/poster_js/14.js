var links = new Array();
var isIE=!!window.ActiveXObject;
links[1] = "http://www.mysztnet.com/index.php?m=poster&c=index&a=poster_click&siteid=1&id=17";
if (isIE){
	if (document.readyState=="complete"){
		statAD('1', '17', '14');
	} else {
		document.onreadystatechange=function(){
			if(document.readyState=="complete") statAD('1', '17', '14');
		}
	}
} else {
	statAD('1', '17', '14');
}

links[2] = "http://www.mysztnet.com/index.php?m=poster&c=index&a=poster_click&siteid=1&id=16";
if (isIE){
	if (document.readyState=="complete"){
		statAD('1', '16', '14');
	} else {
		document.onreadystatechange=function(){
			if(document.readyState=="complete") statAD('1', '16', '14');
		}
	}
} else {
	statAD('1', '16', '14');
}

var imgs = new Array();
for(var n = 1; n <= 5; n++) imgs[n] = new Image();
imgs[1].src = "http://www.sztnet.com/uploadfile/2019/0220/20190220091230206.jpg";
imgs[2].src = "http://www.sztnet.com/uploadfile/2019/0220/20190220113331838.jpg";
var tits = new Array();
tits[1] ="";
tits[2] ="";
var imgwidth = 100;//图片宽度
var imgheight = 100;//图片宽度
var str = "";
str += "<span style='position:relative'>";
str += "<span><a id='dlink' href='" + links[1] + "' target='_blank'><img id='dimg' src='" + imgs[1].src + "' border='0' width='" + imgwidth + "' height='"+imgheight+"' style='filter:Alpha(opacity=100)' onmouseover='Pause(true)' onmouseout='Pause(false)'></a></span>";
//修改点1：循环添加内层div内容以增加个数
str += "</span>";
document.write(str);
var oi = document.getElementById("dimg");
var pause = false;
var curid = 1;
var lastid = 1;
var sw = 1;
var opacity = 100;
var speed = 15;
var delay = (document.all)? 400:700;

function SetAlpha(){
	if(document.all){
		if(oi.filters && oi.filters.Alpha) oi.filters.Alpha.opacity = opacity;
	} else {
		oi.style.MozOpacity = ((opacity >= 100)? 99:opacity) / 100;
	}
}

function statAD(siteid, id, pid) {
	var sp = document.createElement("SCRIPT");
	sp.src = "http://www.mysztnet.com/index.php?m=poster&c=index&a=show&siteid="+siteid+"&id="+id+"&spaceid="+pid;
	document.body.appendChild(sp);
}

function ImgSwitch(id, p){
	if(p){
		pause = true;
		opacity = 100;
		SetAlpha();
	}
	oi.src = imgs[id].src;
	document.getElementById("dlink").href = links[id];
	//document.getElementById("it" + lastid).className = "off";
	//document.getElementById("it" + id).className = "on";
	//document.getElementById("titnv").innerHTML = "<b>" + tits[id] + "</b>";
	curid = lastid = id;
}

function ScrollImg(){
	if(pause && opacity >= 100) return;
	if(sw == 0){
		opacity += 2;
		if(opacity > delay){ opacity = 100; sw = 1; }
	}
	if(sw == 1){
		opacity -= 3;
		if(opacity < 10){ opacity = 10; sw = 3; }
	}
	SetAlpha();
	if(sw != 3) return;
	sw = 0;
	curid++;

	if(curid > 2) curid = 1;
	ImgSwitch(curid, false);
}

function Pause(s){
	pause = s;
}

function StartScroll(){
	setInterval(ScrollImg, speed);
}

function CheckLoad(){
	if (imgs[1].complete == true && imgs[2].complete == true) {
		clearInterval(checkid);
		setTimeout(StartScroll, 2000);
	}
}
var checkid = setInterval(CheckLoad, 10);