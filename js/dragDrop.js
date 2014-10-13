    currentX = currentY = 0;
    whichEl = null;
    
    function grabEl(e) {
        if (IE4) {
            whichEl = event.srcElement;
    
            while (whichEl.id.indexOf("DRAG") == -1) {
                whichEl = whichEl.parentElement;
                if (whichEl == null) { return }
            }
        }
        else {
            mouseX = e.pageX;
            mouseY = e.pageY;
    
            for ( i=0; i<document.layers.length; i++ ) {
            tempLayer = document.layers[i];
                if ( tempLayer.id.indexOf("DRAG") == -1 ) { continue }
                if ( (mouseX > tempLayer.left) && (mouseX < (tempLayer.left + tempLayer.clip.width)) && (mouseY > tempLayer.top) && (mouseY < (tempLayer.top + tempLayer.clip.height)) ) {
                    whichEl = tempLayer;
                }
            } 
    
            if (whichEl == null) { return}
        }
    
        if (whichEl != activeEl) {
            if (IE4) { whichEl.style.zIndex = activeEl.style.zIndex + 1 }
                else { whichEl.moveAbove(activeEl) };
                activeEl = whichEl;
        }
    
        if (IE4) {
            whichEl.style.pixelLeft = whichEl.offsetLeft;
            whichEl.style.pixelTop = whichEl.offsetTop;
    
            currentX = (event.clientX + document.body.scrollLeft);
            currentY = (event.clientY + document.body.scrollTop); 
    
        }
        else {
            currentX = e.pageX;
            currentY = e.pageY;
    
            document.captureEvents(Event.MOUSEMOVE);
            document.onmousemove = moveEl;
        }
    }
    
    function moveEl(e) {
        if (whichEl == null) { return };
    
        if (IE4) {
            newX = (event.clientX + document.body.scrollLeft);
            newY = (event.clientY + document.body.scrollTop);
        }
        else {
            newX = e.pageX;
            newY = e.pageY;
        }

        distanceX = (newX - currentX);
        distanceY = (newY - currentY);
        currentX = newX;
        currentY = newY;
    
        if (IE4) {
            whichEl.style.pixelLeft += distanceX;
            whichEl.style.pixelTop += distanceY;
            event.returnValue = false;
        }
        else { whichEl.moveBy(distanceX,distanceY) }
    }
    
    function checkEl() {
        if (whichEl!=null) { return false }
    }
    
    function dropEl() {
        if (NS4) { document.releaseEvents(Event.MOUSEMOVE) }
        whichEl = null;
    }
    
    function cursEl() {
        if (event.srcElement.id.indexOf("DRAG") != -1) {
            event.srcElement.style.cursor = "move"
        }
    }
    
    if (ver4) {
        if (NS4) {
            document.captureEvents(Event.MOUSEDOWN | Event.MOUSEUP);
        }
        else {
            document.onmousemove = moveEl;
            document.onselectstart = checkEl;
            document.onmouseover = cursEl;
        }
    
        document.onmousedown = grabEl;
        document.onmouseup = dropEl;
    }
    
