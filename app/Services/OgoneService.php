<?php

namespace App\Services;

use Illuminate\Routing\UrlGenerator;
use OnlinePayments\Sdk\DefaultConnection;
use OnlinePayments\Sdk\Client;
use OnlinePayments\Sdk\Communicator;
use OnlinePayments\Sdk\CommunicatorConfiguration;
use OnlinePayments\Sdk\Domain\CreateHostedCheckoutRequest;
use OnlinePayments\Sdk\Domain\HostedCheckoutSpecificInput;
use OnlinePayments\Sdk\Domain\CardPaymentMethodSpecificInputBase;
use OnlinePayments\Sdk\Domain\ThreeDSecureBase;
use OnlinePayments\Sdk\Domain\Order;
use OnlinePayments\Sdk\Domain\AmountOfMoney;

/**
 * Class OgoneService
 * @package App\Services
 */
class OgoneService
{
    private $apiUrl;
    private $apiKey;
    private $apiSecret;

    /**
     * OgoneService constructor.
     */
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->apiUrl = env('OGONE_API_URL');
        $this->pspid = env('OGONE_PSPID');
        $this->apiKey = env('OGONE_API_KEY');
        $this->apiSecret = env('OGONE_API_SECRET');
        $this->templateName = env('OGONE_TEMPLATE_NAME');
        $this->urlGenerator = $urlGenerator;
        $this->setConnection();
    }

    /**
     * Permet d'établir une connection à Ogone
     */
    public function setConnection()
    {
        $connection = new DefaultConnection();

        // Your PSPID in either our test or live environment
        $merchantId = $this->pspid;

        // Put the value of the API Key which you can find on the Back Office page
        // https://secure.ogone.com/Ncol/Test/Backoffice/login/
        $apiKey = $this->apiKey;

        // Put the value of the API Secret which you can find on the Back Office page
        // https://secure.ogone.com/Ncol/Prod/BackOffice/login/
        $apiSecret = $this->apiSecret;

        // This endpoint is pointing to the TEST server 
        // Note: Use the endpoint without the /v2/ part here
        $apiEndpoint = $this->apiUrl;
        
        // Additional settings to easily identify your company in our logs.
        $integrator = 'ROOM4ME';
        $proxyConfiguration = null;
        $communicatorConfiguration = new CommunicatorConfiguration(
            $apiKey,
            $apiSecret,
            $apiEndpoint,
            $integrator,
            $proxyConfiguration
        );

        $communicator = new Communicator($connection, $communicatorConfiguration);

        $client = new Client($communicator);

        $this->merchantClient = $client->merchant($merchantId);
    }

    public function getPayment($paymentID)
    {
        try {
            return $this->merchantClient
            ->payments()
            ->getPayment($paymentID);
        } catch (\Throwable $th) {
            return null;
        }
    }

    /*
    * The HostedCheckoutClient object based on the MerchantClient
    * object created during initialisation 
    */



    
    ///////////////////////////////////////////////////////////////
    // ce code pour payement 
    public function getHostedCheckoutPage($total)
    {
        $hostedCheckoutClient = $this->merchantClient->hostedCheckout();
        $createHostedCheckoutRequest = new CreateHostedCheckoutRequest();
        $hostedCheckoutSpecificInput = new HostedCheckoutSpecificInput();
        $hostedCheckoutSpecificInput->setVariant($this->templateName);
        $hostedCheckoutSpecificInput->setLocale(app()->getLocale());
        $hostedCheckoutSpecificInput->setReturnUrl(route('payment.return'));
        $createHostedCheckoutRequest->setHostedCheckoutSpecificInput($hostedCheckoutSpecificInput);
        $cardPaymentMethodSpecificInputBase = new CardPaymentMethodSpecificInputBase();
        $cardPaymentMethodSpecificInputBase->setAuthorizationMode('SALE');
        /*$threeDSecureBase = new ThreeDSecureBase();
        $cardPaymentMethodSpecificInputBase->setThreeDSecure($threeDSecureBase);*/
        $createHostedCheckoutRequest->setCardPaymentMethodSpecificInput($cardPaymentMethodSpecificInputBase);
        $order = new Order();

        // Example object of the AmountOfMoney
        $amountOfMoney = new AmountOfMoney();
        $amountOfMoney->setCurrencyCode("EUR");
        $amountOfMoney->setAmount($total * 100);
        $order->setAmountOfMoney($amountOfMoney);
        $createHostedCheckoutRequest->setOrder($order);
        // Get the response for the HostedCheckoutClient
        $hostedCheckoutPage = $hostedCheckoutClient->createHostedCheckout($createHostedCheckoutRequest);
        return $hostedCheckoutPage;
    }
}

