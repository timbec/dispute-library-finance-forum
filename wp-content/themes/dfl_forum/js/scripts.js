
const $ = jQuery.noConflict();

console.log($); 

// Clear and succint: https://www.w3schools.com/howto/tryit.asp?filename=tryhow_css_js_dropdown

const dropDownMenuBtn = document.getElementById('nav-click-1'); 
const dropDownUserBtn = document.getElementById('nav-click-2'); 
const dropDownMenu = document.querySelector('.dropdown-menu .menu'); 
const dropDownUserInfo = document.querySelector('.dropdown-user-info'); 
console.log(dropDownUserInfo); 

function toggleDropDownMenu() {
    
    dropDownUserInfo.classList.remove('show'); 
    dropDownMenu.classList.toggle('show'); 
}

function toggleDropDownUser() {
     
    dropDownMenu.classList.remove('show'); 
    dropDownUserInfo.classList.toggle('show'); 
}

dropDownMenuBtn.addEventListener('click', toggleDropDownMenu); 
dropDownUserBtn.addEventListener('click', toggleDropDownUser); 