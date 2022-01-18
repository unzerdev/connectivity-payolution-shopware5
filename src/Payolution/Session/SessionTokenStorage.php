<?php

namespace Payolution\Session;

use Enlight_Controller_Front;
use Enlight_Controller_Request_Request;
use PDO;
use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use PolPaymentPayolution\Config\ConfigContext;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class SessionTokenStorage
 *
 * @package Payolution\Session
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class SessionTokenStorage
{
    /**
     * Frontend constant
     *
     * @var string
     */
    const FRONTEND = 'frontend';

    /**
     * Session Token cookie
     *
     * @var string
     */
    const PAYOLUTION_SESSION_COOKIE = 'payolution_session';

    /**
     * @var Enlight_Controller_Front
     */
    private $front;

    /**
     * @var ComponentManagerInterface
     */
    private $componentManager;

    /**
     * @var ConfigContext
     */
    private $configContext;

    /**
     * SessionTokenStorage constructor.
     *
     * @param Enlight_Controller_Front $front
     * @param ComponentManagerInterface $componentManager
     * @param ConfigContext $configContext
     */
    public function __construct(
        Enlight_Controller_Front $front,
        ComponentManagerInterface $componentManager,
        ConfigContext $configContext
    ) {
        $this->front = $front;
        $this->componentManager = $componentManager;
        $this->configContext = $configContext;
    }

    /**
     * Get Token
     *
     * @param null|int $orderId
     *
     * @return null|string
     */
    public function getToken($orderId = null)
    {
        $request = $this->front->Request();
        $module = $request->getModuleName();

        $token = null;

        if ($orderId) {
            $qb = $this->componentManager->getDbalConnection()->createQueryBuilder();
            $qb->from('s_order_attributes', 'o')
                ->select('o.payolution_session_id')
                ->where($qb->expr()->eq('o.orderID', ':orderId'))
                ->setParameter('orderId', $orderId);

            $result = $qb->execute()->fetch(PDO::FETCH_COLUMN);

            $token = $result ?: null;
        }

        if (!$token
            && $module === self::FRONTEND
            && ($session = $this->getSessionString($request))
            && !$request->getCookie(self::PAYOLUTION_SESSION_COOKIE)) {
            $token = $this->generateNewToken($session);
            $this->front->Response()->setCookie(self::PAYOLUTION_SESSION_COOKIE, $token);
        }

        if (!$token
            && $module === self::FRONTEND
            && $this->getSessionString($request)
            && $cookie = $request->getCookie(self::PAYOLUTION_SESSION_COOKIE)) {
            $token = $cookie;
        }

        return $token;
    }

    /**
     * Save Session Token
     *
     * @param int $orderId
     *
     * @return void
     */
    public function saveSessionToken($orderId)
    {
        if ($token = $this->getToken()) {
            $this->componentManager->getDatabase()->executeQuery(
                'UPDATE s_order_attributes SET payolution_session_id = ? WHERE orderID = ?',
                [
                    $token,
                    $orderId
                ]
            );
        }
    }

    /**
     * Generate new Token
     *
     * @param string $session
     *
     * @return string
     */
    private function generateNewToken($session)
    {
        $shop = $this->configContext->getShop();

        $merchantIdentifier = Container::camelize($shop->getHost());

        $hashValue = sha1($session . time());

        return $merchantIdentifier . '_' . $hashValue;
    }

    /**
     * Get Session String
     *
     * @param Enlight_Controller_Request_Request $request
     * @return mixed|null
     */
    public function getSessionString(Enlight_Controller_Request_Request $request)
    {
        $cookieName = 'session-'.  $this->configContext->getShop()->getId();

        return $request->getCookie($cookieName);
    }
}