// Example: Adding a simple hover effect class
document.querySelectorAll('.card').forEach(card => {
    card.addEventListener('mouseover', () => {
        card.classList.add('card-hover');
    });
    card.addEventListener('mouseout', () => {
        card.classList.remove('card-hover');
    });
});
