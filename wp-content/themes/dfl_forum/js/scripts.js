
const $ = jQuery.noConflict();

console.log($); 

const dropDownMenu = document.querySelector('.dropdown-menu'); 
const dropDownMenuBtn = document.getElementById('nav-click-1'); 

function showDropDownMenu() {
    alert('showDropDownMenu called'); 
    dropDownMenu.classList.add('show');
}

dropDownMenuBtn.addEventListener('click', showDropDownMenu); 