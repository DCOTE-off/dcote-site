const checkbox = document.querySelector('.toggle-input');
const ratingDefault = document.querySelector('.rating:not(.spoilers)')
const ratingSpoilers = document.querySelector('.rating.spoilers')
checkbox.addEventListener('change', function(event) {
    if (event.target.checked) {
        ratingDefault.classList.add('hidden')
        ratingSpoilers.classList.remove('hidden')
    } else {
        ratingSpoilers.classList.add('hidden')
        ratingDefault.classList.remove('hidden')
    }
});