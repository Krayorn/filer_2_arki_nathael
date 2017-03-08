window.onload = function (){
    
    document.querySelector('#new_folder').onclick = function(){
        document.querySelector('#create_folder').style.display = 'block';
        document.querySelector('#new_folder').style.display = "none";
    }

    var close = document.querySelectorAll('.close');
    
    for(var i = 0; i < close.length; i++){
		close[i].onclick = function(){
		this.parentNode.style.display = "none";
        this.parentNode.parentNode.childNodes[2].style.display = "none";
        this.parentNode.parentNode.childNodes[0].style.display = "inline";
		}
	}

    var open = document.querySelectorAll('.open');

    for(var i = 0; i < open.length; i++){
		open[i].onclick = function(){
		this.parentNode.style.display = "none";
        this.parentNode.parentNode.childNodes[1].style.display = "block";
        this.parentNode.parentNode.childNodes[2].style.display = "flex";
		}
	}
}