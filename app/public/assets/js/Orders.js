document.addEventListener('DOMContentLoaded', () => {
    fetchOrders();
});

function fetchOrders() {
    fetch('/api/orders')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('ordersContainer');
            container.innerHTML = '';

            data.forEach(order => {
                const card = document.createElement('div');
                card.className = 'col-md-6 col-lg-4';

                card.innerHTML = `
                    <div class="card p-3">
                        <div class="card-body">
                            <h5 class="card-title mb-1">Order #${order.order_id}</h5>
                            <p class="text-muted mb-2">
                              <strong>Car:</strong> ${order.car_brand} ${order.car_model}
                            </p>

                            <p class="card-text">
                                <strong>Client:</strong> ${order.client_name}<br>
                                <strong>Email:</strong> ${order.client_email}<br>
                                <strong>Phone:</strong> ${order.client_phone}<br><br>
                                <strong>Order Type:</strong> ${order.order_type}<br>
                                <strong>Down Payment:</strong> €${parseFloat(order.down_payment).toFixed(2)}<br>
                                <strong>Employee ID:</strong> ${order.employee_id ?? '—'}<br>
                                <strong>Created:</strong> ${order.created_at}<br>
                                <strong>Updated:</strong> ${order.updated_at}
                            </p>

                            <label class="form-label fw-bold mt-3">Status:</label>
                            <select class="form-select status-select" data-id="${order.order_id}">
                                ${generateStatusOptions(order.status)}
                            </select>
                            <button class="btn btn-primary btn-save" data-id="${order.order_id}">Save</button>
                        </div>
                    </div>
                `;

                container.appendChild(card);
            });

            attachSaveListeners();
        })
        .catch(err => console.error('Error fetching orders:', err));
}

function generateStatusOptions(currentStatus) {
    const options = ['pending', 'approved', 'denied', 'completed'];
    return options.map(status =>
        `<option value="${status}" ${status === currentStatus ? 'selected' : ''}>${status}</option>`
    ).join('');
}

function attachSaveListeners() {
    document.querySelectorAll('.btn-save').forEach(button => {
        button.addEventListener('click', () => {
            const orderId = button.getAttribute('data-id');
            const select = document.querySelector(`.status-select[data-id="${orderId}"]`);
            const newStatus = select.value;

            fetch('/api/orders/status', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    order_id: parseInt(orderId),
                    status: newStatus
                })
            })
            .then(res => res.json())
            .then(response => {
                alert(`Order #${orderId} updated to "${newStatus}"`);
            })
            .catch(err => {
                alert(`Error updating order status: ${err}`);
            });
        });
    });
}
