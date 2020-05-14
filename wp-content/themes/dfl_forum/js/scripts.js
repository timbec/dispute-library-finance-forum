
const $ = jQuery.noConflict();

console.log($); 


const dropDownMenu = document.querySelector('.dropdown-menu'); 
const dropDownMenuBtn = document.getElementById('nav-click-1'); 

function toggleDropDownMenu() {
    console.log('toggleDropDownMenu called'); 
    dropDownMenu.classList.toggle('show'); 


}

dropDownMenuBtn.addEventListener('click', toggleDropDownMenu); 