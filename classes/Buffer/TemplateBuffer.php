<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

declare(strict_types=1);

namespace PrestaShop\Module\PrestashopFacebook\Buffer;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

class TemplateBuffer
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @param string $userId
     */
    public function init($userId)
    {
        $this->session = new Session(
            new MockFileSessionStorage(
                \_PS_CACHE_DIR_ . '/ps_facebook_sessions',
                'pixel'
            )
        );
        $this->session->setId($userId);
        $this->session->start();
        register_shutdown_function([$this, 'save']);
    }

    /**
     * add data to the buffer
     *
     * @param string $data
     *
     * @return void
     */
    public function add($data)
    {
        $this->session->getFlashBag()->add('pixel_events', $data);
    }

    /**
     * reset buffer content
     *
     * @return void
     */
    public function clean()
    {
        $this->session->getFlashBag()->get('pixel_events', []);
    }

    /**
     * return buffer content and reset it
     *
     * @return string
     */
    public function flush()
    {
        $data = '';
        foreach ($this->session->getFlashBag()->get('pixel_events', []) as $message) {
            $data .= $message;
        }

        return $data;
    }

    public function save(): void
    {
        $this->session->save();
    }
}
