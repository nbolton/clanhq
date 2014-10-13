// +--------------------------------------------+ //
// | Clan-HQ.com CMS (Clan Management System)	| //
// | Version 3 - Development by r3n				| //
// | Clan-HQ.com CMS Framework by shaitan		| //
// | JavaScript file							| //
// +--------------------------------------------+ //

function jumpMenu(targ,selObj,restore){
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}

function displayStatusMsg(msgStr) {
  status=msgStr;
  document.returnValue = true;
}

function reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.pgW=innerWidth; document.pgH=innerHeight; onresize=reloadPage; }}
  else if (innerWidth!=document.pgW || innerHeight!=document.pgH) location.reload();
}
reloadPage(true);

function findObj(n, d) {
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function swapImage() {
  var i,j=0,x,a=swapImage.arguments; document.sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=findObj(a[i]))!=null){document.sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function swapImgRestore() {
  var i,x,a=document.sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function goToURL() {
  var i, args=goToURL.arguments; document.returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}

function callJS(jsStr) {
  return eval(jsStr)
}

function popupMsg(msg) {
  alert(msg);
}

function setPointer(theRow, thePointerColor, theNormalBgColor)
{
    var theCells = null;

    if (typeof(theRow.style) == 'undefined') {
        return false;
    }
    if (typeof(document.getElementsByTagName) != 'undefined') {
        theCells = theRow.getElementsByTagName('td');
    }
    else if (typeof(theRow.cells) != 'undefined') {
        theCells = theRow.cells;
    }
    else {
        return false;
    }

    var rowCellsCnt  = theCells.length;
    var currentColor = null;
    var newColor     = null;
    // Opera does not return valid values with "getAttribute"
    if (typeof(window.opera) == 'undefined'
        && typeof(theCells[0].getAttribute) != 'undefined' && typeof(theCells[0].getAttribute) != 'undefined') {
        currentColor = theCells[0].getAttribute('bgcolor');
        newColor     = (currentColor.toLowerCase() == thePointerColor.toLowerCase())
                     ? theNormalBgColor
                     : thePointerColor;
        for (var c = 0; c < rowCellsCnt; c++) {
            theCells[c].setAttribute('bgcolor', newColor, 0);
        } // end for
    }
    else {
        currentColor = theCells[0].style.backgroundColor;
        newColor     = (currentColor.toLowerCase() == thePointerColor.toLowerCase())
                     ? theNormalBgColor
                     : thePointerColor;
        for (var c = 0; c < rowCellsCnt; c++) {
            theCells[c].style.backgroundColor = newColor;
        }
    }

    return true;
}

function MM_reloadPage(init) {
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);

function MM_jumpMenu(targ,selObj,restore){
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}

function confirmForm(question, doCheckPass) {
	if (doCheckPass) {
		var pass_ok = checkPass(this);
		if (!pass_ok) return false;
	} else {
		var pass_ok = true;
	}
	var confirmed = confirm(question);
	if (pass_ok && confirmed) {
		return true;
	} else {
		return false;
	}
}

function confirmLink(theTarget, theLink, theQuestion) {
	confirmed = confirm(theQuestion);
	if (confirmed) {
    	eval(theTarget+".location='"+theLink+"'");
    }
}
  
function openChild(file,window) {
  childWindow=open(file,window,'resizable=no,width=300,height=400');
  if (childWindow.opener == null) childWindow.opener = self;
}