<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Services\PlaceToPay;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $this->app->bind(PlaceToPay::class, function () {
            return new PlaceToPayFake();
        });

        $response =  $this->post(route('order.pay', $this->product), $this->formData);
        $response->assertStatus(302);
        $response->assertRedirect('http://placetopay.fake/redirection/session/43329/9d81eef79b44bb54f8db409791562683');
        $this->assertDatabaseHas('orders', [
            'product_id' => $this->product->id,
            'customer_name' => $this->formData['customer_name'],
            'customer_email' => $this->formData['customer_email'],
            'customer_mobile' => $this->formData['customer_mobile'],
            'product_quantity' => $this->formData['product_quantity'],
            'status' => Order::STATUS_CREATED,
        ]);

        $order = Order::query()->first();
        $response = $this->get(route('order.detail', $order->code));
        $response->assertViewIs('order.detail');
        $this->assertDatabaseHas('orders', ['status' => Order::STATUS_PAYED]);
        $this->assertDatabaseHas('payments', ['status' => 'APPROVED']);
    }

    /** 
     * @test 
     */
    public function cantPayOrderBecausePaymentIsRejected(): void
    {
        $this->app->bind(PlaceToPay::class, function () {
            $placeToPayFake = new PlaceToPayFake();
            $placeToPayFake->setRedirectInformation([
                'requestId' => 43330,
                'status' => [
                    'status' => 'REJECTED',
                    'reason' => '?C',
                    'message' => 'La petición ha sido cancelada por el usuario',
                    'date' => '2021-08-13T00:16:48-05:00',
                ],
                'request' => [
                    'locale' => 'es_CO',
                    'payer' => [
                        'document' => '123123213',
                        'documentType' => 'CC',
                        'name' => 'andres',
                        'surname' => 'lopez',
                        'email' => 'andre@andres.com',
                        'mobile' => '3008270111',
                    ],
                    'buyer' => [
                        'name' => 'andres',
                        'email' => 'andre@andres.com',
                        'mobile' => '3008270111',
                    ],
                    'payment' => [
                        'reference' => 'c5a3af28b6174c248f91666f8763af01',
                        'description' => 'Pay order fhasion store order',
                        'amount' => [
                            'currency' => 'COP',
                            'total' => 14030,
                        ],
                        'allowPartial' => false,
                        'subscribe' => false,
                    ],
                    'fields' => [
                        0 => [
                            'keyword' => '_processUrl_',
                            'value' => 'https://dev.placetopay.com/redirection/session/43330/de8b3abd29a4b44eec1ea95028677031',
                            'displayOn' => 'none',
                        ],
                    ],
                    'returnUrl' => 'http://my-store.loc:2020/order/detail/c5a3af28b6174c248f91666f8763af01',
                    'ipAddress' => '172.16.238.1',
                    'userAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101 Firefox/91.0',
                    'expiration' => '2021-08-15T05:15:51+00:00',
                    'captureAddress' => false,
                    'skipResult' => false,
                    'noBuyerFill' => false,
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
                        'processorFields' => [
                            0 => [
                                'keyword' => 'merchantCode',
                                'value' => '0010203105',
                                'displayOn' => 'none',
                            ],
                            1 => [
                                'keyword' => 'terminalNumber',
                                'value' => 'ESB19065',
                                'displayOn' => 'none',
                            ],
                            2 => [
                                'keyword' => 'bin',
                                'value' => '400558',
                                'displayOn' => 'none',
                            ],
                            3 => [
                                'keyword' => 'expiration',
                                'value' => '1229',
                                'displayOn' => 'none',
                            ],
                            4 => [
                                'keyword' => 'installments',
                                'value' => 36,
                                'displayOn' => 'none',
                            ],
                            5 => [
                                'keyword' => 'lastDigits',
                                'value' => '0040',
                                'displayOn' => 'none',
                            ],
                        ],
                    ],
                ],
            ]);
            return $placeToPayFake;
        });
        $this->post(route('order.pay', $this->product), $this->formData);
        $order = Order::query()->first();
        $this->get(route('order.detail', $order->code));        
        $this->assertDatabaseHas('orders', ['status' => Order::STATUS_REJECTED]);
        $this->assertDatabaseHas('payments', ['status' => 'REJECTED']);
    }
}
