window.addEvent('domready', function () {
    // Disable in case of mobi plugin

    var globalHeaderElement = en4.seaocore.getDomElements('header');
    var globalFooterElement = en4.seaocore.getDomElements('footer');
    
    if(document.getElementById(globalHeaderElement))
        document.getElementById(globalHeaderElement).style.display = 'none';
    
    if(document.getElementById(globalFooterElement))
        document.getElementById(globalFooterElement).style.display = 'none';

    if(document.getElementById("cometchat"))
    	document.getElementById('cometchat').style.display = "none";
});