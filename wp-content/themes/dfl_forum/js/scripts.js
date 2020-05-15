
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

   /**
    * Scroll To Top
    */
//    $(window).scroll(function () {
//     if ($(this).scrollTop() >= 50) {        //Need to change this so it shows up only if user stops scrolling or at bottom. 
//         $('#return-to-top').fadeIn(200);
//     } else {
//         $('#return-to-top').fadeOut(200);   // Else fade out the arrow
//     }
// });

// $('#return-to-top').click(function () {      // When arrow is clicked
//     $('body,html').animate({
//         scrollTop: 0                       // Scroll to top of body
//     }, 500);
// });