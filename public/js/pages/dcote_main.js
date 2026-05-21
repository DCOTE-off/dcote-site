const spoilersBtn = document.querySelector('.spoilers-btn');
const ratingDefault = document.querySelector('.rating:not(.spoilers)')
const ratingSpoilers = document.querySelector('.rating.spoilers')
spoilersBtn.addEventListener('click', () =>{
        ratingDefault.classList.toggle('hidden')
        ratingSpoilers.classList.toggle('hidden')
        spoilersBtn.classList.toggle('spoilers-true')
});