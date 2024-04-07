<?php

namespace Tests\Fakes;

use App\Services\PlaceToPay;
use Dnetix\Redirection\Message\RedirectInformation;
use Dnetix\Redirection\Message\RedirectResponse;

class PlaceToPayFake extends PlaceToPay
{
    private RedirectInformation $redirectInformation;

    public function __construct()
    {
        config()->set('services.placetopay.login', 'test');
        config()->set('services.placetopay.key', 'keyTest');
        config()->set('services.placetopay.url_base', 'http://localhost/fake');

        parent::__construct();
    }

    public function request($redirectRequest)
    {

        return new RedirectResponse([
            'status' => [
                'status' => 'OK',
                'reason' => 'PC',
                'message' => 'La petición se ha procesado correctamente',
                'date' => '2021-08-12T23:21:43-05:00',
            ],
            'requestId' => 43329,
            'processUrl' => 'http://placetopay.fake/redirection/session/43329/9d81eef79b44bb54f8db409791562683',
        ]);
    }

    public function query($requestId)
    {           
        return $this->redirectInformation ?? new RedirectInformation([
            'requestId' => 43329,
            'status' => [
                'status' => 'APPROVED',
                'reason' => '00',
                'message' => 'La petición ha sido aprobada exitosamente',
                'date' => '2021-08-12T23:22:32-05:00',
            ],
            'request' => [
                'locale' => 'es_CO',
                'payer' => [
                    'document' => '1231321',
                    'documentType' => 'CC',
                    'name' => 'andres',
                    'surname' => 'lo',
                    'email' => 'andre@andres.com',
                    'mobile' => '3008270111',
                ],
                'buyer' => [
                    'name' => 'andres',
                    'email' => 'andre@andres.com',
                    'mobile' => '3008270111',
                ],
                'payment' => [
                    'reference' => '4dbddf3488c84ac7b8b19def2caaba22',
                    'description' => 'Pay order fhasion store order',
                    'amount' => [
                        'currency' => 'COP',
                        'total' => 10746,
                    ],
                    'allowPartial' => false,
                    'subscribe' => false,
                ],
                'fields' => [
                    0 => [
                        'keyword' => '_processUrl_',
                        'value' => 'https://dev.placetopay.com/redirection/session/43329/9d81eef79b44bb54f8db409791562683',
                        'displayOn' => 'none',
                    ],
                ],
                'returnUrl' => 'http://my-store.loc:2020/order/detail/4dbddf3488c84ac7b8b19def2caaba22',
                'ipAddress' => '172.16.238.1',
                'userAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101 Firefox/91.0',
                'expiration' => '2021-08-15T04:21:53+00:00',
                'captureAddress' => false,
                'skipResult' => false,
                'noBuyerFill' => false,
            ],
            'payment' => [
                0 => [
                    'status' => [
                        'status' => 'APPROVED',
                        'reason' => '00',
                        'message' => 'Aprobada',
                        'date' => '2021-08-12T23:22:27-05:00',
                    ],
                    'internalReference' => 15407,
                    'paymentMethod' => 'visa',
                    'paymentMethodName' => 'Visa',
                    'issuerName' => 'JPMORGAN CHASE BANK, N.A.',
                    'amount' => [
                        'from' => [
                            'currency' => 'COP',
                            'total' => 10746,
                        ],
                        'to' => [
                            'currency' => 'COP',
                            'total' => 10746,
                        ],
                        'factor' => 1,
                    ],
                    'authorization' => '999999',
                    'reference' => '4dbddf3488c84ac7b8b19def2caaba22',
                    'receipt' => 28547,
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
                            'value' => '411111',
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
                            'value' => '1111',
                            'displayOn' => 'none',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function setRedirectInformation(array $redirectInformation): void
    {
        $this->redirectInformation = new RedirectInformation($redirectInformation);
    }
}
