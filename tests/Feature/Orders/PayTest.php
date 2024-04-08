<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Services\PlaceToPay;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Fakes\PlaceToPayFake;
use Tests\TestCase;

class PayTest extends TestCase
{

    use RefreshDatabase;

    private Product $product;
    private array $formData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->product = Product::factory()->create();
        $this->formData = [
            'customer_name' => 'Andres',
            'customer_email' => 'andres@andres.com',
            'customer_mobile' => '3103232333',
            'product_quantity' => '2',
        ];
    }

    /**
     * @test
     */
    public function canPayOrderSuccesfullyAndSeeDetail(): void
    {
        $mockResponse = [
            'status' => [
                'status' => 'OK',
                'reason' => 'PC',
                'message' => 'La petición se ha procesado correctamente',
                'date' => '2021-11-30T15:08:27-05:00'
            ],
            'requestId' => 1,
            "processUrl" => "https://checkout-co.placetopay.com/session/1/cc9b8690b1f7228c78b759ce27d7e80a"
        ];

        Http::fake(
            [
                config('services.placetopay.url_base') . 'api/session' => Http::response($mockResponse),
                config('services.placetopay.url_base') . 'api/session/*' => Http::response($this->getInformationMock())
            ]
        );

        $response = $this->postJson(route('order.pay', $this->product), $this->formData); // $this->post(route('order.pay', $this->product), $this->formData);
        $response->assertStatus(302);
        $response->assertRedirect('https://checkout-co.placetopay.com/session/1/cc9b8690b1f7228c78b759ce27d7e80a');
        $this->assertDatabaseHas('orders', [
            'product_id' => $this->product->id,
            'customer_name' => $this->formData['customer_name'],
            'customer_email' => $this->formData['customer_email'],
            'customer_mobile' => $this->formData['customer_mobile'],
            'product_quantity' => $this->formData['product_quantity'],
            'status' => Order::STATUS_CREATED,
        ]);

        Http::fake([config('services.placetopay.url_base') . '*' => Http::response($this->getInformationMock())]);

        $order = Order::query()->first();
        $response = $this->getJson(route('order.detail', $order->code));
        $response->assertViewIs('order.detail');
        $this->assertDatabaseHas('orders', ['status' => Order::STATUS_PAYED]);
        $this->assertDatabaseHas('payments', ['status' => 'APPROVED']);
    }

    /**
     * @test
     */
    public function cantPayOrderBecausePaymentIsRejected(): void
    {
        $data = $this->getInformationMock([

            'status' => [
                'status' => 'REJECTED',
                'reason' => '05',
                'message' => 'Rechazada',
                'date' => '2021-08-13T00:16:34-05:00',
            ],
            'payment' => [
                0 => [
                    'status' => [
                        'status' => 'REJECTED',
                        'reason' => '05',
                        'message' => 'Rechazada',
                        'date' => '2021-08-13T00:16:34-05:00',
                    ],
                    'internalReference' => 15408,
                    'paymentMethod' => 'visa',
                    'paymentMethodName' => 'Visa',
                    'issuerName' => 'CREDIBANCO',
                    'amount' => [
                        'from' => [
                            'currency' => 'COP',
                            'total' => 14030,
                        ],
                        'to' => [
                            'currency' => 'COP',
                            'total' => 14030,
                        ],
                        'factor' => 1,
                    ],
                    'authorization' => '000000',
                    'reference' => 'c5a3af28b6174c248f91666f8763af01',
                    'receipt' => 31794,
                    'franchise' => 'RM_VS',
                    'refunded' => false,
                    'discount' => NULL,
                ],
            ]

        ]);

        $mockResponse = [
            'status' => [
                'status' => 'OK',
                'reason' => 'PC',
                'message' => 'La petición se ha procesado correctamente',
                'date' => '2021-11-30T15:08:27-05:00'
            ],
            'requestId' => 1,
            "processUrl" => "https://checkout-co.placetopay.com/session/1/cc9b8690b1f7228c78b759ce27d7e80a"
        ];

        Http::fake(
            [
                config('services.placetopay.url_base') . 'api/session' => Http::response($mockResponse),
                config('services.placetopay.url_base') . 'api/session/*' => Http::response($data)
            ]
        );

        $this->postJson(route('order.pay', $this->product), $this->formData);
        $order = Order::query()->first();
        $this->get(route('order.detail', $order->code));
        $this->assertDatabaseHas('orders', ['status' => Order::STATUS_REJECTED]);
        $this->assertDatabaseHas('payments', ['status' => 'REJECTED']);
    }

    public function getInformationMock(array $overwritter = [])
    {
        $data = [
            "requestId" => 82858,
            "status" => [
                "status" => "APPROVED",
                "reason" => "00",
                "message" => "La petición ha sido aprobada exitosamente",
                "date" => "2024-04-07T21:00:09-05:00",
            ],
            "request" => [
                "locale" => "es_CO",
                "buyer" => [
                    "name" => "andrss",
                    "email" => "andres@yopmail.com",
                    "mobile" => "3005156705",
                ],
                "payer" => [
                    "document" => "3005156705",
                    "documentType" => "CC",
                    "name" => "andrss",
                    "surname" => "lopez",
                    "email" => "andres@yopmail.com",
                    "mobile" => "+573005156705",
                ],
                "payment" => [
                    "reference" => "e72e2873cc1f431da1d9cb1b57239a1f",
                    "description" => "Pay order fhasion store order",
                    "amount" => ["currency" => "USD", "total" => 33],
                    "allowPartial" => false,
                    "subscribe" => false,
                ],
                "fields" => [
                    [
                        "keyword" => "_processUrl_",
                        "value" =>
                        "https://checkout-co.placetopay.dev/spa/session/82858/e000f9f20bb378e15a5556917044c6f0",
                        "displayOn" => "none",
                    ],
                    ["keyword" => "_session_", "value" => 82858, "displayOn" => "none"],
                ],
                "returnUrl" =>
                "https://store.test/order/detail/e72e2873cc1f431da1d9cb1b57239a1f",
                "ipAddress" => "127.0.0.1",
                "userAgent" =>
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:124.0) Gecko/20100101 Firefox/124.0",
                "expiration" => "2024-04-10T01:53:20+00:00",
            ],
            "payment" => [
                [
                    "amount" => [
                        "to" => ["total" => 33, "currency" => "USD"],
                        "from" => ["total" => 33, "currency" => "USD"],
                        "factor" => 1,
                    ],
                    "status" => [
                        "date" => "2024-04-07T20:53:43-05:00",
                        "reason" => "00",
                        "status" => "APPROVED",
                        "message" => "Aprobada",
                    ],
                    "receipt" => "176349891777",
                    "refunded" => false,
                    "franchise" => "PS_VS",
                    "reference" => "e72e2873cc1f431da1d9cb1b57239a1f",
                    "issuerName" => "JPMORGAN CHASE BANK, N.A.",
                    "authorization" => "900051",
                    "paymentMethod" => "visa",
                    "processorFields" => [
                        [
                            "value" => "4549106521651",
                            "keyword" => "merchantCode",
                            "displayOn" => "none",
                        ],
                        [
                            "value" => "98765432",
                            "keyword" => "terminalNumber",
                            "displayOn" => "none",
                        ],
                        [
                            "value" => "C",
                            "keyword" => "cardType",
                            "displayOn" => "none",
                        ],
                        [
                            "value" => "411111",
                            "keyword" => "bin",
                            "displayOn" => "none",
                        ],
                        [
                            "value" => 1,
                            "keyword" => "installments",
                            "displayOn" => "none",
                        ],
                        [
                            "value" =>
                            "https://store.test/order/detail/e72e2873cc1f431da1d9cb1b57239a1f",
                            "keyword" => "returnUrl",
                            "displayOn" => "none",
                        ],
                        ["value" => true, "keyword" => "onTest", "displayOn" => "none"],
                        [
                            "value" => "1111",
                            "keyword" => "lastDigits",
                            "displayOn" => "none",
                        ],
                        [
                            "value" => "761bd9847ce5a4dbe24938c38ac5de44",
                            "keyword" => "id",
                            "displayOn" => "none",
                        ],
                        ["value" => "00", "keyword" => "b24", "displayOn" => "none"],
                    ],
                    "internalReference" => 433834,
                    "paymentMethodName" => "Visa",
                ],
            ],
            "subscription" => null,
        ];

        return array_replace_recursive($data, $overwritter);
    }
}
