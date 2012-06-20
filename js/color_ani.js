/************************************************************************************************************
(C) www.dhtmlgoodies.com, October 2005

This is a script from www.dhtmlgoodies.com. You will find this and a lot of other scripts at our website.   

Terms of use:
You are free to use this script as long as the copyright message is kept intact. However, you may not
redistribute, sell or repost it without our permission.

Thank you!

www.dhtmlgoodies.com
Alf Magne Kalleland

************************************************************************************************************/
    
    /* You can modify the following javascript variables as you like */
    
    var initActiveMenuItem = 0; //  If one of the menu items should be initially highlighted.(-1 = none, 0 = first item, 1 = second...)
    var activeSmallSquareColor = '#D60808'; // Color of small square at the left of each menu item
    var colorSquareWidth = 2;   // Width of small square;
    var marginSquare = 1;   // Margin at the right of the small color square at the left of each menu item  
    
    var bgColorLinks = "#E2EBED";   // Background color for menu links
    var degreesToDarkenOrLighten = 25; // How many percent point to darken the color above on mouse over (10-15 percent point is usually enough).
    var darkenOnMouseOver = true;   // Darken or Lighten on mouse over (true = darken, false = lighten) 
    /* ABOUT COLORS
    You can use the color schemer at www.dhtmlgoodies.com to find your colors
    
    We use the HSB color system here, The HSB color system is based on three values
    
    * Hue = Which color, i.e. degree on color wheel
    * Saturation = Intensity of the color to use
    * Brightness = Brightness of the color
    
    
    When you use the color schemer at the site, pick a color from the palett or type it into the RGB text box(format #RRGGBB, example #E2EBED)
    Then adjust the value of brightness by typing in a new value in that text box(label "B"). 
    
    */      
        
    var timeStepOpacitySquare = 15; // Microseconds between each opacity change -> Lower value = faster
    var opacityChangePerStep = 10;  // Steps - change in opacity - on mouse out = Higher = faster   
    var timeStepSwitchBgColor = 10; // Microseconds between each text background change(darken or lighten)  -> Lower value = faster
    var bgColorStep  = 0.75;    // lower value = slower bg color fading(Usually, you will always have the value 1 or a little lower)
                                // Choose a low value if the number of degrees to darken is low.
    
    /************************************************************************************************************
    * END OF USER VARIABLES
    *
    * Don't change anything below here 
    *
    ************************************************************************************************************/
    var activeMenuItem = false;
    var activeMenuLink = false; 
    var menuObj;
    var brightnessLink = false; 
    var brightnessLinkMin = false;
    var darkenBrightnessCounter = false; // Darken or lighten - this variable is set manually
    var startHue = false;
    var startSat = false;
    
function showMenuItem() {
    var hsb = toHSV(this.getAttribute('bgColorItem'));
    if(darkenOnMouseOver){
        var brightness = hsb[2] - (degreesToDarkenOrLighten/100);
    }else{
        var brightness = hsb[2] + (degreesToDarkenOrLighten/100);
    }
    if(brightness<0)brightness=0;
    if(brightness>1)brightness=1;
    var rgb = toRgb(hsb[0],hsb[1],brightness);
    this.style.backgroundColor = rgb;
    this.currentBgColorItem = rgb;
    this.setAttribute('currentBgColorItem',rgb);
    
    var obj = this.parentNode.getElementsByTagName('DIV')[0];
    obj.setAttribute('okToHide','0');
    obj.okToHide = 0;
    this.setAttribute('okToHide','0');
    this.okToHide = 0;
    obj.style.visibility = 'visible';
    
    obj.style.opacity = 0.98;   
    obj.style.filter = 'alpha(opacity=98)';
    

}
    
function hideMenuItem() {
    if(this.getAttribute('initActive')=='1')return;
    if(this.initActive=='1')return;
    var obj = this.parentNode.getElementsByTagName('DIV')[0];
    obj.setAttribute('okToHide','1');
    obj.okToHide = 1;
    this.setAttribute('okToHide','1');
    this.okToHide = 1;
    obj.style.visibility = 'visible';       
    if(navigator.userAgent.indexOf('Opera')>=0){
        obj.style.visibility = 'hidden';
    }else{
        progressHideSquare(obj.id,(opacityChangePerStep*-1));   
    }       
    progressShowHideBgColor(this.id);   
}
    
function progressShowHideBgColor(inputId) { 
    var obj = document.getElementById(inputId);
    var currentBgColor = obj.getAttribute('currentBgColorItem');
    if(obj.getAttribute('okToHide')=='0')return;
    if(!currentBgColor)currentBgColor = obj.currentBgColorItem;
    if(currentBgColor){
        var hsb = toHSV(currentBgColor);
        var brightness = hsb[2];            
        brightness+=darkenBrightnessCounter;
        if((brightness>brightnessLink && darkenOnMouseOver) || (brightness<brightnessLink && !darkenOnMouseOver))brightness = brightnessLink;
        var rgb = toRgb(startHue,startSat,brightness);
        obj.style.backgroundColor = rgb;
        obj.currentBgColorItem = rgb;
        obj.setAttribute('currentBgColorItem',rgb);     
        if((hsb[2]<brightness && darkenOnMouseOver) || (hsb[2]>brightness && !darkenOnMouseOver)){
            setTimeout('progressShowHideBgColor(\'' + inputId + '\')',timeStepSwitchBgColor);               
        }else{
            obj.style.backgroundColor = bgColorLinks;
        }
    }           
}   
    
    function progressHideSquare(inputId,step)
    {
        
        var obj = document.getElementById(inputId);

        if(obj.getAttribute('okToHide')=='0' && step<0)return;
                
        if(document.all){
            var currentOpacity = obj.style.filter.replace(/[^\d]/g,'')/1;

            if(currentOpacity>=99){

            }
            else if(currentOpacity==11){
                obj.style.visibility='hidden';
            }else{
                currentOpacity = currentOpacity + step;
                if(currentOpacity<1)currentOpacity=1;
                if(currentOpacity>99)currentOpacity=9;
                obj.style.filter = 'alpha(opacity=' + currentOpacity + ')';
                setTimeout('progressHideSquare("' + inputId + '",' + (step) + ')',timeStepOpacitySquare);
            }               
        }else{
            step = step / 100;
            var currentOpacity = obj.style.opacity/1;

            if(currentOpacity>=0.99){

            }
            else if(currentOpacity==0.01){
                obj.style.visibility='hidden';
            }else{
                currentOpacity = currentOpacity + step;

                if(currentOpacity<0.01)currentOpacity=0.01;
                if(currentOpacity>0.99)currentOpacity=0.99;
                obj.style.opacity = currentOpacity;
                setTimeout('progressHideSquare("' + inputId + '",' + (step*100) + ')',timeStepOpacitySquare);
            }   
        }       
    }
    
    
function dhtmlgoodies_initMenu() {
    menuObj = document.getElementById('sidebar');
    var hsbArray = toHSV(bgColorLinks);
    brightnessLink = hsbArray[2];
    startHue = hsbArray[0];
    startSat = hsbArray[1];
    if(darkenOnMouseOver){
        brightnessLinkMin = Math.max(hsbArray[2] - (degreesToDarkenOrLighten/100),0);
        darkenBrightnessCounter = bgColorStep/100;
    }else{
        brightnessLinkMin = Math.min(hsbArray[2] + (degreesToDarkenOrLighten/100),1);
        darkenBrightnessCounter = (bgColorStep/100)*-1;
    }
    var listItems = menuObj.getElementsByTagName('LI');
    for(var no=0;no<listItems.length;no++){
        listItems[no].id = 'listItem' + no; 

        var menuLink = listItems[no].getElementsByTagName('A')[0];
        var height = menuLink.offsetHeight; 
        menuLink.style.display='block';
        menuLink.style.height=height + 'px';
        menuLink.style.lineHeight=height + 'px';
        menuLink.onmouseover = showMenuItem;        
        menuLink.onmouseout = hideMenuItem;     
        menuLink.style.backgroundColor = bgColorLinks;
        menuLink.setAttribute('bgColorItem',bgColorLinks);
        menuLink.bgColorItem = bgColorLinks;
        menuLink.setAttribute('currentBgColorItem',bgColorLinks);
        menuLink.currentBgColorItem = bgColorLinks;
        menuLink.id = 'listLink' + no;
        
        
        
        var colorDiv = document.createElement('DIV');
        colorDiv.innerHTML = '<span><\/span>';
        colorDiv.style.height = height + 'px';
        colorDiv.style.width = colorSquareWidth + 'px';
        colorDiv.style.backgroundColor = activeSmallSquareColor;
        colorDiv.style.marginRight = marginSquare + 'px';
        colorDiv.style.visibility = 'hidden'            
        colorDiv.id = 'colorSquare' + no;
        colorDiv.style.opacity = 0.99;  // Not possible to use 1 because of JS flickering in Firefox
        colorDiv.style.filter = 'alpha(opacity=100)';
        
        if(initActiveMenuItem==no){
            colorDiv.style.visibility = 'visible';
            menuLink.style.backgroundColor = toRgb(startHue,startSat,brightnessLinkMin);
            menuLink.initActive = '1';
            menuLink.setAttribute('initActive','1');
            
        }
        
        listItems[no].appendChild(colorDiv);            
        listItems[no].appendChild(menuLink);

        var clearDiv = document.createElement('DIV');
        clearDiv.style.clear='both';
        listItems[no].appendChild(clearDiv);
                    
        var currentWidth = (listItems[no].offsetWidth - colorDiv.offsetWidth - marginSquare);
        menuLink.style.width =  currentWidth + 'px';
        while(listItems[no].offsetHeight>=(menuLink.offsetHeight*2)){       
            currentWidth--; 
            menuLink.style.width =  currentWidth + 'px';
            
        }
    }       
}
window.onload = dhtmlgoodies_initMenu;
