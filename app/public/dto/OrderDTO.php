<?php
// app/public/dto/OrderDTO.php

class OrderDTO {
    private $order_id;
    private $car_id;
    private $order_type;
    private $status;
    private $down_payment;
    private $client_name;
    private $client_email;
    private $client_phone;
    private $employee_id;
    private $created_at;
    private $updated_at;

    public function __construct(
        $order_id,
        $car_id,
        $order_type,
        $status,
        $down_payment,
        $client_name,
        $client_email,
        $client_phone,
        $employee_id,
        $created_at,
        $updated_at
    ) {
        $this->order_id       = $order_id;
        $this->car_id         = $car_id;
        $this->order_type     = $order_type;
        $this->status         = $status;
        $this->down_payment   = $down_payment;
        $this->client_name    = $client_name;
        $this->client_email   = $client_email;
        $this->client_phone   = $client_phone;
        $this->employee_id    = $employee_id;
        $this->created_at     = $created_at;
        $this->updated_at     = $updated_at;
    }

    public function getOrderId() {
        return $this->order_id;
    }

    public function getCarId() {
        return $this->car_id;
    }

    public function getOrderType() {
        return $this->order_type;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getDownPayment() {
        return $this->down_payment;
    }

    public function getClientName() {
        return $this->client_name;
    }

    public function getClientEmail() {
        return $this->client_email;
    }

    public function getClientPhone() {
        return $this->client_phone;
    }

    public function getEmployeeId() {
        return $this->employee_id;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }
}
?>
