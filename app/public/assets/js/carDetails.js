document.addEventListener('DOMContentLoaded', () => {
    if (!carId) {
        document.getElementById('car-details').innerHTML = '<p>Invalid Car ID</p>';
        return;
    }

    fetch(`/api/cars/${carId}`)
        .then(res => res.json())
        .then(car => {
            if (!car || !car.car_id) {
                document.getElementById('car-details').innerHTML = '<p>Car not found</p>';
                return;
            }

            const isOnSale = car.on_sale === 'yes' && car.discount > 0;
            const originalPrice = Number(car.price).toFixed(2);
            const discountedPrice = (car.price * (1 - car.discount / 100)).toFixed(2);

            const priceHTML = isOnSale
                ? `<p class="card-text">Price: <del>$${originalPrice}</del> <span class="text-success">$${discountedPrice}</span></p>
                   <p class="text-danger">On Sale: ${car.discount}% OFF</p>`
                : `<p class="card-text">Price: $${originalPrice}</p>`;

            document.getElementById('car-details').innerHTML = `
                <img src="/assets/images/${car.image_path}" alt="${car.brand} ${car.model}" class="card-img-top">
                <div class="card-body">
                    <h3 class="card-title">${car.brand} ${car.model}</h3>
                    ${priceHTML}
                    <p><strong>Year:</strong> ${car.year}</p>
                    <p><strong>Transmission:</strong> ${car.transmission}</p>
                    <p><strong>Condition:</strong> ${car.car_condition}</p>
                    <p><strong>Engine:</strong> ${car.engine_spec}</p>
                    <p><strong>Color:</strong> ${car.color}</p>
                    <p><strong>Description:</strong> ${car.description}</p>
                    <p><strong>Status:</strong> ${car.status}</p>
                </div>
            `;

            document.getElementById('order-action').innerHTML = `
                <a href="/order/create/${car.car_id}" class="btn btn-primary">Create Order</a>
            `;
        })
        .catch(err => {
            console.error('Error fetching car:', err);
            document.getElementById('car-details').innerHTML = '<p>Failed to load car details</p>';
        });
});
