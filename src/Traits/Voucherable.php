<?php declare(strict_types=1);

namespace JTL\Shop5Router\Traits;

use Jtl\OAuth2\Client\Exceptions\InvalidProviderException;
use Jtl\Vouchers\Api\Sdk\Client as VoucherClient;
use Jtl\Vouchers\Api\Sdk\Service as VoucherService;
use League\OAuth2\Client\Token\AccessTokenInterface;
use JTL\Shop5Router\Exceptions\MissingClientException;
use JTL\Shop5Router\Services\Authentication;
use JTL\Shop5Router\Services\Client as ClientService;

/**
 * Trait Voucherable
 * @package JTL\Shop5Router\Traits
 */
trait Voucherable
{
    use Shopable, Pluginable;

    /**
     * @param string|null $token
     * @return VoucherService
     */
    protected function voucherService(?string $token = null): VoucherService
    {
        $conf = [
            'base_uri' => $this->plugin->getConfig()->getValue('base_uri')
        ];
        
        if ($token !== null) {
            $conf['token'] = $token;
        }
        
        // Init Voucher SDK Client
        $client = new VoucherClient($conf);
        
        // Init Voucher SDK Service
        return VoucherService::boot($client);
    }
    
    /**
     * @return ClientService
     */
    protected function clientService(): ClientService
    {
        // Init local Client (Voucher Clients ex. our Shop) Handling Service
        return new ClientService($this->shop());
    }
    
    /**
     * @param ClientService|null $clientService
     * @return Authentication
     * @throws MissingClientException
     */
    protected function authentication(?ClientService $clientService = null): Authentication
    {
        $clientService = $clientService ?? $this->clientService();
        
        return new Authentication(
            $this->shop(),
            $this->plugin(),
            $clientService
        );
    }
    
    /**
     * @return AccessTokenInterface|null
     * @throws MissingClientException
     * @throws InvalidProviderException
     */
    protected function getToken(): ?AccessTokenInterface
    {
        return $this->authentication()->getToken();
    }
}
