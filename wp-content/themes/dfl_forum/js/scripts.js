

// const forumContainer = document.querySelector('.bp-nouveau'); 
// const forumLinks = forumContainer.querySelectorAll('a');

// const $ = jQuery; 


// console.log('Location: ' + location);
// if(forumLinks) {
//     forumLinks.forEach(link => {
//         // link.addEventListener('click', NoReload);
//         link.addEventListener('click', AjaxExp)
//     });
// }

function AjaxExp(e) {
    console.log(e); 
    e.preventDefault(); 

    alert(this);
    
    
}

// $(document).ready(function() { /// Wait till page is loaded
//     $('#detailed').click(function(){
//        $('#main').load('property-detailed.php #main', function() {
//             /// can add another function here
//        });
//     });
//  });