window.onload = function (){
    
    document.querySelector('#new_folder').onclick = function(){
        document.querySelector('#create_folder').style.display = 'block';
        document.querySelector('#new_folder').style.display = "none";
    }
}