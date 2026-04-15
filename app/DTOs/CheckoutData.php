<?php

namespace App\DTOs;

class CheckoutData
{
    public array $productIds;

    public array $skus;

    public array $quantities;

    public int $customerAddressId;

    public array $deliveryType;

    public string $paymentMethod;

    public ?string $coupon_code;

    public ?string $order_from;

    public ?string $final_payable_price;

    public ?string $address;

    public ?string $utmSource;

    public ?string $utmMedium;

    public ?string $utmCampaign;

    public ?string $utmTerm;

    public ?string $utmContent;

    public ?string $utmId;


    public static function fromArray(array $data): self
    {
        $dto                      = new self;
        $dto->productIds          = $data['product_id'];
        $dto->skus                = $data['sku'] ?? [];
        $dto->quantities          = $data['quantity'];
        $dto->customerAddressId   = $data['customer_address_id'] ?? null;
        $dto->deliveryType        = $data['delivery_type'];
        $dto->paymentMethod       = $data['payment_method'];
        $dto->coupon_code         = $data['coupon_code']         ?? null;
        $dto->order_from          = $data['order_from']          ?? '1'; // default 1 = app
        $dto->final_payable_price = $data['final_payable_price'] ?? '1'; // default 1 = app
        $dto->address             = $data['address'] ?? '';


        $dto->utmSource           = $data['utm_source'] ?? '';
        $dto->utmMedium           = $data['utm_medium'] ?? '';
        $dto->utmCampaign         = $data['utm_campaign'] ?? '';
        $dto->utmTerm             = $data['utm_term'] ?? '';
        $dto->utmContent          = $data['utm_content'] ?? '';
        $dto->utmId               = $data['utm_id'] ?? '';


        return $dto;
    }
}
